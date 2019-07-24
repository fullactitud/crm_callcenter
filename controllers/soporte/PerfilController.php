<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;

use app\components\crm\Mensaje;
use app\components\crm\Request;
use app\components\crm\Listado;
use app\components\crm\Ayuda;
use app\components\crm\JSon;
use app\components\crm\Formulario;

use app\models\Aux;



use app\models\UploadForm;
use yii\web\UploadedFile;

use yii\widgets\ActiveForm;

use app\vendor\PHPExcel\PHPExcel\IOFactory;

use yii\swiftmailer\Mailer;


/**
 * Controler para manejar los perfiles
 */
class PerfilController extends Controller{
    
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
     * Crea un código valido
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
     * Agregar perfil
     */    
    public function actionAdd(){
        $de = trim(Request::rq('de')); // DESCRIPCION
        $name = trim( Request::rq('name')).''; // PERFIL
        if( $de == '' || $name == '' ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/df&', 302 );
        }else{
            $objs = Aux::findBySql("select description from public.auth_item where name = '" .$name ."' and type='1';")->all();
            if( count($objs) > 0 ){
                // ACTUALIZAR
                $sql = "update public.auth_item set description='" .$de ."' where type='1' and name='" .$name ."'";
                $obj = Aux::findBySql($sql)->one();
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/df&name=' .$name, 302);    
            }else{
                // INSERT 
                $sql = "insert into public.auth_item (description, name, type) values ('" .$de ."', '" .$name ."', '1')";
                $obj = Aux::findBySql($sql)->one();
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/df&name=' .$name, 302);
            }
        }
    } // eof #######################################################
    
    
    /**
     * Acción por defecto de perfil
     */
    public function actionDf(){
        $name = Request::rq('name').'';
        
        if( $name != '' ){
            $obj = Aux::findBySql("select distinct * from public.auth_item where type='1' and name='" .$name ."'")->one(); 
            $aux_id = '<input type="hidden" id="name" name="name" value="' .$name .'">';
            $aux_de = $obj->description;
            $aux_name = $obj->name;
            $aux2_codigo = ' onlyread="onlyread" ';
        }else{
            $aux_id = '<input type="hidden" id="name" name="name" value="">';
            $aux_de = '';
            $aux_name = '';
            $aux2_codigo = '';
        }
        
        $form = '';
        
        
        
        
        
        $form .= '<div class="titulos"> Perfiles - Roles </div>';
        $form .= Mensaje::mostrar();
        $form .= Ayuda::toHtml('perfiles');
        $form .= '<hr/>';
               
        
        $form .= '<form class="form-horizontal" id="datosdelperfil" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/add" method="POST" enctype="multipart/form-data">
' .$aux_id .'
<div class="form-group"> <br/>
  <div class="control-label col-sm-1" for="de">Descripción</div>
  <div class="col-sm-2">
    <input id="de" class="form-control" name="de" value="' .$aux_de .'" placeholder="Descripción" style="z-index:1; width: 240px;" type="text" />
  </div>
  <div class="col-sm-1">&nbsp;</div>
  <div class="control-label col-sm-1" for="codigo">Perfil</div>
  <div class="col-sm-2">
    <input id="name" class="form-control" name="name" value="' .$aux_name .'" ' .$aux2_codigo .' placeholder="Nombre del perfil" style="z-index:1; width: 200px;" type="text" />
  </div>
  <div class="col-sm-1"> &nbsp; </div>
  <div class="col-sm-2">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/df">
      <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:100px;">   Recetear   </div>
    </a>
  </div>
  <div class="col-sm-2">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:100px;">   Salvar   </button>
  </div>
</div>
</form>';



             
            
            $form .= '<hr/>';
        $form .= '<div class="subtitulos"><u>Listado</u> </div>';
        
        
        
        
        $data[] = 'Perfil';
        $data[] = 'Descripción';
        $linea[] = $data;
        unset($data);
        $registros = Aux::findBySql("select * from public.auth_item where type='1'")->all();
        foreach( $registros as $reg ){
            $data[] = $reg->name;
            $data[] = $reg->description;
            $data[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/df&name=' .$reg->name .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reg->de .'"/></a>';
            $data[] = '<a href="javascript:if( confirm(\'Eliminar el perfil: ' .$reg->description .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/eliminar1&name=' .$reg->name .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reg->de .'"/></a>';
            $linea[] = $data;
            unset($data);
        }

        
        $list = '<div style="width:600px;">' .Listado::listado( $linea ) .'</div>';
     
        

        return $this->render('@views/df/txt',array(     
            'txt'=>$form .$list,
        ));
    } // eof #######################################################


    


    
    /**
     * Eliminar pefil
     **/
    public function actionEliminar1(){
        $name = Request::rq('name');
        $sql = "delete from public.auth_item where name= '" .$name ."';";
        Aux::findBySql($sql)->one();        
        $txt = 'Perfil ' .$name .' eliminado.';
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/df&x3txt=' .$txt, 302);
    }


    
} // class
