<?php
      class cliente extends simplePHP {

        private $model;
        private $html;
        private $core;
        private $ui;
        private $util;
        private $file;

        public function __construct() {
    		    global $keys;

            #load model module
            $this->model = $this->loadModule('model');
            $this->model->context = true;

            #load html module
            $this->html = $this->loadModule('html');

            #load ui module
            $this->ui = $this->loadModule('ui');

            #load file module
            $this->file = $this->loadModule('file');

            #load util module
            $this->util = $this->loadModule('util');

            #load core module
            $this->core = $this->loadModule('core','',true);

            #footer
            $this->keys['footer'] = $this->includeHTML('../view/admin/footer.html');

            #topheader
            $this->keys['topheader'] =  $this->includeHTML('../view/admin/topheader.html');
            $this->keys['header'] =  $this->includeHTML('../view/admin/header.html');
            $this->keys['topo'] =  $this->includeHTML('../view/admin/topo.html');

            #menu
            $this->keys['menu'] =  $this->includeHTML('../view/admin/menu.html');
            $this->keys['sidemenu'] =  $this->includeHTML('../view/admin/sidemenu.html');
            $this->keys['topmenu'] =  $this->includeHTML('../view/admin/topmenu.html');

            $this->keys['pageTitle'] = Cliente;

            $usuario = $this->model->getOne('usuario',$_SESSION['usuario_id']);
            $this->keys['usernameMaster'] = $_SESSION['usuario'];
            $this->keys['cliente_menu'] = $this->core->loadMenu();

            $this->keys['activecliente'] = 'active';



        }

        public function _actionStart() {
          $this->redirect('/cliente/listar');
          return $this->keys;
        }
        public function _actionListar() {

          #dados do estado
            $this->keys['select_estado'] = $this->html->select(false, $this->util->ufs(), 'estado','');

            

          $steper = 15;
          $modulo = $this->getParameter('1');
          $page = ($this->getParameter('3') != '') ? $this->getParameter('3') : 1;

          $total = $this->model->countData('cliente',$_SESSION['filtros']['cliente']);

          $this->keys['paginacao'] = $this->ui->pager($steper,$total,$page,'goUrl');

          $limits['limit'] = $steper;
          $limits['start'] = $this->calculaStartPaginacao($page,$steper);

          $dados = $this->model->getData('cliente','*',$_SESSION['filtros']['cliente'],$limits);

          if($_SESSION['filtros']['cliente'] != '') {
            $this->keys['limpar'] = '<a href="/cliente/limpafiltros" class="btn btn-info"><i class="glyphicon glyphicon-zoom-out" aria-hidden="true"></i> Limpar</a>';
            $this->keys['filtroativo'] = 'filtroativo';
          } else {
            $this->keys['limpar'] = '';
            $this->keys['filtroativo'] = '';
          }

          if($dados[0]['result'] != 'empty') {
              $tabela[0]['id'] = '#ID';
              $tabela[0]['nome'] = 'Nome';
             $tabela[0]['estado'] = 'Estado';
             $tabela[0]['cidade'] = 'Cidade';
             $tabela[0]['email'] = 'Email';
             $tabela[0]['senha'] = 'Senha';
             
              $tabela[0]['acoes'] = 'Ações';
              $x = 1;
              foreach($dados as $dado) {
                $tabela[$x]['id'] = $dado['id'];
                $tabela[$x]['nome'] = $dado['nome'];
                  $tabela[$x]['estado'] = $dado['estado'];
                  $tabela[$x]['cidade'] = $dado['cidade'];
                  $tabela[$x]['email'] = $dado['email'];
                  $tabela[$x]['senha'] = $dado['senha'];
                  
                $tabela[$x]['acoes'] = $this->html->link('Ver',"/cliente/ver/$dado[id]",'','btn btn-info btn-xs');

                $x++;
              }
              $this->keys['tabela'] = $this->html->table($tabela,array('class'=>'table table-bordered table-condensed table-hover table-striped upper tabela-listar ','id'=>'lista-cliente'),true,'','',true);
          } else {
              $this->keys['tabela'] = $this->html->div('Não foram encontrados cliente cadastrados  ',array('class'=>'center'));
          }

          #aplica filtros
          foreach($_SESSION['filtros'][$modulo] as $key => $value) {
            $key = str_replace('like','',$key);
            $this->keys['filtro_'.trim($key)] = $value;
          }
          return $this->keys;
        }
        public function _actionInserir() {
          $this->keys['select_estado'] = $this->html->select(false, $this->util->ufs(), 'estado','');
          return $this->keys;
        }
        public function _actionGrava() {
          foreach ($_FILES as $key => $file) {
            if($file['tmp_name'] != '') {
              $_POST[$key] = $this->file->uploadFile($file,'uploads/');
            }
          }

          $_POST['senha'] = md5($_POST['senha']);

          $_POST['usuario_id'] = $_SESSION['usuario_id'];
          $this->model->addData('cliente',$_POST,true);
          die('sucesso;');
        }

        public function _actionAltera() {

          foreach ($_FILES as $key => $file) {
            if($file['tmp_name'] != '') {
              $_POST[$key] = $this->file->uploadFile($file,'uploads/');
            }
          }

          $_POST['senha'] = md5($_POST['senha']);

          $dado_id = $_REQUEST['id'];
          $this->model->alterData('cliente',$_POST,array('id' => $dado_id));
          die('sucesso;');
        }

        public function _actionVer() {

          $dado_id = $this->getParameter('3');
          $this->keys += $this->model->getOne('cliente',$dado_id);

          $this->keys['select_estado'] = $this->html->select(false, $this->util->ufs(), 'estado',$this->keys['estado']);

          return $this->keys;
        }

        public function _actionFiltrar() {
          $modulo = $this->getParameter('1');

          foreach ($_POST as $key => $valueTxt) {
            $key = str_replace('like_','like ',$key);
            if($valueTxt != '') {
              $_SESSION['filtros'][$modulo][$key] = $valueTxt;
            }
            if($valueTxt == '') {
              unset($_SESSION['filtros'][$modulo][$key]);
            }

            if($_SESSION['filtros'][$modulo][$key] == '0') {
              unset($_SESSION['filtros'][$modulo][$key]);
            }
          }

          $this->redirect("/cliente/listar");
        }

        public function _actionLimpafiltros() {
          $modulo = $this->getParameter('1');
          unset($_SESSION['filtros'][$modulo]);
          $this->redirect("/cliente/listar");
        }
      }
?>