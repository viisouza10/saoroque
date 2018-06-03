<?php
/**
* Project: UrbanMobi
*
* @copyright Rafael Franco - https://github.com/rafaelfranco
* @author Rafael Franco <rafael@alphacode.com.br>
*/

include "../modules/aws-autoloader.php";
use Aws\Sns\SnsClient;

class core extends simplePHP {

    private $model;
    private $html;

    #initialize vars
    public function __construct() {
        $this->model = $this->loadModule("model");
        $this->html = $this->loadModule("html");

        #load validator module
        $this->validator = $this->loadModule("validator", "");

    }

    public function subscribe($topico,$endpoint) {

        $client = SnsClient::factory(array(
            "region" => "us-east-1",
            "key" => "AKIAI5C3ZUUWXZM5YYHQ",
            "secret" => "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"
        ));


        $client->subscribe(array(
            'TopicArn' => PUSH_URL .$topico,
            'Protocol' => 'application',
            'Endpoint' => $endpoint
        ));
    }

    /**
    * getEnabledModules function
    * @return array
    **/
    public function getEnabledModules() {

        #get modules enables for this kind of user
        $modules = $this->model->getData("regra_acesso","*,m.id as modulo_id", array("tipo_usuario"=>$_SESSION["tipo"]),"","a.ID DESC","INNER JOIN modulo AS m ON m.id = a.modulo");

        foreach ($modules as $module) {

            unset($module["id"]);
            $module["tag"] = str_replace(" ", "-", $module["nome"]);
            $return[] = $module;

        }
        return $return;
    }

    /**
    *  isLogged
    * @return boolean
    * */
    public function isLogged() {

        if(empty($_SESSION["usuario_id"])) {
            return false;
        } else {
            return true;
        }
    }

    public function calculaTempo($tempo){
        $minutos = intval((time() - $tempo)/60);
        if($minutos <= 60) {
            return $minutos ." m";
        }
        $horas = intval($minutos/60);
        if($horas <= 24) {
            return $horas ." h";
        }
        $dias = intval($horas/24);
        if($dias <= 7) {
            return $dias ." d";
        }

        $semanas = intval($dias/7);
        if($semanas <= 4) {
            return $semanas ." s";
        }

        $meses = intval($dias/30);
        if($meses <= 12) {
            return $meses ." m";
        }

        $anos = intval($dias/365);
        return $anos ." a";

    }

    public function loadMenu() {

        $lista = substr($_SESSION["permissoes"], 0,strlen($_SESSION["permissoes"])-1);

        #carrega os módulos disponiveis pra essa conta
        $modulos = $this->model->getData("adm_modulos","*",array("in id"=>"($lista)"),"","a.ordem asc");

        $menu = "";
        foreach ($modulos as $modulo) {
            //verifica se o modulo esta disponivel para esse usuario
            $path = str_replace(" ", "-", $modulo["nome"]);
            if($modulo["status"] == 1) {
                $label = ucwords($modulo["label"]);
                $icone = $modulo["icone"];

                $menu .= '<li id="menu-'.$modulo['nome'].'" class="[#active'.trim($modulo['nome']).'#]">
                <a href="/'.$path.'"  >
                    '.$icone.'
                    <span>'.$label.'</span>
                </a>
                </li>';
            }
        }
        return $menu;
    }

    private function loadSubMenu($modulo_id, $path) {
         $submenu = '<ul id="'.$path.'" class="collapse">
        <li>
        <a href="#">Listar </a>
        <a href="#">Inserir </a>
        </li>
        </ul>';
        return $submenu;
    }

    public function returnTimeStamp($data){

        if ($data != ""){
            $data = str_replace("_", "/", $data);
            $data = explode("/", $data);
            $timestamp = date(mktime(0, 0, 0, $data[1], $data[0], $data[2]));
        } else {
            $timestamp = "";
        }
        return $timestamp;
    }

    public function limitarTexto($texto, $limite = 100){

        $contador = mb_strlen($texto);
        if ( $contador >= $limite ) {
            $texto = mb_substr($texto, 0, mb_strrpos(mb_substr($texto, 0, $limite), " "), "UTF-8") . "...";
            return $texto;
        } else{
            return $texto;
        }
    }

