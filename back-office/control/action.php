<?php
/**
* Project: UrbanMobi
*
* @copyright Rafael Franco - https://github.com/rafaelfranco
* @author Felipe Soares <felipe@alphacode.com.br>
*/

class action extends simplePHP {
	private $core;
	private $keys;
	private $model;

	public function __construct() {

		#load core module
		$this->core = $this->loadModule("core", "", true);

		#load model module
		$this->model = $this->loadModule("model", "");

		#load html module
		$this->html = $this->loadModule("html", "");

		#load module for validations
		$this->validator = $this->loadModule("validator", "");

		#load file module
		$this->file = $this->loadModule("file", "");

		#load image module
		$this->image = $this->loadModule("image", "");

		#load email module
		$this->email = $this->loadModule("email", "");
	}

	public function _actionStart() {

		return $this->keys;
	}

	/**
	* _actionLogin function
	* @return string
	* */
	public function _actionLogin() {
		$data["email"] = $_POST["login"];
		$data["senha"] = trim(md5($_POST["senha"]));

		#get user
		$res = $this->model->getData("usuario", "*", $data);

		//if return error array
		if(@$res[0]["result"] == "empty") {
			echo "erro;E-mail ou senha incorretos, verifique-os e tente novamente";
			exit;
		} else {
			if($res[0]["status"] == "0"){
				echo "erro;Usuário inativo, contate seu administrador";
				exit;
			} else {
				//start session
				$this->logUser($res[0]["id"], $res[0]["nome"], $res[0]["tipo"], $res[0]["conta_id"], $res[0]["permissoes"]);

				echo "sucesso;".$res[0]["tipo"];

				exit;
			}
		}
	}

	/**
	* logUser function
	* @return void
	* */
	private function logUser($usuario_id, $usuario, $tipo, $conta_id, $permissoes) {

		$_SESSION["usuario_id"] = $usuario_id;
		$_SESSION["conta_id"] = $conta_id;
		$_SESSION["usuario"] = $usuario;
		$_SESSION["tipo"] = $tipo;
		$_SESSION["start"] = time();
		$_SESSION["permissoes"] = $permissoes;
	}

	//-----------------------------------USUARIOS------------------------------------//
	public function _actionAddUsuario() {

		$data = $_POST;

		$permissoes = "";
		if(!empty($data["modulos"])){
			foreach ($data["modulos"] as $modulo) {
				$permissoes .= $modulo.",";
			}
		}
		unset($data["modulos"]);

		$data["conta_id"] = "1";
		$data["senha"] = trim(md5($data["senha"]));
		$data["permissoes"] = (!empty($permissoes)) ? $permissoes : "1,";

		$res = $this->model->addData("usuario", $data, true);

		if($res["status"] != "erro"){
			$this->returnAction("success", "Usuário cadastrado com sucesso!", $res);
		} else {
			$this->returnAction("error", "Houve um erro ao cadastrar o usuário, por favor contate o suporte!");
		}
		exit;
	}

	public function _actionEditUsuario() {

		$data = $_POST;

		if($data["senha"] == ""){
			unset($data["senha"]);
		} else {
			$senha = $this->model->getData("usuario", "senha", array("id"=>$data["id"]))[0];
			if($senha["senha"] != md5($data["senha"])){
				$data["senha"] = md5($data["senha"]);
			}
		}

		$permissoes = "";
		if(!empty($data["modulos"])){
			foreach ($data["modulos"] as $modulo) {
				$permissoes .= $modulo.",";
			}
		}
		unset($data["modulos"]);

		$data["permissoes"] = (!empty($permissoes)) ? $permissoes : "1,";
		$res = $this->model->alterData("usuario", $data, array("id"=>$data["id"]));

		if($res == 1){
			$this->returnAction("success", "Usuário atualizado com sucesso!");
		} else {
			$this->returnAction("error", "Houve um erro ao atualizar o usuário, por favor contate o suporte!");
		}
		exit;
	}

