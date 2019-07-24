<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;
use app\models\AuthAssignment;

use app\components\crm\Mensaje;
use app\components\crm\Reporte;
use app\components\crm\Grafico;
use app\components\crm\Formato;
use app\components\crm\Request;
use app\components\crm\Ayuda;
use app\components\crm\Listado;
use app\models\Aux;

/**
 * Controler para manejar dominios
 */
class DominioController extends Controller{
    
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
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } 

    /**
     * Agregar dominios
     */
    public function actionAdd(){
        $cod_proyecto = trim(Request::rq('cod_proyecto'));
        $id = (int)Request::rq('id');
        $de = trim(Request::rq('de'));
        $cod = trim(Request::rq('cod'));
        $st = (int)Request::rq('st');
        $id_instrumento = (int)Request::rq('id_instrumento');

        if( is_null($cod) || $cod == '' )
            $cod = $de;
        if( $st == 0 )
            $st = 3;
        
        if( $de == '' || $id_instrumento == 0 ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df&', 302 );
        }else{
            $sql = "WITH upsert AS 
(UPDATE " .$cod_proyecto .".dominio SET de='" .$de ."', cod='" .$cod ."', id_instrumento='" .$id_instrumento ."', st='" .$st ."' WHERE id='" .$id ."' RETURNING *)
    INSERT INTO " .$cod_proyecto .".dominio (de, cod, id_instrumento, st) 
SELECT '" .$de ."','" .$cod ."','" .$id_instrumento ."','" .$st ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
            $obj = Aux::findBySql($sql)->one();
            $sql = "select id from " .$cod_proyecto .".dominio where de='" .$de ."' and cod='" .$cod ."' and id_instrumento='" .$id_instrumento ."' and st='" .$st ."' ;";
            //echo $sql; exit();
            $obj = Aux::findBySql($sql)->one();
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df&cod_proyecto=' .$cod_proyecto .'&id_instrumento=' .$id_instrumento .'&id=' .$obj->id , 302);
        }
    } // eof #######################################################
    


    
    /**
     * Acción por defecto para dominios
     */
    public function actionDf(){
        $cad = '';
        $data = array();
        $cad .= '<div class="titulos"> Dominios </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('dominios'); 
        $cad .= '<hr />';
        $cad .= $this->form();
        $cad .= '<br /><br /><hr /><br />';


        // LISTADO ///////////////////////////////////////////
        $dato[] = 'Proyecto';
        $dato[] = 'Instrumento';
        $dato[] = 'ID';
        $dato[] = 'Dominio';
        $dato[] = 'Código';

        $dato[] = 'Estatus';  
        $dato[] = 'Editar';
        $dato[] = 'Eliminar';
        $data[] = $dato; unset($dato);
        $sql0 = "select * from a.proyecto where st!='0' ;";
        $obj0s = Aux::findBySql($sql0)->all();
        foreach( $obj0s as $reg0 ){
            $sql1 = "select
 d.*, i.de as instrumento, s.de as estatus, i.id as id_instrumento 
from " .$reg0->codigo .".dominio d 
inner join " .$reg0->codigo .".instrumento i on i.id=d.id_instrumento 
inner join a.estatus s on s.id=d.st order by instrumento asc, d.de asc;";
            $obj1s = Aux::findBySql($sql1)->all();
            foreach( $obj1s as $reg ){
                $dato[] = $reg0->codigo;
                $dato[] = $reg->instrumento;
                $dato[] = $reg->id;
                $dato[] = $reg->de;
                $dato[] = $reg->cod;
                $dato[] = $reg->estatus;
                $dato[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df&cod_proyecto=' .$reg0->codigo .'&id_instrumento=' .$reg->id_instrumento .'&id=' .$reg->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reg->de .'"/></a>';
                $dato[] = '<a href="javascript:if( confirm(\'Eliminar: ' .$reg->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/eliminar1&cod_proyecto=' .$reg0->codigo .'&id=' .$reg->id .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reg->de .'"/></a>';
                $data[] = $dato; unset($dato);
            }
        }
        $cad .= Listado::listado($data);
        //////////////////////////////////////////////////////
        
        return $this->render('@views/crm/txt',array('txt'=>$cad));
    } // eof #######################################################





    
    /**
     * Formulario para agregar dominios
     */
    public function form(){
        $cod_proyecto = $codProyecto = Request::rq('cod_proyecto');
        $id = (int)Request::rq('id');

        $de = '';
        $st = $id_instrumento = 0;
        $cod = '';
        
        if( $id > 0 ){
            $sql6 = "select * from " .$codProyecto .".dominio where id='" .$id ."';";
            $obj6s = Aux::findBySql($sql6)->all();
            if( count($obj6s) > 0 ){
                $de = $obj6s[0]->de;
                $st = $obj6s[0]->st;
                $id_instrumento = $obj6s[0]->id_instrumento;
                $cod = $obj6s[0]->cod;
            }
        }

        $height = '2.4';
        
        $cad = '';
        $cad .= '<form class="form-horizontal" id="datosf" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/add" method="POST" enctype="multipart/form-data"> 
<input type="hidden" id="id" name="id" value="' .$id .'"/>';



$cad .= '<div class="form-group"> <br/>';
  
 $carga = "carga( 'div_select_instrumento', 'cod_proyecto=' +$(this).val(), 'option_instrumento', 'soporte/dominio' );";

$cad .= '<div style="clear:both; height: ' .$height .'em;">';


$cad .= '<div class="control-label col-sm-1"> Proyecto </div>';
$cad .= '<div class="col-sm-2"><select id="cod_proyecto" name="cod_proyecto" class="form-control" onChange="' .$carga .'">';
$cad .= '<option value="" > => Seleccione <= </option>';
$sql5 = "select * from a.proyecto where st!='0';";
$obj5s = Aux::findBySql($sql5)->all();
foreach( $obj5s as $reg5 ){
    if( $reg5->codigo == $cod_proyecto )
        $cad .= '<option value="' .$reg5->codigo .'" selected="selected"> &nbsp; ' .$reg5->de .'</option>';
    else
        $cad .= '<option value="' .$reg5->codigo .'"> &nbsp; ' .$reg5->de .'</option>';
}
$cad .= '</select></div>';
$cad .= '<div class="col-sm-1">&nbsp;</div>';




$cad .= '<div class="control-label col-sm-1"> Instrumento </div>';
$cad .= '<div id="div_select_instrumento" class="col-sm-2">';
if( !is_null($cod_proyecto)  )
    $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" width="1px;" onLoad="carga( \'div_select_instrumento\', \'cod_proyecto=' .$cod_proyecto .'&id_instrumento=' .$id_instrumento .'\', \'option_instrumento\', \'soporte/dominio\' );" />';
$cad .= '</div>';


$cad .= '<div class="col-sm-1">&nbsp;</div>';



$cad .= '<div class="control-label col-sm-1"> Estado </div>
<div class="col-sm-2"><select id="st" name="st" class="form-control" >';
if( $st == 1 ) 
    $cad .= '<option value="1" selected="selected"> &nbsp; Activo</option>';
else
    $cad .= '<option value="1"> &nbsp; Activo</option>';
if( $st == 3 )
    $cad .= '<option value="3" selected="selected"> &nbsp; En espera</option>';
else
    $cad .= '<option value="3"> &nbsp; En espera</option>';

$cad .= '<option value="0"> &nbsp; Eliminar</option>';
$cad .= '</select></div>';

        
$cad .= '</div><div style="clear:both; height: ' .$height .'em;">';

        $cad .= '<div class="control-label col-sm-1"> Dominio </div>';
        $cad .= '<div class="col-sm-2"><input id="de" class="form-control" name="de" value="' .$de .'" placeholder="Dominio" style="z-index:1; width: 100%;" type="text" /></div>
<div class="col-sm-1">&nbsp;</div>';


        $cad .= '<div class="control-label col-sm-1"> Código </div>';
        $cad .= '<div class="col-sm-2"><input id="cod" class="form-control" name="cod" value="' .$cod .'" placeholder="Código del dominio" style="z-index:1; width: 100%;" type="text" /></div>
<div class="col-sm-1">&nbsp;</div>';
              
              
              $cad .= '</div>';
        
              
              $cad .= '</div>';


        $cad .= '<div class="col-sm-5"></div>
  <div class="col-sm-2">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df">
      <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:100%;">   Recetear   </div>
    </a>
  </div>
<div class="col-sm-2"></div>
  <div class="col-sm-2">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:100%;">   Salvar   </button>
  </div>

</div>
</form>';
        return $cad;
    } // 

    
    /**
     * Opción de instrumentos
     */
    function actionOption_instrumento(){
        Yii::$app->layout = 'embebido';
        $cod_proyecto = Request::rq('cod_proyecto');
        $id_instrumento = (int)Request::rq('id_instrumento');
        $cad = '<select id="id_instrumento" name="id_instrumento" class="form-control" >';
        $cad .= '<option value="0" > &nbsp; --- Seleccione --- </option>';
        $sql1 = "select * from " .$cod_proyecto .".instrumento where st='1';";
        $obj1s = Aux::findBySql($sql1)->all();
        foreach( $obj1s as $reg1 ){
            if( $reg1->id == $id_instrumento )
                $cad .= '<option value="' .$reg1->id .'" selected="selected"> &nbsp; ' .$reg1->id .'. ' .$reg1->de .'</option>';
            else
                $cad .= '<option value="' .$reg1->id .'"> &nbsp; ' .$reg1->id .'. ' .$reg1->de .'</option>';
        }
        $cad .= '</select>';
        return $this->render('@views/crm/txt',array( 'txt' => $cad ));
    } // 


    /**
     * Eliminar dominios
     */
    public function actionEliminar1(){
        $cod_proyecto = Request::rq('cod_proyecto');
        $id = Request::rq('id');
        $sql = "delete from " .$cod_proyecto .".dominio where id= '" .$id ."';";
        Aux::findBySql($sql)->one();        
        $txt = 'Dominio eliminado.';
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df&x3txt=' .$txt, 302);
    }

    
    
} // class


