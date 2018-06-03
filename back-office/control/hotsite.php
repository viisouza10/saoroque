<?php
/**
* Project: AlphaMarket
*
* @copyright Rafael Franco - https://github.com/rafaelfranco
* @author Rafael Franco <rafael@alphacode.com.br>
*/

class hotsite extends simplePHP {

    #initialize vars
    private $model;
    private $html;
    private $core;
    private $zip;
    private $file;

    public function __construct() {

        global $keys;

        #load model module
        $this->model = $this->loadModule("model");

        #load html module
        $this->html = $this->loadModule("html");

        $this->file = $this->loadModule("file");

        $this->ui = $this->loadModule("ui");

        #load html module
        $this->core = $this->loadModule("core", "", true);

        #inclui os arquivos que sao globais a todas as sessoes
        #set global keys
        $this->keys["pageTitle"] = SOFTWAREVERSION;
        $this->keys["softwareName"] = SOFTWAREVERSION;

        #topheader
        $this->keys['topheader'] =  $this->includeHTML('../view/admin/topheader.html');
        $this->keys['header'] =  $this->includeHTML('../view/admin/header.html');
        $this->keys['topo'] =  $this->includeHTML('../view/admin/topo.html');
        $this->keys['footer'] =  $this->includeHTML('../view/admin/footer.html');

        #menu
        $this->keys['menu'] =  $this->includeHTML('../view/admin/menu.html');
        $this->keys['sidemenu'] =  $this->includeHTML('../view/admin/sidemenu.html');
        $this->keys['topmenu'] =  $this->includeHTML('../view/admin/topmenu.html');

        $this->keys["URL_SITE"] = "http://".$_SERVER["HTTP_HOST"]."/";
    }

    public function _actionStart() {

        return $this->keys;
    }

    public function _actionNoticia(){

        return $this->keys;
    }

    public function _actionLogin(){

        if(!empty($_SESSION["id"])){
            $this->redirect("/profile");
        }
        return $this->keys;
    }

    public function _actionLoginAdmin(){
        $this->keys["topheader"] = $this->includeHTML("../view/helpers/topheader.html");
        $this->keys["footer"] = $this->includeHTML("../view/helpers/footer.html");

        return $this->keys;
    }

    public function _actionRecover_Password(){
        $this->keys["topheader"] = $this->includeHTML("../view/helpers/topheader.html");
        $this->keys["footer"] = $this->includeHTML("../view/helpers/footer.html");

        return $this->keys;
    }
    /**
    * _actionLogout function
    * @return array
    * */
    public function _actionLogout() {
        session_start();
        unset($_SESSION);
        unset($_SESSION["usuario_id"]);
        unset($_SESSION["tipo"]);
        session_destroy();

        $this->redirect("/login");
        return $this->keys;
    }


   
    public function _actionAtualizar_senha(){
        $code = $this->getParameter(2);

        $tipo = $this->model->getData("recovery_pass", "tipo_usuario", array("code" => $code, "status" => 1))[0];
        if ($tipo["result"] == "empty") {
            $this->redirect("/login");
        }
        // $pedidos = $this->model->getData("pedido", "a.*, mt.nome as motorista, s.nome as servico", array("cliente_id"=>$_SESSION["id"]), "", "a.id desc", "LEFT JOIN motorista as mt on a.motorista_id = mt.id inner join servico as s on s.id = a.servico_id");

        $user = $this->model->getData("recovery_pass", "*, a.id as id_code", array("a.code" => $code), "", "a.id ASC", "INNER JOIN ".$tipo["tipo_usuario"]." AS u ON u.id = a.usuario_id")[0];

        $this->keys["id"] = $user["id_code"];
        $this->keys["code"] = $code;
        $this->keys["nome"] = explode(" ", $user["nome"])[0];

        // $this->keys["nome"] =

        return $this->keys;
    }
}
?>
