<?php
/**
* Project: Modena
* API
* @copyright Alphacode BR - https://github.com/AlphacodeBR
* @author Rafael Franco <rafael@alphacode.com.br>
* @author Felipe Soares <felipe@alphacode.com.br>
* @author Brendon Bitencourt <brendon@alphacode.com.br>
* @author Lucas Silva <lucassilva@alphacode.com.br>
*/

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include "../modules/aws-autoloader.php";
use Aws\Sns\SnsClient;

class api extends simplePHP {

    #initialize vars
    private $model;
    private $core;
    private $file;
    private $arn_base;
    private $arn_app;

    /**
    *   Construtor
    **/
    public function __construct() {
        global $keys;

        #load model module
        $this->model = $this->loadModule("model");
        $this->model->context = false;

        #load core module
        $this->core = $this->loadModule("core","",true);
        $this->xml = $this->loadModule("xml");
        $this->file = $this->loadModule("file");
        $this->email = $this->loadModule("email");
        #load module for validations
        $this->validator = $this->loadModule("validator", "");
        #load image module
        $this->image = $this->loadModule("image", "");


        $_SESSION["conta_id"] = 1;

        unset($_REQUEST["PHPSESSID"]);
    }

    public function apiReturn($status, $mensagem = "", $data = "") {

        $retorno["status"] = $status;
        $retorno["mensagem"] = $mensagem;
        $retorno["data"] = $data;

        echo json_encode($retorno);
        exit;
    }


