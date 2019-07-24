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
 * Clase Controller para el manejo de las tipificaciones
 */
class TipificacionController extends Controller{

    
    /**
     * Procesos que se ejecuta antes que el controller carge
     */
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } 



    
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
     * Elimina una tipificación
     */
    public function actionEliminar1(){
        $cod_proyecto = Request::rq('cod_proyecto');
        $id = Request::rq('id');
        $sql = "delete from " .$cod_proyecto .".tipificacion where id= '" .$id ."';";
        Aux::findBySql($sql)->one();        
        $txt = 'Tipificaión eliminada.';
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df&x3txt=' .$txt, 302);
    }

    

    

    /**
     * Agrega una tipificacion
     */
    public function actionAdd(){
        $cod_proyecto = trim(Request::rq('cod_proyecto'));
        $id = (int)Request::rq('id');
        $de = trim(Request::rq('de'));
        $valor = trim(Request::rq('valor'));
        $id_estatus = (int)Request::rq('id_estatus');
        $st = (int)Request::rq('st');
         
        if( is_null($valor) || $valor == '' )
            $valor = $de;
        if( $st == 0 )
            $st = 3;
        
        if( $de == '' || is_null($cod_proyecto) || $cod_proyecto == '' ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df&', 302 );
        }else{

            $sql = "WITH upsert AS 
(UPDATE " .$cod_proyecto .".tipificacion SET de='" .$de ."', valor='" .$valor ."', st='" .$st ."', id_estatus='" .$id_estatus ."' WHERE id='" .$id ."' RETURNING *)
    INSERT INTO " .$cod_proyecto .".tipificacion (de, valor, st, id_estatus) 
SELECT '" .$de ."', '" .$valor ."', '" .$st ."', '" .$id_estatus ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
            $obj = Aux::findBySql($sql)->one();
            $sql = "select id from " .$cod_proyecto .".tipificacion where de='" .$de ."' and valor='" .$valor ."' and st='" .$st ."' ;";
            $obj = Aux::findBySql($sql)->one();

            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df&cod_proyecto=' .$cod_proyecto .'&id=' .$obj->id , 302);
        }
    } // eof #######################################################
    


    
    /**
     * Despliega página por defecto de tipificaciones
     */
    public function actionDf(){        
        $cad = '';
        $data = array();
        $stx['0'] = '';
        $stx['1'] = 'Efectiva';
        $stx['2'] = 'No efectiva'; 
        $stx['3'] = 'Disponible';
        $stx['4'] = 'No disponible';
        $stx['5'] = '';
        $stx[''] = '';
        $cad .= '<div class="titulos"> Tipificaciones disponibles según el proyecto </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('tipificacion');
        
        $cad .= '<hr />';
        $cad .= $this->form();
        $cad .= '<br /><br /><hr /><br />';


        $dato[] = 'Proyecto';

        $dato[] = 'Valor';  
        $dato[] = 'Tipificación';
        $dato[] = 'Estado'; 
        $dato[] = 'Estatus';  
        $dato[] = 'Editar';
        $dato[] = 'Eliminar';
        $data[] = $dato; unset($dato);


        $sql0 = "select * from a.proyecto where st!='0' ;";
        $obj0s = Aux::findBySql($sql0)->all();
        foreach( $obj0s as $reg0 ){
                        $sql1 = "select
 t.*, s.de as estatus
from " .$reg0->codigo .".tipificacion t 
inner join a.estatus s on s.id=t.st order by cast(t.valor as int) asc, t.de asc;";
            $sql1 = "select
 t.*, s.de as estatus
from " .$reg0->codigo .".tipificacion t 
inner join a.estatus s on s.id=t.st order by t.valor asc, t.de asc;";
            $obj1s = Aux::findBySql($sql1)->all();
            foreach( $obj1s as $reg ){
                $dato[] = $reg0->codigo;

                $dato[] = $reg->valor;
                $dato[] = $reg->de;
                $dato[] = $stx[$reg->id_estatus];
                $dato[] = $reg->estatus;
                $dato[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df&cod_proyecto=' .$reg0->codigo .'&id=' .$reg->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reg->de .'"/></a>';
                $dato[] = '<a href="javascript:if( confirm(\'Eliminar: ' .$reg->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/eliminar1&cod_proyecto=' .$reg0->codigo .'&id=' .$reg->id .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reg->de .'"/></a>';
                $data[] = $dato; unset($dato);
            }
        }
        $cad .= Listado::listado($data);

        return $this->render('@views/crm/txt',array('txt'=>$cad));
    } // eof #######################################################



    
    /**
     * Formulario de tipificaciones
     */
    public function form(){
        $cod_proyecto = Request::rq('cod_proyecto');
        $id = (int)Request::rq('id');

        $de = '';
        $st = 0; // $id_instrumento = 0;
        $valor = '';
        
        if( $id > 0 ){
            $sql6 = "select * from " .$cod_proyecto .".tipificacion where id='" .$id ."';";
            $obj6s = Aux::findBySql($sql6)->all();
            if( count($obj6s) > 0 ){
                $de = $obj6s[0]->de;
                $st = $obj6s[0]->st;

                $valor = $obj6s[0]->valor;               
            }
        }
         $height = '2.4';

        $cad = '';
        $cad .= '<form class="form-horizontal" id="datosf" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/add" method="POST" enctype="multipart/form-data"> 
<input type="hidden" id="id" name="id" value="' .$id .'"/>
';


$cad .= '<div class="form-group"> <br/>';

              $cad .= '<div style="clear:both; height: ' .$height .'em;">';
              

$cad .= '<div class="control-label col-sm-1"> Proyecto </div>';
        $cad .= '<div class="col-sm-2"><select id="cod_proyecto" name="cod_proyecto" class="form-control" >';
        $cad .= '<option value="" > &nbsp; --- Seleccione --- </option>';
    $sql5 = "select * from a.proyecto where st!='0';";
    $obj5s = Aux::findBySql($sql5)->all();
    foreach( $obj5s as $reg5 ){
        if( $reg5->codigo == $cod_proyecto )
            $cad .= '<option value="' .$reg5->codigo .'" selected="selected"> &nbsp; ' .$reg5->de .'</option>';
        else
            $cad .= '<option value="' .$reg5->codigo .'"> &nbsp; ' .$reg5->de .'</option>';
    }
        $cad .= '</select></div>
<div class="col-sm-1">&nbsp;</div>';


$cad .= '<div class="control-label col-sm-1" title="Estatus del Registro"> Estatus </div>
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
        

        $cad .= '<div class="control-label col-sm-1"> Tipificación </div>';
        $cad .= '<div class="col-sm-2"><input id="de" class="form-control" name="de" value="' .$de .'" placeholder="Tipificación" style="z-index:1; width: 100%;" type="text" /></div>
<div class="col-sm-1">&nbsp;</div>';

        $cad .= '<div class="control-label col-sm-1"> Valor </div>';
        $cad .= '<div class="col-sm-2"><input id="valor" class="form-control" name="valor" value="' .$valor .'" placeholder="Valor" style="z-index:1; width: 100%;" type="text" /></div>
<div class="col-sm-1">&nbsp;</div>';


        $cad .= '<div class="control-label col-sm-1" title="Estado General"> Estado</div>';
        $cad .= '<div class="col-sm-2"><select id="id_estatus" name="id_estatus" class="form-control" style="z-index:1; width: 100%;" >
<option value="0" > &nbsp; --- Seleccione --- </option>
<option value="1"> &nbsp; Efectiva</option>
<option value="2"> &nbsp; No efectiva</option>
<option value="3"> &nbsp; Disponible</option>
<option value="4"> &nbsp; No disponible</option>
<option value="5"> &nbsp; </option>
</select></div>';
        
        
              $cad .= '</div>';

        $cad .= '<div class="col-sm-5"></div>
  <div class="col-sm-2">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df">
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
    } // eof 

    
} // class
