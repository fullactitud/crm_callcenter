<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;
use app\models\LoginForm;
use app\models\Proyecto;
use app\models\Usuario;
use app\models\Perfil;
use app\models\crm\Encuesta;
use app\models\crm\Entrada;
use app\models\crm\Opcion;

use app\components\crm\Mensaje;
use app\components\crm\Request;
use app\components\crm\Listado;

use app\models\xMenu;
use app\models\xProyecto;
use app\models\xUsuario;
use app\models\xPerfil;
use app\models\crm\xCabecera;

use app\models\crm\xInstrumento;
use app\models\crm\xEntrada;
use app\models\crm\xOpcion;
use app\models\crm\xInstrumentoCabecera;



use app\models\Aux;
use app\components\crm\Ayuda;
use app\components\crm\JQGrid;
use app\components\crm\Menu;
use app\components\crm\JSon;
use app\components\crm\Formulario;
use app\components\EncuestaProspecto1;
use app\components\EncuestaEntrada1;

use app\models\test\cProspecto;


use app\models\UploadForm;
use yii\web\UploadedFile;


use yii\widgets\ActiveForm;

use app\vendor\PHPExcel\PHPExcel\IOFactory;

use yii\swiftmailer\Mailer;


/**
 * Controler para manejar los proyectos
 */
class ProyectoController extends Controller{
    
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [                
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    } // eof #######################################################

    
    
