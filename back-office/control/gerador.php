<?php
/**
 * Project: Alphacode
 * API
 * @copyright Alphacode It Solutions - http://www.alphacode.com.br
 * @author Rafael Franco <rafael@alphacode.com.br>
 */

//include '../modules/aws-autoloader.php';
//use Aws\Sns\SnsClient;

class gerador extends simplePHP {

    #initialize vars
    private $model;
    private $core;
    private $image;
    private $file;
    private $payzen;
    private $util;

    /**
    *   Construtor
    **/
    public function __construct() {
        global $keys;

        #load model module
        $this->model = $this->loadModule('model');
        $this->util = $this->loadModule('util');
        $this->image = $this->loadModule('image');
        $this->file = $this->loadModule('file');
        $this->zip = $this->loadModule('zip');


        #load core module
        $this->core = $this->loadModule('core','',true);

        $_SESSION['conta_id'] = 1;

        #footer
        $this->keys['footer'] = $this->includeHTML('../view/admin/footer.html');

        #topheader
        $this->keys['topheader'] =  $this->includeHTML('../view/admin/topheader.html');

        #menu
        $this->keys['menu'] =  $this->includeHTML('../view/admin/menu.html');

        $usuario = $this->model->getOne('usuario',$_SESSION['usuario_id']);
        $this->keys['usernameMaster'] = $_SESSION['usuario'];
        $this->keys['userAvatar']  = $usuario['avatar'];

        $this->keys['pageTitle'] = SOFTWAREVERSION;

        $this->keys['software_name'] = $_SESSION['conta']['nome'];

        $this->keys['cliente_menu'] = $this->core->loadMenu();


    }

    public function _actionStart() {
        return $this->keys;
    }
    public function _actionBase() {

        return $this->keys;
    }