	public function _actionRecoverPassword(){

		$email = $_REQUEST["email"];
		$fromEmail = "felipe@alphacode.com.br";
		$subject = "AlphaMarket - Recuperar senha";

		$senhaGerada = md5(time().rand(0,1000));
		$senhaUsuario = substr($senhaGerada, 0,6);
		$senhaParaGravarNoBanco = md5($senhaUsuario);

		//salvar no banco de dados
		$usuario = $this->model->getData("usuario", "id", array("email"=>$email));
		if ($usuario[0]["result"] != "empty") {

			$this->model->alterData("usuario",array("senha"=>$senhaParaGravarNoBanco),array("id"=>$usuario[0]["id"]));

			$info["senha"] = $senhaUsuario;
			$template = $this->includeHTML("../view/emails/recover.html");

			$send_mail = $this->email->send($email, $fromEmail, $subject, $template, $info);

			echo "sucesso;";
		} else {
			echo "erro;";
		}
		exit;
	}

	public function _actionCep() {

		$curl = curl_init();
		$cep = $this->getParameter(3);

		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://cep.alphacode.com.br/action/cep/".$cep,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"postman-token: 8298f645-ca52-c11d-8116-fe26e4e1780f"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		echo $response; exit;
	}

	////////////////////////// CLIENTE //////////////////////////////
	public function _actionAddCliente(){

		$data = $_POST;
		unset($data["id"]);
		$this->begin();

		if($data["cliente"]){
			if (!$this->validator->cpf(preg_replace("~[^a-zA-Z0-9]+~", "", $data["cliente"]["cpf"]))){
				$this->returnAction("error", "O CPF do cliente é inválido, verifique se foi digitado corretamente!", "cliente_cpf");
			}
			if (!$this->validaCelular($data["cliente"]["celular"])){
				$this->returnAction("error", "Este celular já está sendo usado!", "cliente_celular");
			}
			if ($data["cliente"]["numero"] <= 0){
				$this->returnAction("error", "O Número é inválido!", "cliente_numero");
			}
			if (strlen($data["cliente"]["bairro"]) < 2 || strlen($data["cliente"]["bairro"]) > 72){
				$this->returnAction("error", "O Bairro deve ter entre 2 e 72 caracteres!", "cliente_bairro");
			}
			if (strlen($data["cliente"]["cidade"]) < 2 || strlen($data["cliente"]["cidade"]) > 72){
				$this->returnAction("error", "A cidade deve ter entre 2 e 72 caracteres!", "cliente_cidade");
			}
			if (strlen($data["cliente"]["endereco"]) < 2 || strlen($data["cliente"]["endereco"]) > 72){
				$this->returnAction("error", "O endereço deve ter entre 2 e 72 caracteres!", "cliente_endereco");
			}
			$data["cliente"]["senha"] = trim(md5($data["cliente"]["senha"]));
			$data["cliente"]["nascimento"] = $this->core->saveDate($data["cliente"]["nascimento"]);

			$cliente_id = $this->model->addData("cliente", $data["cliente"], true);

		} else {
			$this->returnAction("error", "Sem dados do cliente!");
		}
		if($data["servicos"]){
			$servicos = $data["servicos"][1];
			if(!empty($servicos)){
				foreach ($servicos as $key => $values) {
					foreach ($values as $value) {
						if($value != ""){
							$itens .= $value.",";
						}
					}
				}
				$this->model->alterData("cliente", array("servico"=>$itens), array("id"=>$cliente_id));
			}
		} else {
			$this->returnAction("error", "Nenhum serviço foi selecionado!");
		}

		if($data["horario"]){
			$horario_id = $this->model->addData("cliente_horario", array("cliente_id"=>$cliente_id), true);
			if($horario_id > 0){
				$this->trataHorario($data["horario"], "cliente", $cliente_id, $horario_id);
			}
		} else {
			$this->returnAction("error", "Nenhum horário foi definido!");
		}

		if(!$cliente_id > 0 || !$horario_id > 0) {
			$this->rollback();
		} else {
			$this->commit();
		}

		if( $cliente_id > 0 ){

			$email = $data["cliente"]["email"];
			$fromEmail = "felipe@alphacode.com.br";
			$subject = "Cadastro Aprovado";

			$info["cliente"] = $data["cliente"]["nome"];
			$info["celular"] = $data["cliente"]["celular"];
			$info["senha"] = $data["cliente"]["senha"];

			$template = $this->includeHTML("../view/emails/novo_cliente.html");

			$send_mail = $this->email->send($email, $fromEmail, $subject, $template, $info);
		}

		if($cliente_id > 0 && $horario_id > 0){
			$this->returnAction("success", "Cliente cadastrado com sucesso!");
		}
		exit;
	}