    public function removeMask($string){
        $mascaraRemovida = str_replace("(", "", str_replace(")", "", str_replace("-", "", str_replace("/", "",
        str_replace(".", "", str_replace(" ", "", $string))))));

        return $mascaraRemovida;
    }

    /* Retorna float */
    public function removeMaskMoney($string){
        $mascaraRemovida = str_replace("R$", "", str_replace(".", "", $string));
        $mascaraRemovida = str_replace(",", ".", str_replace(" ", "", $mascaraRemovida));

        return $mascaraRemovida;
    }

    public function imageDriver($image = null){
        if ($image){
            $url_image = $image;
            if ($this->validator->is_url_exist($url_image)){
                return $url_image;
            } else {
                return "http://".$_SERVER["HTTP_HOST"]."/uploads/motorista/default.jpg";
            }
        } else {
            return "http://".$_SERVER["HTTP_HOST"]."/uploads/motorista/default.jpg";
        }
    }

    public function mask($val, $mask){
        $maskared = "";
        $k = 0;

        for($i = 0; $i<=strlen($mask)-1; $i++){
            if($mask[$i] == "#"){
                if(isset($val[$k])){
                    $maskared .= $val[$k++];
                }
            } else {
                if(isset($mask[$i])){
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    public function preparaData($data){
        $data = date("d/m/Y", $data);

        if ($data){
            return $data;
        } else {
            return "";
        }
    }
    public function preparaDataParaConsulta($data, $final = false){
        $array_data = explode("_", $data);

        if ($final){
            $timestamp = date(mktime(23, 59, 59, $array_data[1], $array_data[0], $array_data[2]));
        } else {
            $timestamp = date(mktime(0, 0, 0, $array_data[1], $array_data[0], $array_data[2]));
        }

        return $timestamp;
    }

    public function getLabelStatus($status){
        if ($status == 1){
            return "Ativo";
        } else {
            return "Inativo";
        }
    }

    public function formatDateTime($date){
        $date = explode(" ", $date);

        $date = explode("-", $date[0]);
        $date_fomatada = $date[2]."/".$date[1]."/".$date[0];

        return $date_fomatada;
    }

    public function push($titulo, $mensagem, $usuario_id = '', $topico = '', $parametros = '') {

        if($usuario_id != ''){
            $usuario = $this->model->getData('clientes', 'aparelho, endpoint', array('id'=>$usuario_id));
            $token = $usuario[0]['aparelho'];
            $endpoint = $usuario[0]['endpoint'];
        }

        $client = SnsClient::factory(array(
            'region' => 'us-east-1',
            'key' => 'AKIAI5C3ZUUWXZM5YYHQ',
            'secret' => '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'
        ));

        if ($topico) {
            // PUSH PARA O TOPICO
            $message = array(
                'TopicArn' => PUSH_URL.$topico,
                'MessageStructure' => 'json',
                'Message' =>
                '{
                    "default": "{\"aps\":{\"alert\": \"'.$mensagem.'\",\"badge\" : 0,\"title\" : \"'.$titulo.'\",\"sound\" :\"beep.wav\"}, \"status\": \"'.$parametros.'\" }",
                    "APNS_SANDBOX": "{\"aps\":{\"alert\": \"'.$mensagem.'\",\"badge\" : 0,\"title\" : \"'.$titulo.'\",\"sound\" :\"beep.wav\", \"status\": \"'.$parametros.'\"} }",
                    "APNS": "{\"aps\":{\"alert\": \"'.$mensagem.'\",\"badge\" : 0,\"title\" : \"'.$titulo.'\",\"sound\" :\"beep.wav\", \"status\": \"'.$parametros.'\"} }",
                    "GCM": "{ \"data\": { \"title\": \"'.$titulo.'\", \"message\": \"'.$mensagem.'\", \"status\": \"'.$parametros.'\" } }"
                }'
            );
            try {
                $client->publish($message);
            } catch (Exception $e) {
                $error = $e->getMessage();
                return false;
            }
        }
        else {
            $message = array(
                'TargetArn' => $endpoint,
                'MessageStructure' => 'json',
                'Message' =>
                '{
                    "default": "{\"aps\":{\"alert\": \"'.$mensagem.'\",\"badge\" : 0,\"title\" : \"'.$titulo.'\",\"sound\" :\"beep.wav\"}, \"status\": \"'.$parametros.'\" }",
                    "APNS_SANDBOX": "{\"aps\":{\"alert\": \"'.$mensagem.'\",\"badge\" : 0,\"title\" : \"'.$titulo.'\",\"sound\" :\"beep.wav\", \"status\": \"'.$parametros.'\" }}",
                    "APNS": "{\"aps\":{\"alert\": \"'.$mensagem.'\",\"badge\" : 0,\"title\" : \"'.$titulo.'\",\"sound\" :\"beep.wav\", \"status\": \"'.$parametros.'\"} }",
                    "GCM": "{ \"data\": { \"title\": \"'.$titulo.'\", \"message\": \"'.$mensagem.'\", \"status\": \"'.$parametros.'\" } }"
                }'
            );
            try {
                $client->publish($message);
            } catch (Exception $e) {
                $error = $e->getMessage();
                return false;
            }
        }
        return false;
    }

    /**
    * mandaEmail
    * $tipo: tabela do cadastro
    * @return boolean
    **/
    public function sendEmail($assunto, $cliente_id, $template = "", $mensagem = "", $chaves = "", $email = "", $tipo = "cliente") {

        //caso não receba o email, busca na tabela desejada
        if ($email == "") {
          $cliente = $this->model->getData($tipo,"email",array("id"=>$cliente_id));
          $email = $cliente[0]["email"];
        }

        // verifica se é template ou mensagem
        if ($template == "") {
            $html = $mensagem;
        } else {
            $template = file_get_contents("../view/emails/".$template.".html");
        }

        // substitui keys se necessário
        if ($chaves != "") {
            $html = $this->applyKeys($html,$chaves);
        }

        include_once("../public/modules/phpmailer/class.phpmailer.php");
        include_once("../public/modules/phpmailer/class.smtp.php");

        $senderMail = CONTACT_EMAIL;
        $Host = "smtp.mandrillapp.com";
        $Username = "rafaelfranco@me.com";
        $Password = "-Q38_6lflM4oKcwMrXh3fg";
        $Port = "587";
        $mail = new PHPMailer();

        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->CharSet = "UTF-8"; // UTF-8
        $mail->Host = $Host; // SMTP server
        $mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Port = $Port; // set the SMTP port for the service server
        $mail->Username = $Username; // account username
        $mail->Password = $Password; // account password
        $mail->SetFrom($senderMail, "AlphaMarket");
        $mail->Subject = ("[AlphaMarket] ".$assunto);

        $mail->MsgHTML($html);
        $mail->AddAddress($email);

        return $mail->Send();
    }

    public function geraCodigoUnico($salt, $length=16){
        $salt = md5($salt);
        $len = strlen($salt);
        $code = "";

        mt_srand(10000000*(double)microtime());
        for ($i = 0; $i < $length; $i++){
            $code .= $salt[mt_rand(0,$len - 1)];
        }

        return $code;
    }

    // Função que irá redimensionar nossa imagem
    public function redimensionar($caminho, $nomearquivo, $width, $height){
        // Determina as novas dimensões
        $width = 300;
        $height = 300;

        // Pegamos a largura e altura originais, além do tipo de imagem
        list($width_orig, $height_orig, $tipo, $atributo) = getimagesize($caminho.$nomearquivo);

        // Se largura é maior que altura, dividimos a largura determinada pela original e multiplicamos a altura pelo resultado, para manter a proporção da imagem
        if($width_orig > $height_orig){
            $height = ($width/$width_orig)*$height_orig;
            // Se altura é maior que largura, dividimos a altura determinada pela original e multiplicamos a largura pelo resultado, para manter a proporção da imagem
        } elseif($width_orig < $height_orig) {
            $width = ($height/$height_orig)*$width_orig;
        } // -> fim if
        // Criando a imagem com o novo tamanho
        $novaimagem = imagecreatetruecolor($width, $height);
        switch($tipo){

            // Se o tipo da imagem for gif
            case 1:
            // Obtém a imagem gif original
            $origem = imagecreatefromgif($caminho.$nomearquivo);
            // Copia a imagem original para a imagem com novo tamanho
            imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            // Envia a nova imagem gif para o lugar da antiga
            imagegif($novaimagem, $caminho.$nomearquivo);
            break;

            // Se o tipo da imagem for jpg
            case 2:
            // Obtém a imagem jpg original
            $origem = imagecreatefromjpeg($caminho.$nomearquivo);
            // Copia a imagem original para a imagem com novo tamanho
            imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            // Envia a nova imagem jpg para o lugar da antiga
            imagejpeg($novaimagem, $caminho.$nomearquivo);
            break;

            // Se o tipo da imagem for png
            case 3:
            // Obtém a imagem png original
            $origem = imagecreatefrompng($caminho.$nomearquivo);
            // Copia a imagem original para a imagem com novo tamanho
            imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
            // Envia a nova imagem png para o lugar da antiga
            imagepng($novaimagem, $caminho.$nomearquivo);
            break;
        } // -> fim switch

        // Destrói a imagem nova criada e já salva no lugar da original
        imagedestroy($novaimagem);
        // Destrói a cópia de nossa imagem original
        imagedestroy($origem);
    } // -> fim function redimensionar()

    public function saveDate($date){

        $date = str_replace("/", "-", $date);
        $date = date("Y-m-d", strtotime($date));
        return $date;
    }

    public function trataDoc($files){

        $traseira = $dut = array();
        $c = 0;
		foreach ($files as $key => $value) {
			$traseira[$key][$c] = $value[1]["foto_traseira"];
			$dut[$key][$c] = $value[1]["foto_dut"];
            $c++;
		}
        $documentos[] = $traseira;
        $documentos[] = $dut;
        return $documentos;
    }

    public function tableHours($horario = null){
        if(!empty($horario)){
            foreach ($horario as $key => $value) {
                if($value != ""){
                    $horas[$key] = date("H:i", $value);
                } else {
                    $horas[$key] = date("H:i", "");
                }
            }
        }

        $lista[0][1] = "Dias da semana";
        $lista[0][2] = "Horário de abertura";
        $lista[0][3] = "Horário de fechamento";

        $lista[1][1] = "Segunda-feira";
        $lista[1][2] = $this->html->input("time", $horas["segunda_inicio"], array("class"=>"form-control", "name"=>"horario[segunda][inicio]"));
        $lista[1][3] = $this->html->input("time", $horas["segunda_fim"], array("class"=>"form-control", "name"=>"horario[segunda][fim]"));

        $lista[2][1] = "Terça-feira";
        $lista[2][2] = $this->html->input("time", $horas["terca_inicio"], array("class"=>"form-control", "name"=>"horario[terca][inicio]"));
        $lista[2][3] = $this->html->input("time", $horas["terca_fim"], array("class"=>"form-control", "name"=>"horario[terca][fim]"));

        $lista[3][1] = "Quarta-feira";
        $lista[3][2] = $this->html->input("time", $horas["quarta_inicio"], array("class"=>"form-control", "name"=>"horario[quarta][inicio]"));
        $lista[3][3] = $this->html->input("time", $horas["quarta_fim"], array("class"=>"form-control", "name"=>"horario[quarta][fim]"));

        $lista[4][1] = "Quinta-feira";
        $lista[4][2] = $this->html->input("time", $horas["quinta_inicio"], array("class"=>"form-control", "name"=>"horario[quinta][inicio]"));
        $lista[4][3] = $this->html->input("time", $horas["quinta_fim"], array("class"=>"form-control", "name"=>"horario[quinta][fim]"));

        $lista[5][1] = "Sexta-feira";
        $lista[5][2] = $this->html->input("time", $horas["sexta_inicio"], array("class"=>"form-control", "name"=>"horario[sexta][inicio]"));
        $lista[5][3] = $this->html->input("time", $horas["sexta_fim"], array("class"=>"form-control", "name"=>"horario[sexta][fim]"));

        $lista[6][1] = "Sábado";
        $lista[6][2] = $this->html->input("time", $horas["sabado_inicio"], array("class"=>"form-control", "name"=>"horario[sabado][inicio]"));
        $lista[6][3] = $this->html->input("time", $horas["sabado_fim"], array("class"=>"form-control", "name"=>"horario[sabado][fim]"));

        $lista[7][1] = "Domingo";
        $lista[7][2] = $this->html->input("time", $horas["domingo_inicio"], array("class"=>"form-control", "name"=>"horario[domingo][inicio]"));
        $lista[7][3] = $this->html->input("time", $horas["domingo_fim"], array("class"=>"form-control", "name"=>"horario[domingo][fim]"));

        $horarios["lista-horarios"] = $this->html->table($lista,array("class"=>"table table-bordered table-condensed table-hover table-striped upper tabela-listar text-center","id"=>"lista-horarios"),true,"","",true);

        $templateHorarios = $this->includeHTML("../view/snippets/horarios.html");
        $tabela = $this->applyKeys($templateHorarios, $horarios);
        return $tabela;
    }

    public function checkServices($lista, $count = ""){

        $servicos = array_filter(explode(",", $lista));
        if(!empty($servicos)){
            foreach ($servicos as $servico) {
                // Principal
                if( $servico == 1 ) {$item["umExecutivo_umExecutivo"] = "checked";}
                if( $servico == 2 ) {$item["umExecutivoBag_umExecutivoBag"] = "checked";}
                if( $servico == 3 ) {$item["umTaxi_umTaxi"] = "checked";}
                if( $servico == 4 ) {$item["umStandard_umStandard"] = "checked";}
                if( $servico == 5 ) {$item["umFretamento_umFretamento"] = "checked";}
                if( $servico == 6 ) {$item["umEntregas_umEntregas"] = "checked";}
                if( $servico == 7 ) {$item["umMulher_umMulher"] = "checked";}

                if( $servico == 8 ) {$item["umExecutivo_motorista_executivo"] = "checked";}
                if( $servico == 9 ) {$item["umExecutivo_motorista_bilingue"] = "checked";}
                if( $servico == 10 ) {$item["umExecutivo_veiculo_blindado"] = "checked";}
                if( $servico == 11 ) {$item["umExecutivo_umAcessibilidade"] = "checked";}

                if( $servico == 12 ) {$item["umExecutivoBag_motorista_executivo"] = "checked";}
                if( $servico == 13 ) {$item["umExecutivoBag_motorista_bilingue"] = "checked";}
                if( $servico == 14 ) {$item["umExecutivoBag_veiculo_blindado"] = "checked";}
                if( $servico == 15 ) {$item["umExecutivoBag_umAcessibilidade"] = "checked";}

                if( $servico == 24 ) {$item["umMulher_motorista_executivo"] = "checked";}
                if( $servico == 25 ) {$item["umMulher_motorista_bilingue"] = "checked";}
                if( $servico == 26 ) {$item["umMulher_veiculo_blindado"] = "checked";}
                if( $servico == 27 ) {$item["umMulher_umAcessibilidade"] = "checked";}

                if( $servico == 16 ) {$item["umAcessibilidade_umAcessibilidade"] = "checked";}

                if( $servico == 17 ) {$item["umFretamento_van"] = "checked";}
                if( $servico == 18 ) {$item["umFretamento_onibus"] = "checked";}
                if( $servico == 19 ) {$item["umFretamento_micro"] = "checked";}

                if( $servico == 20 ) {$item["umEntregas_moto_frete"] = "checked";}
                if( $servico == 21 ) {$item["umEntregas_furgao"] = "checked";}
            }
        }
        if($count == ""){
            $item["count"] = 1;
        }
        $template = $this->includeHTML("../view/snippets/servicos.html");
        $check = $this->applyKeys($template, $item);

        return $check;
    }

    public function createPass($pass){
        // $pass = strrev($pass);
        $pass = trim(md5($pass));
        return $pass;
    }

    public function buscaStatusAssinatura($cliente_id) {

      if($this->model->countData('assinaturas',array('cliente_id'=>$cliente_id,'status'=>'Pago')) > 0) {
        //ativo
        return 'ativo';
      } else {
        //inativo
        return 'inativo';
      }

   }

    // Start Brendon
    //Função de pagamento generica
    public function pagamento($data) {

        $pagseguro = $this->loadModule('pagseguro', '', true);

        //Tratamento dos dados
        $arrayReplace = array(".", "-", " ", "(", ")");
        $data['cartao']['numero'] = str_replace($arrayReplace, "", $data['cartao']['numero']);
        $data['cliente']['cpf'] = str_replace($arrayReplace, "", $data['cliente']['cpf']);
        $data['cliente']['celular'] = str_replace($arrayReplace, "", $data['cliente']['celular']);
        $data['cliente']['areaCode'] = substr($data['cliente']['celular'], 0, 2);
        $data['cliente']['number'] = substr($data['cliente']['celular'], -9, 9);
        $nascimento = explode('-', $data['cliente']['data_de_nascimento']);
        $data['cliente']['data_de_nascimento'] = $nascimento[2].'/'.$nascimento[1].'/'.$nascimento[0];

        //Variaveis para o pagamento
        $payment['payment']['mode'] = 'default';
        $payment['payment']['currency'] = 'BRL';
        $payment['payment']['notificationURL'] = 'http://roterizer.alphacode.mobi/pagseguro/Notifica/';
        // $payment['payment']['receiverEmail'] = 'roterizer@sandbox.pagseguro.com.br';

        $payment['payment']['sender']['hash'] = $data['sender_hash'];
        // $payment['payment']['sender']['ip'] = $_SERVER['REMOTE_ADDR'];
        $payment['payment']['sender']['phone']['areaCode'] = $data['cliente']['areaCode'];
        $payment['payment']['sender']['phone']['number'] = $data['cliente']['number'];
        $payment['payment']['sender']['email'] = "teste@sandbox.pagseguro.com.br"; //teste
        // $payment['payment']['sender']['email'] = $data['cliente']['email']; //producao
        $payment['payment']['sender']['documents']['document']['type'] = 'CPF';
        $payment['payment']['sender']['documents']['document']['value'] = $data['cliente']['cpf'];
        // $payment['payment']['sender']['bornDate'] = $data['cliente']['data_de_nascimento'];
        $payment['payment']['sender']['name'] = $data['cliente']['nome'];

        $payment['payment']['items']['item']['id'] = $data['plano']['id'];
        $payment['payment']['items']['item']['description'] = $data['plano']['nome'];
        $payment['payment']['items']['item']['amount'] = $data['plano']['valor'];
        $payment['payment']['items']['item']['quantity'] = 1;

        $payment['payment']['shipping']['cost'] = 0.00;
        $payment['payment']['shipping']['addressRequired'] = false;

        $payment['payment']['method'] = 'creditCard';

        $payment['payment']['creditCard']['token'] = $data['cartao_token'];
        $payment['payment']['creditCard']['holder']['name'] = $data['cartao']['nome_titular'];
        $payment['payment']['creditCard']['installment']['quantity'] = 1;
        $payment['payment']['creditCard']['installment']['value'] = $data['plano']['valor'];
        $payment['payment']['creditCard']['holder']['documents']['document']['type'] = 'CPF';
        $payment['payment']['creditCard']['holder']['documents']['document']['value'] = $data['cliente']['cpf'];
        $payment['payment']['creditCard']['billingAddress']['street'] = 'Alameda araguaia';
        $payment['payment']['creditCard']['billingAddress']['number'] = '2190';
        $payment['payment']['creditCard']['billingAddress']['complement'] = 'cj 307';
        $payment['payment']['creditCard']['billingAddress']['district'] = 'Tamboré';
        $payment['payment']['creditCard']['billingAddress']['city'] = 'Barueri';
        $payment['payment']['creditCard']['billingAddress']['state'] = 'SP';
        $payment['payment']['creditCard']['billingAddress']['country'] = 'BRA';
        $payment['payment']['creditCard']['billingAddress']['postalCode'] = '06455000';

        $res = $pagseguro->pagamento($payment);
        return $res;
    }

    public function verificaStatusPagamento($dados){
        switch ($dados) {
            case 1:
                $status = 'Aguardando Pagamento';
                break;
            case 2:
                $status = 'Em análise';
                break;
            case 3:
                $status = 'Pago';
                break;
            case 4:
                $status = 'Disponível';
                break;
            case 5:
                $status = 'Em disputa';
                break;
            case 6:
                $status = 'Devolvida';
                break;
            case 7:
                $status = 'Cancelada';
                break;
        }
        return $status;
    }
    // End Brendon

}
?>