    public function _actionProject() {

      extract($_REQUEST);

      mkdir('tmp/back-office/', 0777);
      mkdir('tmp/back-office/public/', 0777);
      mkdir('tmp/back-office/public/images/', 0777);
      mkdir('tmp/back-office/public/css/', 0777);
      mkdir('tmp/back-office/public/js/', 0777);
      mkdir('tmp/back-office/public/fonts/', 0777);
      mkdir('tmp/back-office/public/js/hotsite/', 0777);
      mkdir('tmp/back-office/config/', 0777);
      mkdir('tmp/back-office/control/', 0777);
      mkdir('tmp/back-office/view/', 0777);
      mkdir('tmp/back-office/view/hotsite/', 0777);
      mkdir('tmp/back-office/view/admin/', 0777);
      mkdir('tmp/back-office/view/helpers/', 0777);
      mkdir('tmp/back-office/view/gerador/', 0777);


      //grava o Logotipo
      move_uploaded_file($_FILES['logo']['tmp_name'], 'tmp/back-office/public/images/logo.png');
      $string = "<?php
      #import define
      include '../define.php';?>";
      $file = fopen('tmp/back-office/public/index.php','w+');
      fwrite($file,$string);
      $file = fopen('tmp/back-office/public/action.php','w+');
      fwrite($file,$string);
      $file = fopen('tmp/back-office/public/admin.php','w+');
      fwrite($file,$string);
      $file = fopen('tmp/back-office/public/gerador.php','w+');
      fwrite($file,$string);

      #define
      $string = file_get_contents('padrao/define.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/define.php','w+');
      fwrite($file,$string);

      #envinroments.php
      $string = file_get_contents('padrao/envinroments.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/config/envinroments.php','w+');
      fwrite($file,$string);

      #db.php
      $string = file_get_contents('padrao/db.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/config/db.php','w+');
      fwrite($file,$string);

      #hotsite
      $string = file_get_contents('padrao/hotsite.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/control/hotsite.php','w+');
      fwrite($file,$string);

      #action
      $string = file_get_contents('padrao/action.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/control/action.php','w+');
      fwrite($file,$string);

      #admin
      $string = file_get_contents('padrao/admin.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/control/admin.php','w+');
      fwrite($file,$string);

      #gerador
      $string = file_get_contents('../control/gerador.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/control/gerador.php','w+');
      fwrite($file,$string);

      #core
      $string = file_get_contents('padrao/core.php');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/control/core.php','w+');
      fwrite($file,$string);

      #hotsite/start
      $string = file_get_contents('padrao/hotsite/start.html');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/view/hotsite/start.html','w+');
      fwrite($file,$string);

      $string = file_get_contents('../view/gerador/modulo.html');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/view/gerador/modulo.html','w+');
      fwrite($file,$string);

      #admin/start
      $string = file_get_contents('padrao/admin/start.html');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/view/admin/start.html','w+');
      fwrite($file,$string);

      #helpers/topheader
      $string = file_get_contents('padrao/helpers/topheader.html');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/view/helpers/topheader.html','w+');
      fwrite($file,$string);

      #.htaccess
      $string = file_get_contents('.htaccess');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/public/.htaccess','w+');
      fwrite($file,$string);

      #helpers/footer
      $string = file_get_contents('padrao/helpers/footer.html');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/view/helpers/footer.html','w+');
      fwrite($file,$string);

      #helpers/topheader
      $string = file_get_contents('padrao/helpers/menu.html');
      $string = $this->applyKeys($string,$_REQUEST);
      $file = fopen('tmp/back-office/view/helpers/menu.html','w+');
      fwrite($file,$string);

      #fonts
      $files[] = $this->geraPath('fonts/FontAwesome.otf');
      $files[] = $this->geraPath('fonts/fontawesome-webfont.eot');
      $files[] = $this->geraPath('fonts/fontawesome-webfont.svg');
      $files[] = $this->geraPath('fonts/fontawesome-webfont.ttf');
      $files[] = $this->geraPath('fonts/fontawesome-webfont.woff');
      $files[] = $this->geraPath('fonts/fontawesome-webfont.woff2');

      #css
      $files[] = $this->geraPath('css/dropzone.css');
      $files[] = $this->geraPath('css/sweetalert.css');
      $files[] = $this->geraPath('css/font-awesome.min.css');
      $files[] = $this->geraPath('css/sb-admin.css');
      $files[] = $this->geraPath('css/toastr.css');
      $files[] = $this->geraPath('css/bootstrap-datetimepicker.min.css');
      $files[] = $this->geraPath('css/bootstrap.min.css');
      $files[] = $this->geraPath('css/jquery-ui.min.css');
      $files[] = $this->geraPath('css/morris.css');

      #js
      $files[] = $this->geraPath('js/jquery.js');
      $files[] = $this->geraPath('js/jquery.mask.min.js');
      $files[] = $this->geraPath('js/moment.js');
      $files[] = $this->geraPath('js/dropzone.js');
      $files[] = $this->geraPath('js/jquery.maskMoney.js');
      $files[] = $this->geraPath('js/jquery-ui.min.js');
      $files[] = $this->geraPath('js/sweetalert.min.js');
      $files[] = $this->geraPath('js/global.js');
      $files[] = $this->geraPath('js/toastr.js');
      $files[] = $this->geraPath('js/locale_moment_pt-br.js');
      $files[] = $this->geraPath('js/bootstrap.min.js');
      $files[] = $this->geraPath('js/bootstrap-datetimepicker.min.js');
      $files[] = $this->geraPath('js/hotsite/start.js');




      #sql
      $string = file_get_contents('padrao/base.sql');

      foreach ($_REQUEST['modulo'] as $key => $modulo) {
        $chave = strtolower(removeAcentos($modulo));
        $modulos .= "(NULL,'$chave',0,1,1,'$modulo','<i class=\"fa fa-fw fa-dashboard\" aria-hidden=\"true\"></i>',NULL),";
      }
      $_REQUEST['modulos'] = $modulos;
      $sql = $this->applyKeys($string,$_REQUEST);

      $file = fopen('tmp/'.$banco.'.sql','w+');
      fwrite($file,$sql);

      #zipa arquivos

      $files[] = 'tmp/back-office/public/.htaccess';
      $files[] = 'tmp/back-office/public/index.php';
      $files[] = 'tmp/back-office/public/action.php';
      $files[] = 'tmp/back-office/public/admin.php';
      $files[] = 'tmp/back-office/public/gerador.php';
      $files[] = 'tmp/back-office/config/envinroments.php';
      $files[] = 'tmp/back-office/config/db.php';
      $files[] = 'tmp/back-office/control/hotsite.php';
      $files[] = 'tmp/back-office/control/action.php';
      $files[] = 'tmp/back-office/control/admin.php';
      $files[] = 'tmp/back-office/control/core.php';
      $files[] = 'tmp/back-office/control/gerador.php';
      $files[] = 'tmp/back-office/define.php';
      $files[] = 'tmp/back-office/view/hotsite/start.html';
      $files[] = 'tmp/back-office/view/admin/start.html';
      $files[] = 'tmp/back-office/view/helpers/topheader.html';
      $files[] = 'tmp/back-office/view/helpers/menu.html';
      $files[] = 'tmp/back-office/view/helpers/footer.html';
      $files[] = 'tmp/back-office/view/gerador/modulo.html';
      $files[] = 'tmp/back-office/public/images/logo.png';

      $files[] = 'tmp/'.$banco.'.sql';


      $file = fopen("tmp/$nome.zip",'w+');
      $this->zip->zip($files,"tmp/$nome.zip",true);
      exit;
    }