	public function _actionEditCliente(){

		$data = $_POST;
		$this->begin();

		if($data["cliente"]){
			if($data["cliente"]["senha"] != ""){
				$senha = $this->model->getData("cliente", "senha", array("id"=>$data["cliente"]["id"]))[0];
				if($senha[0]["result"] != "empty"){
					$oldPass = $senha["senha"];
					$newPass = $this->core->createPass($data["cliente"]["senha"]);

					if($oldPass != $newPass){
						$data["cliente"]["senha"] = $newPass;
					} else {
						unset($data["cliente"]["senha"]);
					}
				}
			} else {
				unset($data["cliente"]["senha"]);
			}
			if (!$this->validator->cpf(preg_replace("~[^a-zA-Z0-9]+~", "", $data["cliente"]["cpf"]))){
				$this->returnAction("error", "O CPF do cliente é inválido, verifique se foi digitado corretamente!", "cliente_cpf");
			}
			if (!$this->validaCelular($data["cliente"]["celular"], $data["cliente"]["id"])){
				$this->returnAction("error", "Este celular já está sendo usado!", "cliente_celular");
			}
			if ($data["cliente"]["numero"] <= 0){
				$this->returnAction("error", "O Número é inválido!", "cliente_numero");
			}
			if (strlen($data["cliente"]["bairro"]) < 2 || strlen($data["cliente"]["bairro"]) > 72){
				$this->returnAction("error", "O Bairro deve ter entre 2 e 72 caracteres!", "cliente_bairro");
			}
			if (strlen($data["cliente"]["cidade"]) < 2 || strlen($data["cliente"]["cidade"]) > 72){
				$this->returnAction("error", "A cidade deve ter entre 2 e 72 caracteres!", "cliente_cidade");
			}
			if (strlen($data["cliente"]["endereco"]) < 2 || strlen($data["cliente"]["endereco"]) > 72){
				$this->returnAction("error", "O endereço deve ter entre 2 e 72 caracteres!", "cliente_endereco");
			}
			$data["cliente"]["nascimento"] = $this->core->saveDate($data["cliente"]["nascimento"]);
			$data["cliente"]["regra_master"] = ($data["cliente"]["regra_master"]) ? $data["cliente"]["regra_master"] : "nao";

			$cliente = $this->model->alterData("cliente", $data["cliente"], array("id"=>$data["cliente"]["id"]));
		} else {
			$this->returnAction("error", "Sem dados do cliente!");
		}

		if($data["servicos"]){
			$servicos = $data["servicos"][1];
			if(!empty($servicos)){
				foreach ($servicos as $key => $values) {
					foreach ($values as $value) {
						if($value != ""){
							$itens .= $value.",";
						}
					}
				}
				$this->model->alterData("cliente", array("servico"=>$itens), array("id"=>$data["cliente"]["id"]));
			}
		} else {
			$this->returnAction("error", "Nenhum serviço foi selecionado!");
		}

		if($data["horario"]){
			$horario = $this->model->getData("cliente_horario", "id", array("cliente_id"=>$data["cliente"]["id"]))[0];
			if($horario[0]["result"] != "empty"){
				$this->trataHorario($data["horario"], "cliente", $data["cliente"]["id"], $horario["id"]);
			} else {
				$horario_id = $this->model->addData("cliente_horario", array("cliente_id"=>$data["cliente"]["id"]), true);
				if($horario_id > 0){
					$this->trataHorario($data["horario"], "cliente", $data["cliente"]["id"], $horario_id);
				}
			}
		} else {
			$this->returnAction("error", "Nenhum horário foi definido!");
		}

		if(!$cliente > 0) {
			$this->rollback();
		} else {
			$this->commit();
		}
		if($cliente > 0){
			$this->returnAction("success", "Cliente atualizado com sucesso!");
		}
		exit;
	}

	public function _actionEditClienteAdmin(){

		$data = $_POST;
		$nascimento = str_replace("/", "_", $data["nascimento"]);
		$data["nascimento"] = date("Y-m-d", $this->core->preparaDataParaConsulta($nascimento));

		$res = $this->model->alterData("cliente", $data, array("id"=>$data["id"]));

		if ( $res > 0 ) {
			$result["status"]  = "success";
			$result["message"] = "Cliente editado com sucesso!";
		} else {
			$result["status"]  = "error";
			$result["message"] = "Ocorreu um erro durante o processo, por favor, tente novamente!";
		}

		echo json_encode($result);
		exit;
	}


