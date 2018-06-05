<?php
      class estabelecimento extends simplePHP {

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

            $this->keys['pageTitle'] = Estabelecimento;

            $usuario = $this->model->getOne('usuario',$_SESSION['usuario_id']);
            $this->keys['usernameMaster'] = $_SESSION['usuario'];
            $this->keys['cliente_menu'] = $this->core->loadMenu();

            $this->keys['activeestabelecimento'] = 'active';



        }

        public function _actionStart() {
          $this->redirect('/estabelecimento/listar');
          return $this->keys;
        }
        public function _actionListar() {

          #dados do tipo
              $tipo = array(''=>'');
              $this->keys['select_tipo'] = $this->html->select(false, $tipo, 'tipo_id',$this->keys['tipo_id'],0,'Selecione');

              

          $steper = 15;
          $modulo = $this->getParameter('1');
          $page = ($this->getParameter('3') != '') ? $this->getParameter('3') : 1;

          $total = $this->model->countData('estabelecimento',$_SESSION['filtros']['estabelecimento']);

          $this->keys['paginacao'] = $this->ui->pager($steper,$total,$page,'goUrl');

          $limits['limit'] = $steper;
          $limits['start'] = $this->calculaStartPaginacao($page,$steper);

          $dados = $this->model->getData('estabelecimento','*',$_SESSION['filtros']['estabelecimento'],$limits);

          if($_SESSION['filtros']['estabelecimento'] != '') {
            $this->keys['limpar'] = '<a href="/estabelecimento/limpafiltros" class="btn btn-info"><i class="glyphicon glyphicon-zoom-out" aria-hidden="true"></i> Limpar</a>';
            $this->keys['filtroativo'] = 'filtroativo';
          } else {
            $this->keys['limpar'] = '';
            $this->keys['filtroativo'] = '';
          }

          if($dados[0]['result'] != 'empty') {
              $tabela[0]['id'] = '#ID';
              $tabela[0]['nome'] = 'Nome';
             $tabela[0]['endereco'] = 'Endereco';
             $tabela[0]['telefone'] = 'Telefone';
             $tabela[0]['celular'] = 'Celular';
             $tabela[0]['tipo'] = 'Tipo';
             $tabela[0]['recomendacoes'] = 'Recomendacoes';
             
              $tabela[0]['acoes'] = 'Ações';
              $x = 1;
              foreach($dados as $dado) {
                $tabela[$x]['id'] = $dado['id'];
                $tabela[$x]['nome'] = $dado['nome'];
                  $tabela[$x]['endereco'] = $dado['endereco'];
                  $tabela[$x]['telefone'] = $dado['telefone'];
                  $tabela[$x]['celular'] = $dado['celular'];
                  $tabela[$x]['tipo'] = $dado['tipo'];
                  $tabela[$x]['recomendacoes'] = $dado['recomendacoes'];
                  
                $tabela[$x]['acoes'] = $this->html->link('Ver',"/estabelecimento/ver/$dado[id]",'','btn btn-info btn-xs');

                $x++;
              }
              $this->keys['tabela'] = $this->html->table($tabela,array('class'=>'table table-bordered table-condensed table-hover table-striped upper tabela-listar ','id'=>'lista-estabelecimento'),true,'','',true);
          } else {
              $this->keys['tabela'] = $this->html->div('Não foram encontrados estabelecimento cadastrados  ',array('class'=>'center'));
          }

          #aplica filtros
          foreach($_SESSION['filtros'][$modulo] as $key => $value) {
            $key = str_replace('like','',$key);
            $this->keys['filtro_'.trim($key)] = $value;
          }
          return $this->keys;
        }
        public function _actionInserir() {
          $tipo = array(
            'Evento' => 'Evento',
            'Hotel' => 'Hotel',
            'Restaurante' => 'Restaurante'
          );
            $this->keys['select_tipo'] = $this->html->select(false, $tipo, 'tipo_id',$this->keys['tipo_id'],1,'Selecione');#imagem
                $this->keys['imagem'] = 'http://via.placeholder.com/500x375/';
              
          return $this->keys;
        }
        public function _actionGrava() {
          foreach ($_FILES as $key => $file) {
            if($file['tmp_name'] != '') {
              $_POST[$key] = $_SERVER['HTTP_ORIGIN'].'/uploads/'.$this->file->uploadFile($file,'uploads/');
            }
          }

          

          $_POST['usuario_id'] = $_SESSION['usuario_id'];
          $this->model->addData('estabelecimento',$_POST,true);
          die('sucesso;');
        }

        public function _actionAltera() {

          foreach ($_FILES as $key => $file) {
            if($file['tmp_name'] != '') {
              $_POST[$key] = $this->file->uploadFile($file,'uploads/');
            }
          }

          

          $dado_id = $_REQUEST['id'];
          $this->model->alterData('estabelecimento',$_POST,array('id' => $dado_id));
          die('sucesso;');
        }

        public function _actionVer() {

          $dado_id = $this->getParameter('3');
          $this->keys += $this->model->getOne('estabelecimento',$dado_id);

          $tipo = array(''=>'');
            $this->keys['select_tipo'] = $this->html->select(false, $tipo, 'tipo_id',$this->keys['tipo'],1,'Selecione');#imagem
            if($this->keys['imagem'] != '') {
              $this->keys['imagem'] = '/uploads/'.$this->keys['imagem'];
            } else {
              $this->keys['imagem'] = 'http://via.placeholder.com/200x200/';

            }
            

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

          $this->redirect("/estabelecimento/listar");
        }

        public function _actionLimpafiltros() {
          $modulo = $this->getParameter('1');
          unset($_SESSION['filtros'][$modulo]);
          $this->redirect("/estabelecimento/listar");
        }
      }
?>