    /**
     * @inheritdoc
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    } // eof #######################################################


   

    /**
     * Procesos que se ejecutan antes que el controller carge
     */    
    function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();


        
        if( isset($_REQUEST['_lang']) ){
            Yii::$app->language = $_REQUEST['_lang'];
            Yii::$app->session['_lang'] = Yii::$app->language;
        }else if( isset(Yii::$app->session['_lang']) ){
            Yii::$app->language = Yii::$app->session['_lang'];
        }else{
            Yii::$app->language = 'es_ve';
            Yii::$app->session['_lang'] = Yii::$app->language;
        }
    } // eof #######################################################
            



    /**
     * Genera un código valido
     */
    public function codigo( $codigo ){        
        $codigo = str_replace(' ', '', $codigo);
        $codigo = str_replace("\t", '', $codigo);
        $codigo = str_replace("\n", '', $codigo);
        $codigo = str_replace('á', '', $codigo);
        $codigo = str_replace('ó', '', $codigo);
        $codigo = str_replace('í', '', $codigo);
        $codigo = str_replace('é', '', $codigo);
        $codigo = str_replace('ú', '', $codigo);
        $codigo = str_replace('ñ', '', $codigo);
        $codigo = str_replace('Á', '', $codigo);
        $codigo = str_replace('Ó', '', $codigo);
        $codigo = str_replace('Í', '', $codigo);
        $codigo = str_replace('É', '', $codigo);
        $codigo = str_replace('Ú', '', $codigo);
        $codigo = str_replace('Ñ', '', $codigo);
        $codigo = str_replace('-', '_', $codigo);
        $codigo = str_replace('&', '_', $codigo);
        $codigo = str_replace('#', '_', $codigo);
        $codigo = str_replace('%', '_', $codigo);
        $codigo = str_replace('$', '_', $codigo);
        $codigo = str_replace('/', '_', $codigo);
        $codigo = str_replace('(', '_', $codigo);
        $codigo = str_replace(')', '_', $codigo);
        $codigo = str_replace('=', '_', $codigo);
        $codigo = str_replace("'", '', $codigo);
        $codigo = str_replace('.', '_', $codigo);
        $codigo = str_replace(',', '_', $codigo);
        $codigo = str_replace(':', '_', $codigo);
        $codigo = str_replace(';', '_', $codigo);
        return $codigo;
    }


    /**
     * Agrega un proyecto
     */    
    public function actionAddproyecto(){
        $de = trim(Request::rq('de')); // DESCRIPCION del proyecto
        if( $de == '' ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto&', 302 );
        }else{
            $codigo = trim(Request::rq('codigo')); // CODIGO del proyecto
            if( $codigo == '' ) $codigo = strtolower($de);
            $codigo = $this->codigo($codigo);
            
            $st = (int)Request::rq('st'); // ESTATUS: 0 borrado, 1 activo, 2 pendiente, 3 en espera



            
            $dir_subida = Yii::$app->basePath .'/web/uploads/logo/'; // .$this->nombre;
            $nombre = date('Ymdhis') .'_' .basename($_FILES['imageFile']['name']);
            $fichero_subido = $dir_subida .$nombre;
            if( move_uploaded_file($_FILES['imageFile']['tmp_name'], $fichero_subido) ){
                $imagen = $nombre;
            }

            
           
            

 

            
            
            $admin = (int)Request::rq('admin'); // ID del usuario administrador
            $pais = Request::rq('pais'); // Codigo del pais
            
            $id = (int)Request::rq('x3id'); // ID del proyecto (para la actualización)
            if( $id != 0 ){
                // ACTUALIZAR PROYECTO
                $sql = "update a.proyecto set st='" .$st ."', de='" .$de ."', id_admin='" .$admin ."', pais='" .$pais ."' where id='" .$id ."'";
                $obj = Aux::findBySql($sql)->one();
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto&x3id=' .$id, 302);
            }else{
                
                $objs = Aux::findBySql("select id from a.proyecto where codigo = '" .$codigo ."';")->all();
                if( count($objs) > 0 ){
                    $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto&', 302);
                }else{
                    // crear las estructuras en la base de datos
                    $sql00s = file( Yii::$app->basePath .'/src/base/schema.sql' );
                    $auxSQL = '';
                    foreach( $sql00s as $sql00 ){
                        $auxSQL .= $sql00;
                        if( substr(trim($auxSQL), -1, 1) == ';' ){
                            $auxSQL = str_replace('__schema__', $codigo, $auxSQL);
                            Aux::findBySql($auxSQL)->one();
                            $auxSQL = '';
                        }
                    }
                    
                    
                    // CREA EL DIRECTORIO
                    $carpeta = Yii::$app->basePath .'/controllers/proyecto/' .$codigo;
                    if( !file_exists($carpeta) ) {
                        mkdir( $carpeta, 0755, true );
                    }
                    
                    
                    // LEE CONTROLLER BASE, PARA CREAR OTRO ARCHIVO CONTROLLER
                    $controller = file_get_contents( Yii::$app->basePath .'/src/base/controller.php' );
                    $controller = str_replace('__codigo__', $codigo, $controller);
                    $controller = str_replace('__codigo0__', ucfirst($codigo), $controller);
                    $controller = str_replace('__de__', ucfirst($de), $controller);
                    file_put_contents( $carpeta .'/' .ucfirst($codigo) .'Controller.php', $controller );
                    
                    
                    // INSERT PROTECTO
                    if( !isset($imagen) )
                        $sql = "insert into a.proyecto (de, codigo, id_admin, pais, st) values ('" .$de ."', '" .$codigo ."', '" .$admin ."', '" .$pais ."', '" .$st ."')";
                    else
                        $sql = "insert into a.proyecto (de, codigo, id_admin, pais, st, imagen) values ('" .$de ."', '" .$codigo ."', '" .$admin ."', '" .$pais ."', '" .$st ."', '" .$imagen ."')";
                    $obj = Aux::findBySql($sql)->one();
                    $obj = Aux::findBySql("select id from a.proyecto where codigo='" .$codigo ."' order by id DESC")->one();
                    $id = $obj->id;
                    $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto&x3id=' .$id, 302);
                }
            }
        }
    } // eof #######################################################
    
    
    /**
     * Acción por defecto de proyectos
     */
    public function actionProyecto(){
        $id = (int)Request::rq('x3id');
    


        if( $id != 0 ){
            $obj = Aux::findBySql("select * from a.proyecto where id='" .$id ."'")->one(); 
            $df_pais = $obj->pais;
            $df_usuario = $obj->admin;
            $df_activo = $obj->st;
            $aux_id = '<input type="hidden" id="x3id" name="x3id" value="' .$obj->id .'">';
            $aux_de = $obj->de;
            $aux_codigo = $obj->codigo;
            $aux2_codigo = ' onlyread="onlyread" ';
        }else{
            $df_pais = 've';
            $df_usuario = '1';
            $df_activo = '1';
            $aux_id = '<input type="hidden" name="id" value="0">';
            $aux_de = '';
            $aux_codigo = '';
            $aux2_codigo = '';
        }
        
        $form = '';
        $option_pais = '';
        $option_usuarios = '';

        
        $paises = Aux::findBySql("select * from a.pais where st='1'")->all();
        foreach( $paises as $pais )
            if( $pais->codigo == $df_pais )
                $option_pais .= '<option value="' .$pais->codigo .'" selected="selected">' .$pais->de .'</option>';
            else
                $option_pais .= '<option value="' .$pais->codigo .'">' .$pais->de .'</option>';

        
        $usuarios = Aux::findBySql("select * from a.usuario where st='1' order by nombres ASC, apellidos ASC")->all();
        foreach( $usuarios as $usuario )
            if( $usuario->id == $df_usuario )
                $option_usuarios .= '<option value="' .$usuario->id .'" selected="selected">' .$usuario->nombres .' ' .$usuario->apellidos .'</option>';
            else
                $option_usuarios .= '<option value="' .$usuario->id .'">' .$usuario->nombres .' ' .$usuario->apellidos .'</option>';


        if( $df_activo == 1 )
            $aux_st = '<div class="radio-inline"><label class="control-label"><input type="radio" id="st_2" name="st" value="1"  checked="checked" > Sí</label></div>
    <div class="radio-inline"><label class="control-label"><input type="radio" id="st_1" name="st" value="3" > No</label></div>';
        else
            $aux_st = '<div class="radio-inline"><label class="control-label"><input type="radio" id="st_2" name="st" value="1" > Sí</label></div>
    <div class="radio-inline"><label class="control-label"><input type="radio" id="st_1" name="st" value="3" checked="checked"> No</label></div>';            
        

        


            

        $form .= '<div class="titulos"> Panel de Proyectos </div>';
        $form .= Mensaje::mostrar();
        $form .= Ayuda::toHtml('proyectos');
        

        
        $form .= '<hr/>';
        $form .= '<div class="subtitulos"><u>Agregar Proyecto</u></div>';
    




                                      

$form .= '<form class="form-horizontal" id="datosdelproyecto" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/addproyecto" method="POST" enctype="multipart/form-data">

' .$aux_id .'

<div class="form-group"> <br/>
  <label class="control-label col-sm-1" for="imageFile">Logo</label>
  <div class="col-sm-2">
    <input type="file" id="imageFile" name="imageFile" class="ui-button ui-corner-all ui-widget" style="font-size:0.75em; z-index:1; width: 240px;" />
  </div>
  <div class="col-sm-1">&nbsp;</div>
  <label class="control-label col-sm-1" for="de">Descripción</label>
  <div class="col-sm-2">
    <input id="de" class="form-control" name="de" value="' .$aux_de .'" placeholder="Descripción" style="z-index:1; width: 240px;" type="text" />
  </div>
  <div class="col-sm-1">&nbsp;</div>
  <label class="control-label col-sm-1" for="codigo">Código</label>
  <div class="col-sm-2">
    <input id="codigo" class="form-control" name="codigo" value="' .$aux_codigo .'" ' .$aux2_codigo .' placeholder="Código" style="z-index:1; width: 240px;" type="text" />
  </div>
</div>



<div class="form-group">
  <label class="control-label col-sm-1" for="codigo" title="Administrador del Proyecto">Admin</label>
  <div class="col-sm-2">
    <select id="codigo" class="form-control" name="admin" placeholder="Administrador" style="z-index:1; width: 240px;" >' .$option_usuarios .'</select>
  </div>
  <div class="col-sm-1">&nbsp;</div>
  <label class="control-label col-sm-1" for="pais">País</label>
  <div class="col-sm-2">
    <select id="pais" class="form-control" name="pais" placeholder="País" style="z-index:1; width: 240px;">' .$option_pais .'</select>
  </div>
  <div class="col-sm-1">&nbsp;</div>
  <label class="control-label col-sm-1" for="st"> Activo:  &nbsp;</label>
  <div class="col-sm-2">' .$aux_st .'</div>
</div>

<div class="form-group">
  <div class="col-sm-offset-6 col-sm-2">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto">
      <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:250px;">   Recetear   </div>
    </a>
  </div>
  <div class="col-sm-1"> &nbsp; </div>
  <div class="col-sm-2">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:250px;">   Salvar   </button>
  </div>
</div>

</form>';





            
            $form .= '<hr/>';
        $form .= '<div class="subtitulos"><u>Listado de Proyectos</u></div>';
        
        

        
 
        $list = '<table width="100%"><tbody>';
                    $list .= '<tr><td>ID</td><td>Descripción</td><td>Código</td><td>País</td><td>Estatus</td><td>Admin</td></tr>';
        $registros = Aux::findBySql("select p.id, p.de, p.codigo, pa.de as pais, p.st, nombres, apellidos, s.de as estatus from a.proyecto p inner join a.estatus s on s.id=p.st inner join a.usuario u on u.id = p.id_admin inner join a.pais pa on p.pais = pa.codigo")->all();
        foreach( $registros as $reg )
            $list .= '<tr><td>' .$reg->id .'</td><td>' .$reg->de .'</td><td>' .$reg->codigo .'</td><td>' .$reg->pais .'</td><td>' .$reg->estatus .'</td><td>' .$reg->nombres .' ' .$reg->apellidos .'</td><td title="Editar el proyecto"><a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto&x3id=' .$reg->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"/></a></td><td title="Eliminar ' .$reg->de .'"><a href="javascript:if( confirm(\'Eliminar el proyecto: ' .$reg->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/eliminar1&x3schema=' .$reg->codigo .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"/></a></td></tr>';
$list .= '</body></table>';

        $cabecera[] = array('ID','id','15','true','int','center',null,null,null,null);
        $cabecera[] = array('CODIGO','codigo','50','false','string','center',null,null,null,null);
        $cabecera[] = array('PAIS','pais','50','false','string','center',null,null,null,null);        
        $cabecera[] = array('ESTATUS','st','15','false','int','center','int',true,'select',"{value:'3:No;1:Si'}");

        
        $grilla = Listado::JQGrid( '', $cabecera, $registros, 'Listado de Proyectos', '117', 'df/form1&aux=proyecto', 'divdetail' );
        return $this->render('@views/df/txt',array(     
            'txt'=>$form .$list,
        ));

    } // eof #######################################################


    
    /**
     * Elimina recursivamente un directorio 
     */
    public function eliminarDirectorio( $src ){
        if( is_dir($src) ){
            $dir = opendir( $src );
            while( false !== ($file = readdir($dir)) ){
                if( $file != '.' && $file != '..' ){
                    $full = $src .'/'. $file;
                    if( is_dir($full) ){
                        $this->eliminarDirectorio( $full );
                    }else{
                        unlink($full);
                    }
                }
            }   
            closedir( $dir );
            rmdir( $src );
        }
        return;
    }


    
    /**
     * Elimina un proyecto 
     */
    public function actionEliminar1(){
        $x3schema = Request::rq('x3schema');
        $sql = "select * from " .$x3schema .".instrumento where st != '0';";
        $objs = Aux::findBySql($sql)->all();
        if( count($objs) > 0 ){
            $txt = 'El proyecto ' .$x3schema .' no puede ser eliminado eliminado porque posee instrumentos. Debe eliminarlos antes.';
        }else{
            $carpeta = Yii::$app->basePath .'/controllers/' .$x3schema;
            $this->eliminarDirectorio($carpeta);
            $sql = "drop schema " .$x3schema ." CASCADE;";
            Aux::findBySql($sql)->one();
            $sql = "delete from a.proyecto where codigo= '" .$x3schema ."';";
            Aux::findBySql($sql)->one();        
            $txt = 'Proyecto ' .$x3schema .' eliminado.';
        }
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&x3txt=' .$txt, 302);

    }


    /**
     * Listados de proyectos
     */
    public function actionProyectos(){ 
        Yii::$app->layout = 'proyectos';
       
        $sql = "select * from a.proyecto where 1=1 order by id DESC;";
        $objs = xProyecto::findBySql($sql)->all();
        
        return $this->render('@views/df/proyectos',array(
            'objs'=>$objs,    
        ));
    } // eof #######################################################
    

    /**
     * Acción LOGOUT
     */
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    } // eof #######################################################




    /**
     * Accion que genera y publica objetos JSON destinados al JQGrid
     */
    public function actionOut(){
        $op = $_REQUEST['op'];
        switch( $op ){

        case '117': // proyectos
            echo JSon::toJson('a.proyecto', 'id,codigo,pais,st');
            break;
      
        } // switch    
    } 
 
    
} // class