	//////////////////// ALERTAS /////////////////////////////
	public function _actionAlertaGeral(){

		$data = $_POST;

		$data["data_cadastro"] = date("Y-m-d");

		$res = $this->model->addData("alerta", array("texto"=>$data["mensagem"], "data_cadastro"=>$data["data_cadastro"]), true);

		if($data["tipo"] == "cliente"){
			$clientes = $this->model->getData("cliente", "id, endpoint");
			if($clientes[0]["result"] != "empty"){
				foreach ($clientes as $cliente) {
					// pre($cliente);
					$cl_at = $this->model->addData("cliente_alerta", array("cliente_id"=>$cliente["id"], "alerta_id"=>$res));
				}
			}
		} else {
			$motoristas = $this->model->getData("motorista", "id");
			if($motoristas[0]["result"] != "empty"){
				foreach ($motoristas as $motorista) {
					// pre($motorista);
					$mt_at = $this->model->addData("motorista_alerta", array("motorista_id"=>$motorista["id"], "alerta_id"=>$res));
				}
			}
		}
		if ( $res > 0 ) {
			$return["status"]  = "success";
			$return["message"] = "Alerta enviado com sucesso!";
		} else {
			$return["status"]  = "error";
			$return["message"] = "Ocorreu um erro durante o processo, por favor, tente novamente!";
		}
		echo json_encode($return);
		exit;
	}

	public function _actionAlerta(){

		$data = $_POST;

		$data["data_cadastro"] = date("Y-m-d");

		$res = $this->model->addData("alerta", array("texto"=>$data["mensagem"], "data_cadastro"=>$data["data_cadastro"]), true);

		if(!empty($data["id"]) && $data["tipo"] == "cliente"){
			$cl_at = $this->model->addData("cliente_alerta", array("cliente_id"=>$data["id"], "alerta_id"=>$res));
		} else {
			$mt_at = $this->model->addData("motorista_alerta", array("motorista_id"=>$data["id"], "alerta_id"=>$res));
		}
		if ( $res > 0 ) {
			$return["status"]  = "success";
			$return["message"] = "Alerta enviado com sucesso!";
		} else {
			$return["status"]  = "error";
			$return["message"] = "Ocorreu um erro durante o processo, por favor, tente novamente!";
		}
		echo json_encode($return);
		exit;
	}

	//////////////////// GENÉRICAS /////////////////////////////
	public function saveImage($table, $archive, $folder, $name, $reference, $reference_id){
		$file = $this->loadModule("file");
		if(!empty($archive) && $archive["error"] == 0){
			$ext = pathinfo($archive["name"], PATHINFO_EXTENSION);
			$url = $file->uploadFile($archive, "../public/".$folder, $reference_id.".".$ext);
			$image = URL.$folder.$url;
			$anexo["origem"] = $reference;
			$anexo["referencia_id"] = $reference_id;
			$anexo["nome"] = str_replace(" ", "_", $archive["name"]);
			$anexo["tamanho"] = $archive["size"];
			$anexo["url"] = $image;
			$this->model->alterData($table, array($name=>$image), array("id"=>$reference_id));
			$save = $this->model->addData("anexos", $anexo, true);
			return $save;
		} else {
			return "error";
		}
	}

	public function returnAction($status, $message, $id = null){

		$return["status"] = $status;
		$return["message"] = $message;
		$return["data"] = $data;
		echo json_encode($return);
		exit;
	}

	public function removeMaskMoney($string){
		$mascaraRemovida = str_replace("R$", "", str_replace(",", ".", str_replace(".", "", $string)));

		return $mascaraRemovida;
	}

	public function storeImage($obj, $path, $id) {
		require_once("slim.php");
		try {
			$images = Slim::getImages();
		}
		catch (Exception $e) {
			Slim::outputJSON(SlimStatus::FAILURE);
			return;
		}
		if (count($images) === 0) {
			Slim::outputJSON(SlimStatus::FAILURE);
			return;
		}

		$image = $images[0];
		$ext = pathinfo($image["input"]["name"], PATHINFO_EXTENSION);
		$file = Slim::saveFile($image["output"]["data"], $id.".".$ext, $path, true);
		$reduced = $this->image->reduceImage($file["path"], 300, 300);

		return $file;
	}

	public function begin(){
		$this->model->sql("BEGIN");
	}

	public function commit(){
		$this->model->sql("COMMIT");
	}

	public function rollback(){
		$this->model->sql("ROLLBACK");
	}
}
?>