    public function _actionBuscaEstabelecimento(){
        $coords = json_decode($_REQUEST['objeto'],true);
        $limit['start'] = 0;
        $limit['limit'] = 10;
        if($coords){
            $sqlDistancia = ",round( (6371 * acos(
                cos( radians(".$coords['latitude'].") )
                * cos( radians( a.latitude ) )
                * cos( radians( a.longitude ) - radians(".$coords['longitude'].") )
                + sin( radians(".$coords['latitude'].") )
                * sin( radians( a.latitude ) ) 
                ) )
                ) AS distancia  ";
            $sqlOrder= "distancia desc,rand()";
        }else{
            $sqlDistancia = "";
            $sqlOrder= "rand()";
        }

        $restaurantes = $this->model->getData('estabelecimento','a.*'.$sqlDistancia,array("status" => "ativo", "tipo" => "restaurante"),$limit,$sqlOrder);
        if($restaurantes[0]['result'] != "empty") $res['restaurantes'] = $restaurantes;

        $hoteis = $this->model->getData('estabelecimento','a.*'.$sqlDistancia,array("status" => "ativo", "tipo" => "hotel"),$limit,$sqlOrder);
        if($hoteis[0]['result'] != "empty") $res['hoteis'] = $hoteis;

        $eventos = $this->model->getData('estabelecimento','a.*'.$sqlDistancia,array("status" => "ativo", "tipo" => "evento"),$limit,$sqlOrder);
        if($eventos[0]['result'] != "empty") $res['eventos'] = $eventos;

        $filmes = $this->model->getData('estabelecimento','a.*'.$sqlDistancia,array("status" => "ativo","tipo" => "cinema"),$limit,$sqlOrder);
        if($filmes[0]['result'] != "empty") $res['filmes'] = $filmes;
        
        $this->apiReturn("sucesso","",$res);
    }

    public function _actionCadastrarCliente(){

        $data = $_REQUEST;
        $data["email"] = trim($_REQUEST["email"]);
        $data["senha"] = md5(trim($_REQUEST["senha"]));

        $res = $this->model->getData("clientes", "a.id", array("email"=>$data["email"]));

        if ($res[0]["result"] != "empty") {
            $this->apiReturn("error","E-mail já cadastrado, tente novamente com outro e-mail ou faça login.");
        }
        $res = $this->model->getData("clientes", "a.id", array("cpf"=>$data["cpf"]));
        if ($res[0]["result"] != "empty") {
            $this->apiReturn("error","CPF já cadastrado, tente novamente com outro cpf ou faça login.");
        }

        $cliente_id = $this->model->addData("clientes", $data, true);

        if ($cliente_id["status"] == "erro") {
            $this->apiReturn("error","Não foi possível concluir o cadastro. Tente novamente.");
        } else {
            $data["id"] = $cliente_id;
            $this->apiReturn("success","Cadastro realizado com sucesso.", $data);
        }


    }

    public function _actionEsqueciSenha() {

        $data = $_REQUEST;

        $cliente = $this->model->getData('clientes','a.id, a.email, a.nome', array('email'=>$data['email']));

        if ($cliente[0]['result'] == 'empty') {
            $this->apiReturn('error','O e-mail informado não está cadastrado.');
        } else {

            for($i=0; $i<5; $i++){
                $code .= "".mt_rand(0, 9);
            }

            $nome = explode(' ', $cliente[0]['nome']);
            $senha = substr(md5(rand(0,10000)),0,6);
            $content = array("nome"=>$nome[0], "senha"=>$senha, "data"=>Date("d/m/Y h:i"));
            $template = $this->includeHTML("../view/emails/esqueci_senha.html");

            $this->model->alterData('clientes',array('senha'=>md5($senha)),array('email'=>$data['email']));

            $enviou = $this->email->send($data['email'], CONTACT_EMAIL, utf8_encode("[Modena] Recuperação de senha"), $template, $content, "Modena");

            if ($enviou == 1) {

                $recovery = array('data_recovery' => date('Y-m-d h:i:s'), 'cliente_id' => $cliente[0]['id'], 'code' => $code, 'status' => 'Ativo');
                $add = $this->model->addData('recovery_pass', $recovery, true);

                $this->apiReturn('success','E-mail enviado com sucesso', $code);
            } else {
                $this->apiReturn('error','Ocorreu um erro ao enviar seu e-mail. Tente novamente.');
            }
        }
    }


    public function _actionAtualizarCliente(){

        $data = $_REQUEST;
        if($_REQUEST['senha'] != '') {
          $data['senha'] = md5(trim($_REQUEST['senha']));
        }

        $res = $this->model->alterData('clientes', $data, array('id' => $_REQUEST['id']));

        if ($res['status'] == 'erro') {
            $this->apiReturn('error','Não foi possível atualizar dos dados. Tente novamente.');
        } else {
            unset($_REQUEST['senha'], $_REQUEST['senha_atual']);
            $this->apiReturn('success','Dados atualizados com sucesso', $_REQUEST);
        }
    }

    public function _actionRedefinirSenha() {

        $data = $_REQUEST;
        $senha = md5(trim($data['senha']));

        $res = $this->model->alterData('clientes', array('senha'=>$senha), array('id' => $data['id']));

        if ($res['status'] == 'erro') {
            $this->apiReturn('error','Não foi possível atualizar dos dados. Solicite novamente.');
        } else {
            $this->apiReturn('success','Dados atualizados com sucesso');
        }

    }
    public function _actionEmpreendimentos() {
      $res = $this->model->getData('empreendimentos','id,nome,metragens,dormitorios,slogan,conceito,status,latitude,longitude,situacao,cep,endereco,numero,bairro,cidade,estado',array('status'=>'Ativo'));
      foreach ($res as $empreendimento) {
        $empreendimento['multimidia'] = $this->model->getData('multimidia','conceito,galeria,destaque,tipo,url,legenda,foto,categoria',array('empreendimento_id'=>$empreendimento['id']));

      $empreendimento['caracteristicas'] = $this->model->getData('caracteristicas','nome,valor',array('empreendimento_id'=>$empreendimento['id']));
       $empreendimento['conceito'] = nl2br($empreendimento['conceito']);

        $empreendimentos[$empreendimento['id']] = $empreendimento;
      }
      $this->apiReturn('success','',$empreendimentos);
     return $this->keys;
   }

   public function _actionEntregas() {

     $res = $this->model->getData('entregas','a.*,e.nome',array('empreendimento_id'=>$_REQUEST['empreendimento_id']),'','a.id desc','inner join empreendimentos as e on e.id = a.empreendimento_id');

    foreach ($res as $key => $entrega) {
      $entrega['imagens'] = $this->model->getData('imagens_entrega','id,arquivo',array('entrega_id'=>$entrega['id']));
      $entregas[] = $entrega;
    }

    $this->apiReturn('success','',$entregas);
   }

   public function _actionMensagem() {
      $chat = $this->model->getData('chats','id',array('cliente_id'=>$_REQUEST['cliente_id']));
      if($chat[0]['result'] == 'empty') {
        $data = $_POST;
        $data['ultima_mensagem'] = $_POST['mensagem'];
        unset($data['mensagem']);
        $data['data_e_hora'] = date('d/m/Y H:i');

        $chat_id = $this->model->addData('chats',$data,true);
      } else {
        $chat_id = $chat[0]['id'];
      }
      $mensagem = $_POST;
      $mensagem['texto'] = $_POST['mensagem'];
      $mensagem['chat_id'] = $chat_id;
      unset($mensagem['mensagem']);
      unset($mensagem['ultima_mensagem']);
      $this->model->addData('mensagens',$mensagem,true);

      return $this->keys;
  }

  public function _actionChat() {
    $chat = $this->model->getData('chats','id',array('cliente_id'=>$_REQUEST['cliente_id']));
    if($chat[0]['result'] == 'empty') {
      $data = $_POST;
      $data['ultima_mensagem'] = $_POST['mensagem'];
      unset($data['mensagem']);
      $data['data_e_hora'] = date('d/m/Y H:i');

      $chat[0]['id'] = $this->model->addData('chats',$data,true);
    } 
    $res = $this->model->getData('mensagens','texto,UNIX_TIMESTAMP(time) as time,autor',array('chat_id'=>$chat[0]['id']),'','a.id asc');
    foreach($res as $msg) {
      $msg['data'] = date('d/m/Y H:i',$msg['time']);
      $mensagens[] = $msg;
    }
    $this->apiReturn('sucess','',$mensagens);
 }


 public function _actionGravaaparelho() {
      $client = SnsClient::factory(array(
          "region" => "us-east-1",
          "key" => "AKIAI5C3ZUUWXZM5YYHQ",
          "secret" => "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"
      ));


      if(strlen($_REQUEST["aparelho"]) == 64) {
        // $response = $client->CreatePlatformEndpoint(array(
        //   "PlatformApplicationArn"=> PUSH_URL."app/APNS".APNS,
        //   "Token"=> $_REQUEST["aparelho"]));

        $response = $client->CreatePlatformEndpoint(array(
          "PlatformApplicationArn"=> PUSH_URL."app/APNS/".APNS,
          "Token"=> $_REQUEST["aparelho"]));
      }else{
        $response = $client->CreatePlatformEndpoint(array(
          "PlatformApplicationArn"=> PUSH_URL."app/GCM/".APNS."GCM",
          "Token"=> $_REQUEST["aparelho"]));
      }

      $endpoint = $response["EndpointArn"];

      $this->core->subscribe(APNS,$endpoint);

      $this->model->alterData("clientes",array("aparelho"=>$_REQUEST["aparelho"],"endpoint"=>$endpoint),array("id"=>$_REQUEST["cliente_id"]));


      $retorno["status"] = "sucesso";

      echo json_encode($retorno);
      exit;
    }

    public function _actionPoliview() {

     echo file_get_contents($_REQUEST['url']);
     exit;
   }

}

?>