    public function _actionCreate(){

      $moduloUC = $_REQUEST['modulo'];
      $modulo = $this->prepara(($_REQUEST['modulo']));
      $x = 0;
      foreach ($_REQUEST['nome'] as $key => $campo) {
         $campo = removeAcentos(trim($campo));
         $campos .= $this->criaCampo($campo,$_REQUEST['tipo'][$key]);
         $x++;
      }

      $sql = "CREATE TABLE `$modulo` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `conta_id` int(11) DEFAULT '1',
              `usuario_id` int(11) DEFAULT '1',
              ".$campos."
              `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;";


      $this->model->sql($sql);

      $sql = "INSERT INTO `adm_modulos` (`id`, `nome`, `especifico`, `ordem`, `status`, `label`, `icone`) VALUES (NULL, '$modulo', 0, 1, 1, '$moduloUC', '<i class=\"fa fa-fw fa-dashboard\" aria-hidden=\"true\"></i>');";
      $this->model->sql($sql);


      $x = 0;
      foreach ($_REQUEST['nome'] as $key => $campo) {


         if($_REQUEST['listar'][$key] == 'on') {
             $camposListaTitulo .= '$tabela[0][\''.$campo.'\'] = \''.ucfirst($campo).'\';
             ';
             $campo = $this->prepara($campo);

             switch ($_REQUEST['tipo'][$key] ) {
               case 'select_tabela':
                 $camposLista .= '$tabela[$x][\''.$campo.'\'] = $'.$campo.'[$dado[\''.$campo.'_id\']];
                 ';
                 break;
               case 'data':
                 $camposLista .= '$tabela[$x][\''.$campo.'\'] = date(\'d/m/Y\',strtotime($dado[\''.$campo.'\']));
                 ';
                 break;

               default:
                  $camposLista .= '$tabela[$x][\''.$campo.'\'] = $dado[\''.$campo.'\'];
                  ';
                 break;
             }
         }
         $x++;
      }
      $string = '<?php
      class '.$modulo.' extends simplePHP {

        private $model;
        private $html;
        private $core;
        private $ui;
        private $util;
        private $file;

        public function __construct() {
    		    global $keys;

            #load model module
            $this->model = $this->loadModule(\'model\');
            $this->model->context = true;

            #load html module
            $this->html = $this->loadModule(\'html\');

            #load ui module
            $this->ui = $this->loadModule(\'ui\');

            #load file module
            $this->file = $this->loadModule(\'file\');

            #load util module
            $this->util = $this->loadModule(\'util\');

            #load core module
            $this->core = $this->loadModule(\'core\',\'\',true);

            #footer
            $this->keys[\'footer\'] = $this->includeHTML(\'../view/admin/footer.html\');

            #topheader
            $this->keys[\'topheader\'] =  $this->includeHTML(\'../view/admin/topheader.html\');
            $this->keys[\'header\'] =  $this->includeHTML(\'../view/admin/header.html\');
            $this->keys[\'topo\'] =  $this->includeHTML(\'../view/admin/topo.html\');

            #menu
            $this->keys[\'menu\'] =  $this->includeHTML(\'../view/admin/menu.html\');
            $this->keys[\'sidemenu\'] =  $this->includeHTML(\'../view/admin/sidemenu.html\');
            $this->keys[\'topmenu\'] =  $this->includeHTML(\'../view/admin/topmenu.html\');

            $this->keys[\'pageTitle\'] = '.ucfirst($modulo).';

            $usuario = $this->model->getOne(\'usuario\',$_SESSION[\'usuario_id\']);
            $this->keys[\'usernameMaster\'] = $_SESSION[\'usuario\'];
            $this->keys[\'cliente_menu\'] = $this->core->loadMenu();

            $this->keys[\'active'.$modulo.'\'] = \'active\';



        }

        public function _actionStart() {
          $this->redirect(\'/'.$modulo.'/listar\');
          return $this->keys;
        }
        public function _actionListar() {

          '.$this->comandosListar().'

          $steper = 15;
          $modulo = $this->getParameter(\'1\');
          $page = ($this->getParameter(\'3\') != \'\') ? $this->getParameter(\'3\') : 1;

          $total = $this->model->countData(\''.$modulo.'\',$_SESSION[\'filtros\'][\''.$modulo.'\']);

          $this->keys[\'paginacao\'] = $this->ui->pager($steper,$total,$page,\'goUrl\');

          $limits[\'limit\'] = $steper;
          $limits[\'start\'] = $this->calculaStartPaginacao($page,$steper);

          $dados = $this->model->getData(\''.$modulo.'\',\'*\',$_SESSION[\'filtros\'][\''.$modulo.'\'],$limits);

          if($_SESSION[\'filtros\'][\''.$modulo.'\'] != \'\') {
            $this->keys[\'limpar\'] = \'<a href="/'.$modulo.'/limpafiltros" class="btn btn-info"><i class="glyphicon glyphicon-zoom-out" aria-hidden="true"></i> Limpar</a>\';
            $this->keys[\'filtroativo\'] = \'filtroativo\';
          } else {
            $this->keys[\'limpar\'] = \'\';
            $this->keys[\'filtroativo\'] = \'\';
          }

          if($dados[0][\'result\'] != \'empty\') {
              $tabela[0][\'id\'] = \'#ID\';
              '.$camposListaTitulo.'
              $tabela[0][\'acoes\'] = \'Ações\';
              $x = 1;
              foreach($dados as $dado) {
                $tabela[$x][\'id\'] = $dado[\'id\'];
                '.$camposLista.'
                $tabela[$x][\'acoes\'] = $this->html->link(\'Ver\',"/'.$modulo.'/ver/$dado[id]",\'\',\'btn btn-info btn-xs\');

                $x++;
              }
              $this->keys[\'tabela\'] = $this->html->table($tabela,array(\'class\'=>\'table table-bordered table-condensed table-hover table-striped upper tabela-listar \',\'id\'=>\'lista-'.$modulo.'\'),true,\'\',\'\',true);
          } else {
              $this->keys[\'tabela\'] = $this->html->div(\'Não foram encontrados '.$modulo. ' cadastrados  \',array(\'class\'=>\'center\'));
          }

          #aplica filtros
          foreach($_SESSION[\'filtros\'][$modulo] as $key => $value) {
            $key = str_replace(\'like\',\'\',$key);
            $this->keys[\'filtro_\'.trim($key)] = $value;
          }
          return $this->keys;
        }
        public function _actionInserir() {
          '.$this->comandosInserir().'
          return $this->keys;
        }
        public function _actionGrava() {
          foreach ($_FILES as $key => $file) {
            if($file[\'tmp_name\'] != \'\') {
              $_POST[$key] = $this->file->uploadFile($file,\'uploads/\');
            }
          }

          '.$this->comandosGravar().'

          $_POST[\'usuario_id\'] = $_SESSION[\'usuario_id\'];
          $this->model->addData(\''.$modulo.'\',$_POST,true);
          die(\'sucesso;\');
        }

        public function _actionAltera() {

          foreach ($_FILES as $key => $file) {
            if($file[\'tmp_name\'] != \'\') {
              $_POST[$key] = $this->file->uploadFile($file,\'uploads/\');
            }
          }

          '.$this->comandosAlterar().'

          $dado_id = $_REQUEST[\'id\'];
          $this->model->alterData(\''.$modulo.'\',$_POST,array(\'id\' => $dado_id));
          die(\'sucesso;\');
        }

        public function _actionVer() {

          $dado_id = $this->getParameter(\'3\');
          $this->keys += $this->model->getOne(\''.$modulo.'\',$dado_id);

          '.$this->comandosVer().'

          return $this->keys;
        }

        public function _actionFiltrar() {
          $modulo = $this->getParameter(\'1\');

          foreach ($_POST as $key => $valueTxt) {
            $key = str_replace(\'like_\',\'like \',$key);
            if($valueTxt != \'\') {
              $_SESSION[\'filtros\'][$modulo][$key] = $valueTxt;
            }
            if($valueTxt == \'\') {
              unset($_SESSION[\'filtros\'][$modulo][$key]);
            }

            if($_SESSION[\'filtros\'][$modulo][$key] == \'0\') {
              unset($_SESSION[\'filtros\'][$modulo][$key]);
            }
          }

          $this->redirect("/'.$modulo.'/listar");
        }

        public function _actionLimpafiltros() {
          $modulo = $this->getParameter(\'1\');
          unset($_SESSION[\'filtros\'][$modulo]);
          $this->redirect("/'.$modulo.'/listar");
        }
      }
?>';

      //$file = fopen('tmp/'.$modulo.'.php','w+');
      $file = fopen('../control/'.$modulo.'.php','w+');
      fwrite($file,$string);
      //echo $string;
      $filtros = $this->criaFiltros($_REQUEST);
      $string = '<!doctype html>
      <html >
        [#header#]
        <body>
          [#topo#]
          <div class="container-fluid">
            <div class="row">
              [#sidemenu#]

              <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                <div class="row">
                  <div class="col-md-11"><h1 class="page-header">'.ucfirst($modulo).'</h1></div>
                  <div class="col-md-1"><a href="/'.$modulo.'/inserir"><button type="button" data-tip="Cadastrar" class="btn btn-success tool btn-lg btn-block"><span class=" glyphicon glyphicon-plus" aria-hidden="true"></span></button></a></div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h3 class="panel-title"> <span class="glyphicon glyphicon-search"></span> Pesquisar</h3>
                        </div>
                        <div class="panel-body [#filtroativo#]">
                          '.$filtros.'
                        </div>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="table-responsive">
                        [#tabela#]
                    </div>
                  </div>
                </div>
                <div class="row">
                    <div class="center col-md-12">
                        [#paginacao#]
                    </div>
                </div>

              </div>
            </div>
          </div>
          [#footer#]
      </body>
      </html>';
      //$file = fopen('tmp/listar.html','w+');
      mkdir("../view/".$modulo, 0777);
      $file = fopen('../view/'.$modulo.'/listar.html','w+');
      fwrite($file,$string);

      $campos = '';
      $x = 0;

      foreach ($_REQUEST['nome'] as $key => $campo) {
         if($_REQUEST['obrigatorio'][$key] == 'on') {
           $campos .= $this->criaCampoInsert($campo,$_REQUEST['tipo'][$key],$modulo,true);
         } else {
           $campos .= $this->criaCampoInsert($campo,$_REQUEST['tipo'][$key],$modulo,false);
         }
         $x++;
      }

      //$file = fopen('tmp/ver.html','w+');

      $string = '<!doctype html>
      <html >
        [#header#]
        <body>
          [#topo#]
          <div class="container-fluid">
            <div class="row">
              [#sidemenu#]

              <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                <div class="row">
                  <div class="col-md-11"><h1 class="page-header">'.ucfirst($modulo).'</h1></div>
                  <div class="col-md-1"><a href="/'.$modulo.'"><button type="button" data-tip="Voltar"  onclick="goBack()" class="btn tool btn-lg btn-block"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></button></a></div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h3 class="panel-title"> <span class="glyphicon glyphicon-plus"></span> Cadastrar '.$this->singular($modulo).'</h3>
                        </div>
                        <div class="panel-body">
                          <form id="form-add" method="POST" class="form-adicionar" enctype="multipart/form-data">
                            <div class="row">
                            '.$campos.'
                            </div>
                          </form>
                        </div>
                        <div class="panel-footer text-right">
                          <button id="salva" class="btn  btn-success">Inserir</button>
                        </div>
                      </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          [#footer#]
      </body>
      <script>
      $(\'#salva\').on(\'click\', function(){
          errors = 0;

          $(\'.required\').each(function(index, el) {
              errors += validateEmpty(el[\'id\']);
          });

          if (errors == 0) {
              $.ajax({
                 url: \'/'.$modulo.'/grava/\',
                 cache: false,
                 contentType: false,
                 processData: false,
                 type: \'POST\',
                 data: new FormData(document.getElementById(\'form-add\'))
              })

             .success(function(data) {
                  data = data.split(\';\');
                  if(data[0] == \'sucesso\'){
                      swal({
                          title: "Sucesso!",
                          text: \''.ucfirst($modulo).' adicionado com sucesso!\',
                          type: "success",
                          showConfirmButton: true
                      },
                      function(){
                          window.location = \'/'.$modulo.'\';
                      });
                  } else {
                      swal({
                          title: "Ops..!",
                          text: "Tivemos um pequeno problema, tente novamente!",
                          type: "error",
                          showConfirmButton: true
                      },
                      function(){

                      });
                  }
              })
          } else {
              swal("Ops..!", "Parece que você esqueceu de preencher algum campo!", "error");
          }
      });
      </script>
      </html>';

      $file = fopen('../view/'.$modulo.'/inserir.html','w+');
      fwrite($file,$string);

      $campos = '';
      $x = 0;
      foreach ($_REQUEST['nome'] as $key => $campo) {
         if($_REQUEST['obrigatorio'][$key] == 'on') {
           $campos .= $this->criaCampoInsert($campo,$_REQUEST['tipo'][$key],$modulo,true,'edita');
         } else {
           $campos .= $this->criaCampoInsert($campo,$_REQUEST['tipo'][$key],$modulo,false,'edita');
         }

         $x++;
      }

      $string = '<html >
        [#header#]
        <body>
          [#topo#]
          <div class="container-fluid">
            <div class="row">
              [#sidemenu#]

              <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                <div class="row">
                  <div class="col-md-11"><h1 class="page-header">'.ucfirst($modulo).'</h1></div>
                  <div class="col-md-1"><a href="/'.$modulo.'"><button type="button" data-tip="Voltar"  onclick="goBack()" class="btn tool btn-lg btn-block"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></button></a></div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h3 class="panel-title"> <span class="glyphicon glyphicon-plus"></span> Ver '.$this->singular($modulo).'</h3>
                        </div>
                        <div class="panel-body">
                          <form id="form-alt" method="POST" class="form-adicionar" enctype="multipart/form-data">
                          <input type="hidden" name="id" value="[#id#]" />
                            <div class="row">
                            '.$campos.'
                            </div>
                          </form>
                        </div>
                        <div class="panel-footer text-right">
                          <button id="desbloqueia" class="btn btn-success">Alterar</button>
                          <button id="salva" class="btn btn-success">Salvar</button>
                        </div>
                      </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
          [#footer#]
      </body>

      <script>
      $(\'#salva\').hide();
      $(\'#desbloqueia\').on(\'click\', function(){
        $(\'input\').removeAttr(\'disabled\')
        $(\'textarea\').removeAttr(\'disabled\')
        $(\'#salva\').show();
        $(\'#desbloqueia\').hide();
      });

      $(\'#salva\').on(\'click\', function(){
          errors = 0;

          $(\'.required\').each(function(index, el) {
              errors += validateEmpty(el[\'id\']);
          });

          if (errors == 0) {
              $.ajax({
                 url: \'/'.$modulo.'/altera/\',
                 cache: false,
                 contentType: false,
                 processData: false,
                 type: \'POST\',
                 data: new FormData(document.getElementById(\'form-alt\'))
              })

             .success(function(data) {
                  data = data.split(\';\');
                  if(data[0] == \'sucesso\'){
                      swal({
                          title: "Sucesso!",
                          text: \''.ucfirst($modulo).' alterado com sucesso!\',
                          type: "success",
                          showConfirmButton: true
                      },
                      function(){
                          window.location = \'/'.$modulo.'\';
                      });
                  } else {
                      swal({
                          title: "Ops..!",
                          text: "Tivemos um pequeno problema, tente novamente!",
                          type: "error",
                          showConfirmButton: true
                      },
                      function(){

                      });
                  }
              })
          } else {
              swal("Ops..!", "Parece que você esqueceu de preencher algum campo!", "error");
          }
      });
      </script>
      ';

      $file = fopen('../view/'.$modulo.'/ver.html','w+');
      fwrite($file,$string);


//$file = fopen('tmp/ver.html','w+');
      $string = '<?php include(\'index.php\'); ?>';
      $file = fopen($modulo.'.php','w+');
      fwrite($file,$string);
//exit;
      $string = '';
      $file = fopen('tmp/ver.html','w+');
      fwrite($file,$string);

      $string = '';
      $file = fopen('tmp/editar.html','w+');
      fwrite($file,$string);

      $file = fopen('tmp/'.$modulo.'.zip','w+');
      fwrite($file,$string);

      $files[] = 'tmp/listar.html';
      $files[] = 'tmp/ver.html';
      $files[] = 'tmp/editar.html';
      $files[] = 'tmp/'.$modulo.'.php';

      $this->zip->zip($files,'tmp/'.$modulo.'.zip',true);

      exit;
      //die('x');
    }

    private function criaCampo($nome,$tipo) {
      $nome = removeAcentos($nome);
      switch ($tipo) {
        case 'nome':
          $tipagem = 'varchar(255)';
          break;
        case 'email':
          $tipagem = 'varchar(255)';
          break;
        case 'cidade':
          $tipagem = 'varchar(255)';
          break;
        case 'estado':
          $tipagem = 'varchar(2)';
          break;
        case 'cpf':
          $tipagem = 'varchar(20)';
          break;
        case 'cnpj':
          $tipagem = 'varchar(20)';
          break;
        case 'celular':
          $tipagem = 'varchar(11)';
          break;
        case 'numero':
          $tipagem = 'int(11)';
          break;
        case 'cep':
          $tipagem = 'varchar(9)';
          break;
        case 'telefone':
          $tipagem = 'varchar(10)';
          break;
        case 'data':
          $tipagem = 'DATE';
          break;
        case 'texto':
          $tipagem = 'TEXT';
          break;
        case 'select_tabela':
          $nome = $nome.'_id';
          $tipagem = 'int(11)';
          break;

        default:
          $tipagem = 'varchar(255)';
          break;
      }
      $nome = strtolower($nome);
      $nome = str_replace(' ','_',$nome);
      return "`".$nome."` ".$tipagem." ,";
    }

    private function criaCampoInsert($nome,$tipo,$modulo,$obrigatorio,$operacao = 'insere') {
      $nome = trim($nome);
      $nome_input = strtolower(removeAcentos($nome));
      $nome_input = str_replace(' ','_',$nome_input);

      $required = ($obrigatorio) ?  'required' : '';
      $asterisco = ($obrigatorio) ?  '*' : '';

      if($operacao == 'edita') {
        $value = "[#$nome_input#]";
        $disabled = "disabled";
      }

      $moduloSingular = $this->singular($modulo);

      switch ($tipo) {
        case 'avatar':
          $html = '<div class="col-lg-12 center">
              <img width="200" src="[#'.$nome_input.'#]" id="img'.$nome_input.'" /><br><br>
              <input type="file" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control hidden '.$required.'" >'.
              "<script>
                $('#img$nome_input').click(function(){
                  $('#$nome_input').trigger('click');
                })
                $('#$nome_input').change(function(){

                  var file  = this.files[0];
                  var img = new Image();

                  var objectURL = URL.createObjectURL(file);
                  $('#img$nome_input').attr('src',objectURL);

                })
              </script>
          </div>";
          break;
        case 'nome':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
        case 'senha':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="password" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
        case 'reais':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').maskMoney({showSymbol:true, symbol:'R$ ', decimal:',', thousands:'.'});</script>";
          break;
        case 'oculto':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="hidden" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
        case 'texto':

          $html = '<div class="col-lg-12">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <textarea rows="5" '.$disabled.' name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">'.$value.'</textarea>
          </div>';
          break;
        case 'email':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
        case 'cidade':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
        case 'estado':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <select name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" >
                [#select_'.$nome_input.'#]
              </select>
          </div>';
          break;
        case 'select':
          $html .= '<div class="col-md-4">
          <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <select name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" >
                [#select_'.$nome_input.'#]
              </select>
          </div>';
          break;
        case 'select_tabela':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <select name="'.$nome_input.'_id" id="'.$nome_input.'_id" class="form-control '.$required.'" >
                [#select_'.$nome_input.'#]
              </select>
          </div>';
          break;
        case 'status':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <select name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" >
                <option>Ativo</option>
                <option>Inativo</option>
              </select>
          </div>';
          break;
        case 'cpf':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('000.000.000-00');</script>";
          break;
        case 'cnpj':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('00.000.000/0000-00');</script>";
          break;
        case 'celular':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('00 00000-0000');</script>";
          break;
        case 'cartao':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('0000 0000 0000 0000');</script>";
          break;
        case 'validade':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('00/00');</script>";
          break;
        case 'numero':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
        case 'cep':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>
            $('#$nome_input').mask('00000-000');
            $('#$nome_input').blur(function(){
        	   	$.ajax({
        	       	url: 'http://cep.alphacode.com.br/action/cep/'+$('#$nome_input').val(),
        	       	type: 'GET',
        	       	success: function(json) {
        	         	data = $.parseJSON(json)['success'];
        	         	$('#endereco').val(data.tp_logradouro+' '+data.logradouro);
        	         	$('#bairro').val(data.bairro);
        	         	$('#cidade').val(data.cidade);
        	        	$('#estado').val(data.uf.toUpperCase());
        	        }
        	   	})
        	  })
          </script>";
          break;
        case 'telefone':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control required" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('0000-0000');</script>";
          break;
        case 'data':
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control required" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          $html .="<script>$('#$nome_input').mask('00/00/0000');</script>";
          break;

        default:
          $html = '<div class="col-md-4">
              <label for="'.$nome_input.'">'.$nome.$asterisco.'</label>
              <input '.$disabled.' type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control required" placeholder="'.$nome.' do '.$moduloSingular.'">
          </div>';
          break;
      }
      return $html;
    }

    private function criaFiltros($campos) {

      $html = '';
      $modulo = strtolower(removeAcentos($_REQUEST['modulo']));

      foreach ($_REQUEST['nome'] as $posicao => $campo) {
        $nome = $campo;
        $nome_input = $this->prepara($campo);

        $tipo = $_REQUEST['tipo'][$posicao];
        $value = "[#filtro_$nome_input#]";

        if($_REQUEST['filtro'][$posicao] == 'on') {
            switch ($tipo) {
              case 'email':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                break;
              case 'cidade':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                break;
              case 'estado':
                $html .= '<div class="col-md-4" >
                    <select name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" >
                      [#select_'.$nome_input.'#]
                    </select>
                </div>';
                break;
              case 'select':
                $html .= '<div class="col-md-4" >
                    <select name="'.$nome_input.'" id="'.$nome_input.'"  class=" form-control '.$required.'" >
                    <option value="">'.$campo.'</option>
                    [#select_'.$nome_input.'#]
                    </select>
                </div>';
                break;
              case 'select_tabela':
                $html .= '<div class="col-md-4" >
                    <select name="'.$nome_input.'_id" id="'.$nome_input.'" class="form-control '.$required.'" >
                      <option value="">'.$campo.'</option>
                      [#select_'.$nome_input.'#]
                    </select>
                </div>';
                break;
              case 'status':
                $html .= '<div class="col-md-4" >
                    <select name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" >
                      <option value="">'.$campo.'</option>
                      <option [#statusAtivo#]> Ativo</option>
                      <option [#statusInativo#]> Inativo</option>
                    </select>
                </div>';
                break;
              case 'cpf':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                $html .="<script>$('#$nome_input').mask('000.000.000-00');</script>";
                break;

              case 'cnpj':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                $html .="<script>$('#$nome_input').mask('00.000.000/0000-00');</script>";
                break;

              case 'celular':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                $html .="<script>$('#$nome_input').mask('00 00000-0000');</script>";
                break;
              case 'cartao':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                break;
              case 'validade':
                $html .= '<div class="col-md-4" >

                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                $html .="<script>$('#$nome_input').mask('00/00');</script>";
                break;
              case 'numero':
                $html .= '<div class="col-md-4" >

                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                break;
              case 'cep':
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                $html .="<script>$('#$nome_input').mask('00000-000');</script>";
                break;
              case 'telefone':
                $html .= '<div class="col-md-4" >

                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control required" placeholder="'.$nome.' ">
                </div>';
                break;
              case 'data':
                $html .= '<div class="col-md-4" >

                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control required" placeholder="'.$nome.' ">
                </div>';
                break;
              case 'nome':
                $html .= '<div class="col-md-4" >

                    <input type="text" value="'.$value.'" name="like '.$nome_input.'" id="'.$nome_input.'" class="form-control required" placeholder="'.$nome.' ">
                </div>';
                break;

              default:
                $html .= '<div class="col-md-4" >
                    <input type="text" value="'.$value.'" name="'.$nome_input.'" id="'.$nome_input.'" class="form-control '.$required.'" placeholder="'.$nome.' ">
                </div>';
                break;
            }
          }
      }
      if($html != '') {
        $html = '<form class="form-busca" method="post" action="/'.$modulo.'/filtrar"  >
                  <div class="col-lg-11" ><div class="row">'.$html.'</div></div>
                  <div class="col-lg-1">
                      <button style="margin-bottom:10px;" type="submit" class="btn btn-success"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
                      [#limpar#]
                  </div>
                </form>
          <hr><br>';
      }

      return $html;
    }

    private function comandosGravar() {

      foreach ($_REQUEST['tipo'] as $key => $value) {
        $nome = $this->prepara($_REQUEST['nome'][$key]);
        switch ($value) {
          case 'senha':

            $retorno .= '$_POST[\''.$nome.'\'] = md5($_POST[\''.$nome.'\']);';
            break;
          case 'data':

            $retorno .= '$_POST[\''.$nome.'\'] = date(\'Y-m-d\',strtotime($_POST[\''.$nome.'\']));';
          break;
        }
      }
      return $retorno;
    }

    private function comandosAlterar() {

      foreach ($_REQUEST['tipo'] as $key => $value) {
        $nome = $this->prepara($_REQUEST['nome'][$key]);
        switch ($value) {
          case 'senha':

            $retorno .= '$_POST[\''.$nome.'\'] = md5($_POST[\''.$nome.'\']);';
            break;
          case 'data':

            $retorno .= '$_POST[\''.$nome.'\'] = date(\'Y-m-d\',strtotime($_POST[\''.$nome.'\']));';
          break;
        }
      }
      return $retorno;
    }

    private function comandosInserir() {

      foreach ($_REQUEST['tipo'] as $key => $value) {
        switch ($value) {
          case 'estado':
            $retorno .= '$this->keys[\'select_'.$value.'\'] = $this->html->select(false, $this->util->ufs(), \''.$value.'\',\'\');';
            break;

          case 'select_tabela':
            $tabela = $_REQUEST['opcoes'][$key];
            $nome = $this->prepara($_REQUEST['nome'][$key]);

            $retorno .= '$'.$nome . ' = $this->model->getList("'.$tabela.'","id","nome");
            $this->keys[\'select_'.$nome.'\'] = $this->html->select(false, $'.$nome.', \''.$value.'\',\'\');';
            break;
          case 'avatar':
              $nome = $this->prepara($_REQUEST['nome'][$key]);
              $retorno .= '#imagem
                $this->keys[\''.$nome.'\'] = \'http://via.placeholder.com/200x200/\';
              ';
          break;
          case 'select':
            $tabela = $_REQUEST['opcoes'][$key];
            $nome = $this->prepara($_REQUEST['nome'][$key]);

            $options = explode(',',$_REQUEST['opcoes'][$key]);

            $opcoesArray = '';
            foreach ($options as $key => $value) {
              $opcoesArray .= "'$value'=>'$value',";
            }

            $opcoesArray = $this->cutEnd($opcoesArray,strlen($opcoesArray)-1);

            $retorno .= '$'.$nome . ' = array('.$opcoesArray.');
            $this->keys[\'select_'.$nome.'\'] = $this->html->select(false, $'.$nome.', \''.$nome.'_id\',$this->keys[\''.$nome.'_id\'],1,\'Selecione\');';

            break;
          default:
            # code...
            break;
        }
      }
      return $retorno;
    }

    private function comandosVer() {

      foreach ($_REQUEST['tipo'] as $key => $value) {
        switch ($value) {
          case 'estado':
            $nome = $this->prepara($_REQUEST['nome'][$key]);
            $retorno .= '$this->keys[\'select_'.$value.'\'] = $this->html->select(false, $this->util->ufs(), \''.$nome.'\',$this->keys[\''.$nome.'\']);';
            break;
          case 'avatar':
            $nome = $this->prepara($_REQUEST['nome'][$key]);
            $retorno .= '#imagem
            if($this->keys[\''.$nome.'\'] != \'\') {
              $this->keys[\''.$nome.'\'] = \'/uploads/\'.$this->keys[\''.$nome.'\'];
            } else {
              $this->keys[\''.$nome.'\'] = \'http://via.placeholder.com/200x200/\';

            }
            ';
            break;

          case 'select_tabela':
            $tabela = $_REQUEST['opcoes'][$key];
            $nome = $this->prepara($_REQUEST['nome'][$key]);

            $retorno .= '$'.$nome . ' = $this->model->getList("'.$tabela.'","id","nome");
            $this->keys[\'select_'.$nome.'\'] = $this->html->select(false, $'.$nome.', \''.$nome.'_id\',$this->keys[\''.$nome.'_id\'],1,\'Selecione\');';

            break;
          case 'select':
            $tabela = $_REQUEST['opcoes'][$key];
            $nome = $this->prepara($_REQUEST['nome'][$key]);

            $options = explode(',',$_REQUEST['opcoes'][$key]);

            $opcoesArray = '';
            foreach ($options as $key => $value) {
              $opcoesArray .= "'$value'=>'$value',";
            }

            $opcoesArray = $this->cutEnd($opcoesArray,strlen($opcoesArray)-1);

            $retorno .= '$'.$nome . ' = array('.$opcoesArray.');
            $this->keys[\'select_'.$nome.'\'] = $this->html->select(false, $'.$nome.', \''.$nome.'_id\',$this->keys[\''.$nome.'\'],1,\'Selecione\');';

            break;

          default:
            # code...
            break;
        }
      }


      return $retorno;
    }

    private function comandosListar() {

      foreach ($_REQUEST['tipo'] as $key => $value) {
        switch ($value) {
          case 'estado':
            $retorno .= '#dados do estado
            $this->keys[\'select_'.$value.'\'] = $this->html->select(false, $this->util->ufs(), \''.$value.'\',\'\');

            ';
            break;

          case 'select_tabela':
            $tabela = $_REQUEST['opcoes'][$key];
            $nome = $this->prepara($_REQUEST['nome'][$key]);
            $modulo = $this->prepara($_REQUEST['modulo']);

            $retorno .= '#dados do '.$nome.'
            $'.$nome.' = $this->model->getList("'.$tabela.'","id","nome");
            $this->keys[\'select_'.$nome.'\'] = $this->html->select(false, $'.$nome.', \''.$nome.'_id\',$_SESSION[\'filtros\'][\''.$modulo.'\'][\''.$nome.'_id\'],0);

            ';
            break;
            case 'select':
              $tabela = $_REQUEST['opcoes'][$key];
              $nome = $this->prepara($_REQUEST['nome'][$key]);

              $options = explode(',',$_REQUEST['opcoes'][$key]);

              $opcoesArray = '';
              foreach ($options as $key => $value) {
                $opcoesArray .= "'$value'=>'$value',";
              }

              $opcoesArray = $this->cutEnd($opcoesArray,strlen($opcoesArray)-1);

              $retorno .= '#dados do '.$nome.'
              $'.$nome . ' = array('.$opcoesArray.');
              $this->keys[\'select_'.$nome.'\'] = $this->html->select(false, $'.$nome.', \''.$nome.'_id\',$this->keys[\''.$nome.'_id\'],0,\'Selecione\');

              ';

              break;

            case 'status':
              $tabela = $_REQUEST['opcoes'][$key];
              $nome = $this->prepara($_REQUEST['nome'][$key]);

              $options = explode(',',$_REQUEST['opcoes'][$key]);

              $opcoesArray = '';
              foreach ($options as $key => $value) {
                $opcoesArray .= "'$value'=>'$value',";
              }

              $opcoesArray = $this->cutEnd($opcoesArray,strlen($opcoesArray)-1);

              $retorno .= '
              #dados do status
              if(strtolower($_SESSION[\'filtros\'][\''.$modulo.'\'][\''.$nome.'\']) != \'\') {
                  if(strtolower($_SESSION[\'filtros\'][\''.$modulo.'\'][\''.$nome.'\']) == \'ativo\') {
                    $this->keys[\'statusAtivo\'] = "selected";
                    $this->keys[\'statusInativo\'] = "";
                  } else {
                    $this->keys[\'statusAtivo\'] = "";
                    $this->keys[\'statusInativo\'] = "selected";
                  }
              }
              ';

              break;
          default:
            # code...
            break;
        }
      }
      return $retorno;
    }

    private function geraPath($path) {
      $file = fopen('tmp/back-office/public/'.$path,'w+');
      fwrite($file,file_get_contents('padrao/'.$path));
      return  'tmp/back-office/public/'.$path;
    }
    private function singular($texto) {
      if($texto[strlen($texto)-1] == 's') {
        $texto[strlen($texto)-1] = '';
        $texto = trim($texto);
      }
      return $texto;
    }

    private function prepara($texto) {
      return strtolower(trim(removeAcentos(str_replace(' ','_',$texto))));
   }
    private function criaOptions($texto,$selecionado = '') {
      $options = explode(',',$texto);
      $optionsHtml = '';
      foreach ($options as $value) {
        if($value == $selecionado) {
            $optionsHtml .= "<option selected>$value</option>";
        } else {
            $optionsHtml .= "<option>$value</option>";
        }

      }
      return $optionsHtml;
   }

}
?>
