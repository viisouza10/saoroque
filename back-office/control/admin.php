<?php
/**
 * Project: Alphacode
 *
 * @copyright Rafael Franco - https://github.com/alphacodeBR
 * @author Rafael Franco <rafael@alphacode.com.br>
 */

class admin extends simplePHP {
    #initialize vars
    private $model;
    private $html;
    private $core;
    private $ui;


    public function __construct() {
        global $keys;

        #load model module
        $this->model = $this->loadModule('model');
        $this->model->context = true;

        #load html module
        $this->html = $this->loadModule('html');

        #load ui module
        $this->ui = $this->loadModule('ui');

        #load core module
        $this->core = $this->loadModule('core','',true);

        if(!$this->core->isLogged()) {
          $this->redirect('/');
          exit;
        }
        #inclui os arquivos que sao globais a todas as sessoes
        $this->keys['pageTitle'] = 'Painel de Controle';

        #include system globals

        #topheader
        $this->keys['topheader'] =  $this->includeHTML('../view/admin/topheader.html');
        $this->keys['header'] =  $this->includeHTML('../view/admin/header.html');
        $this->keys['topo'] =  $this->includeHTML('../view/admin/topo.html');
        $this->keys['footer'] =  $this->includeHTML('../view/admin/footer.html');

        #menu
        $this->keys['menu'] =  $this->includeHTML('../view/admin/menu.html');
        $this->keys['sidemenu'] =  $this->includeHTML('../view/admin/sidemenu.html');
        $this->keys['topmenu'] =  $this->includeHTML('../view/admin/topmenu.html');

        #nome de usuario hotsite
        $conta = $this->model->getData('adm_contas','nome, nome_responsavel',array('id'=>$_SESSION['conta_id']));
        $usuario = $this->model->getOne('usuario',$_SESSION['usuario_id']);

        $this->keys['usernameMaster'] = $_SESSION['usuario'];
        $this->keys['userAvatar']  = $usuario['avatar'];

        $this->keys['pageTitle'] = SOFTWAREVERSION;
        $this->keys['software_name'] = $_SESSION['conta']['nome'];

        $this->keys['accountJS'] = '<script src="/js/account/'.$_SESSION['conta_id'].'.js"></script>';
        $this->keys['accountCSS'] =  '<link href="/css/account/'.$_SESSION['conta_id'].'.css" rel="stylesheet">';

        $this->keys['cliente_menu'] = $this->core->loadMenu();

        $permissoes = explode(',', $_SESSION['permissoes']);

        $this->keys['painelGeral'] = 'painelGeral';
        $this->keys['activeadmin'] = 'active';

    }

    public function _actionStart() {

        $this->keys['clientes'] = $this->model->countData('clientes');
        $this->keys['empreendimentos'] = $this->model->countData('empreendimentos');
        $this->keys['chats'] = $this->model->countData('chats');
        
        return $this->keys;
    }
    public function _actionLogout() {

        unset($_SESSION['usuario_id']);
        unset($_SESSION['conta_id']);
        unset($_SESSION['usuario']);
        unset($_SESSION['start']);
        unset($_SESSION['tipo']);
        unset($_SESSION['permissoes']);


        $this->redirect('/admin');

        return $this->keys;
    }



}
