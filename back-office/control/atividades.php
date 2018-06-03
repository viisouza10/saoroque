<?php
      class atividades extends simplePHP {

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

            $this->keys['pageTitle'] = Atividades;

            $usuario = $this->model->getOne('usuario',$_SESSION['usuario_id']);
            $this->keys['usernameMaster'] = $_SESSION['usuario'];
            $this->keys['cliente_menu'] = $this->core->loadMenu();

            $this->keys['activeatividades'] = 'active';



        }

        public function _actionStart() {
          $this->redirect('/atividades/listar');
          return $this->keys;
        }
        public function _actionListar() {

          #dados do cliente
            $cliente = $this->model->getList("cliente","id","nome");
            $this->keys['select_cliente'] = $this->html->select(false, $cliente, 'cliente_id',$_SESSION['filtros']['atividades']['cliente_id'],0);

            #dados do estabelecimento
            $estabelecimento = $this->model->getList("estabelecimento","id","nome");
            $this->keys['select_estabelecimento'] = $this->html->select(false, $estabelecimento, 'estabelecimento_id',$_SESSION['filtros']['atividades']['estabelecimento_id'],0);

            

          $steper = 15;
          $modulo = $this->getParameter('1');
          $page = ($this->getParameter('3') != '') ? $this->getParameter('3') : 1;

          $total = $this->model->countData('atividades',$_SESSION['filtros']['atividades']);

          $this->keys['paginacao'] = $this->ui->pager($steper,$total,$page,'goUrl');

          $limits['limit'] = $steper;
          $limits['start'] = $this->calculaStartPaginacao($page,$steper);

          $dados = $this->model->getData('atividades','*',$_SESSION['filtros']['atividades'],$limits);

          if($_SESSION['filtros']['atividades'] != '') {
            $this->keys['limpar'] = '<a href="/atividades/limpafiltros" class="btn btn-info"><i class="glyphicon glyphicon-zoom-out" aria-hidden="true"></i> Limpar</a>';
            $this->keys['filtroativo'] = 'filtroativo';
          } else {
            $this->keys['limpar'] = '';
            $this->keys['filtroativo'] = '';
          }

          if($dados[0]['result'] != 'empty') {
              $tabela[0]['id'] = '#ID';
              $tabela[0]['cliente'] = 'Cliente';
             $tabela[0]['estabelecimento'] = 'Estabelecimento';
             $tabela[0]['post'] = 'Post';
             $tabela[0]['recomendacao'] = 'Recomendacao';
             
              $tabela[0]['acoes'] = 'Ações';
              $x = 1;
              foreach($dados as $dado) {
                $tabela[$x]['id'] = $dado['id'];
                $tabela[$x]['cliente'] = $cliente[$dado['cliente_id']];
                 $tabela[$x]['estabelecimento'] = $estabelecimento[$dado['estabelecimento_id']];
                 $tabela[$x]['post'] = $dado['post'];
                  $tabela[$x]['recomendacao'] = $dado['recomendacao'];
                  
                $tabela[$x]['acoes'] = $this->html->link('Ver',"/atividades/ver/$dado[id]",'','btn btn-info btn-xs');

                $x++;
              }
              $this->keys['tabela'] = $this->html->table($tabela,array('class'=>'table table-bordered table-condensed table-hover table-striped upper tabela-listar ','id'=>'lista-atividades'),true,'','',true);
          } else {
              $this->keys['tabela'] = $this->html->div('Não foram encontrados atividades cadastrados  ',array('class'=>'center'));
          }

          #aplica filtros
          foreach($_SESSION['filtros'][$modulo] as $key => $value) {
            $key = str_replace('like','',$key);
            $this->keys['filtro_'.trim($key)] = $value;
          }
          return $this->keys;
        }
        public function _actionInserir() {
          $cliente = $this->model->getList("cliente","id","nome");
            $this->keys['select_cliente'] = $this->html->select(false, $cliente, 'select_tabela','');$estabelecimento = $this->model->getList("estabelecimento","id","nome");
            $this->keys['select_estabelecimento'] = $this->html->select(false, $estabelecimento, 'select_tabela','');
          return $this->keys;
        }
        public function _actionGrava() {
          foreach ($_FILES as $key => $file) {
            if($file['tmp_name'] != '') {
              $_POST[$key] = $this->file->uploadFile($file,'uploads/');
            }
          }

          

          $_POST['usuario_id'] = $_SESSION['usuario_id'];
          $this->model->addData('atividades',$_POST,true);
          die('sucesso;');
        }

        public function _actionAltera() {

          foreach ($_FILES as $key => $file) {
            if($file['tmp_name'] != '') {
              $_POST[$key] = $this->file->uploadFile($file,'uploads/');
            }
          }

          

          $dado_id = $_REQUEST['id'];
          $this->model->alterData('atividades',$_POST,array('id' => $dado_id));
          die('sucesso;');
        }

        public function _actionVer() {

          $dado_id = $this->getParameter('3');
          $this->keys += $this->model->getOne('atividades',$dado_id);

          $cliente = $this->model->getList("cliente","id","nome");
            $this->keys['select_cliente'] = $this->html->select(false, $cliente, 'cliente_id',$this->keys['cliente_id'],1,'Selecione');$estabelecimento = $this->model->getList("estabelecimento","id","nome");
            $this->keys['select_estabelecimento'] = $this->html->select(false, $estabelecimento, 'estabelecimento_id',$this->keys['estabelecimento_id'],1,'Selecione');

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

          $this->redirect("/atividades/listar");
        }

        public function _actionLimpafiltros() {
          $modulo = $this->getParameter('1');
          unset($_SESSION['filtros'][$modulo]);
          $this->redirect("/atividades/listar");
        }
      }
?>