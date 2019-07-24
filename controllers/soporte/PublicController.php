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
use app\components\crm\Email;
use app\models\Aux;


/**
 * Controler para acciones publicas
 */
class PublicController extends Controller{
    
    
    /**
     * Procesos que se ejecuta antes de cargar el controller
     */
    
    public function init(){
        
        parent::init();
    } // eof 
    
    
    
    
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
     * Envia los reportes
     */
    public function actionEnviar(){
 
        $archivo = '';
        $vct = array('d','l','m','i','j','v','s');
        $hora1 = date('H:i', strtotime('-30 minute'));
                                      $hora1 = date('H:i', strtotime('-6000 minute'));
        $hora2 = date('H:i');
        $dia = date('N');
                                    
        $sql0 = "select * from a.proyecto where st='1';";
        $obj0s = Aux::findBySql($sql0)->all();
        foreach( $obj0s as $reg0 ){
                 
            $sql1 = "select e.id, e.de, e.id_reporte, e.id_instrumento, i.codigo as cod_instrumento, " .$vct[$dia] ." as hora, r.de as reporte, r.tp, r.codigo  
from " .$reg0->codigo .".envio e 
inner join a.reporte r on r.id=e.id_reporte
left join " .$reg0->codigo .".instrumento i on e.id_instrumento = i.id and i.st = '1'
where e.st ='1' and " .$vct[$dia] ." > '" .$hora1 ."' and " .$vct[$dia] ." <= '" .$hora2 ."';";
            
            $obj1s = Aux::findBySql($sql1)->all();
            foreach( $obj1s as $reg ){
                $destino = '';
                if( $reg->tp == 1 ){
                    $archivo = Reporte::reportNoGeneric( $reg0->codigo, $reg->codigo, $reg->cod_instrumento, $reg->id_instrumento, true );
                }else
                    $archivo = Reporte::reportGeneric( $reg0->codigo, $reg->id_reporte, null, true );
                
                
                $sql2 = "select ee.*, s.de as estatus, u.nombres||' '||u.apellidos as usuario, u.email 
from " .$reg0->codigo .".envio_email ee  
inner join a.usuario u on u.id=ee.id_email 
inner join a.estatus s on s.id=ee.st 
where id_envio='" .$reg->id ."' and ee.st='1'
union select ee.*, s.de as estatus, ' ' as usuario, u.email 
from " .$reg0->codigo .".envio_email ee  
inner join " .$reg0->codigo .".email u on u.id = ee.id_email 
inner join a.estatus s on s.id = ee.st 
where id_envio='" .$reg->id ."' and ee.st='1' ;";
                $obj2s = Aux::findBySql($sql2)->all();
                foreach( $obj2s as $reg2 ) if( isset($reg2->email) && $reg2->email != '' ) $destino = $reg2->email .',';
                if( $destino != '' ){
                    $stt = Email::enviarArchivo( 'apps@callycall.com.ve', substr($destino,0,-1), 'Reporte '.$reg->reporte , $reg->de, $archivo );
                    if( $stt )
                        echo 'Enviado el reporte ' .$reg->reporte .' para: ' .substr($destino,0,-1) .'<br />';
                    else{
                        $stt = Email::enviarArchivo( 'develop.callycall@gmail.com', substr($destino,0,-1), 'Reporte '.$reg->reporte , $reg->de, $archivo );
                        if( $stt )
                            echo 'Enviado el reporte ' .$reg->reporte .' para: ' .substr($destino,0,-1) .'<br />';
                        else echo 'Error al enviar el reporte ' .$reg->reporte .' para: ' .substr($destino,0,-1) .'<br />';
                    }
                }
            }
        }
    } // eof 
    
  
    
    
    /**
     * Usuarios para los envios
     */
    public static function usuarios( $cod_proyecto = '', $id_envio = 0 ){
        $cad = '';
        $sql2 = '';
        if( $cod_proyecto != '' && $id_envio > 0 ){
            $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario, ee.id as chk
            from a.usuario u
            inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
            left join " .$cod_proyecto .".envio_email ee on ee.id_email = u.id and ee.id_envio='" .$id_envio ."' 
            where u.st = '1' and u.email != '' and u.email != 'NULL' order by usuario asc;";
            
            $sql2 = "select distinct e.id, e.email, e.de 
            from " .$cod_proyecto .".email e 
            left join " .$cod_proyecto .".envio_email ee on ee.id_email = e.id and ee.id_envio='" .$id_envio ."'
            where e.st = '1' order by e.email asc;";
        }else if( $cod_proyecto != '' ){
            $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario
            from a.usuario u
            inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
            where u.st = '1' and u.email != '' and u.email != 'NULL' order by usuario asc;";
            
            $sql2 = "select distinct e.id, e.email, e.de 
            from " .$cod_proyecto .".email e 
            where e.st = '1' order by e.email asc;";
        }else{
            $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario
            from a.usuario u
            inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
            where u.st = '1' and u.email != '' and u.email != 'NULL' order by usuario asc;";
        }

        
        $objs = Aux::findBySql($sql)->all();
        $cad .= '<h3> Destinatarios </h3>';
        $cad .= '<div> <input type="radio" id="destino_1" name="destino" value="1"/> &nbsp; Todos </div>';
        $cad .= '<div> <input type="radio" id="destino_2" name="destino" value="2"/> &nbsp; Personal de Soporte </div>';
        $cad .= '<div> <input type="radio" id="destino_3" name="destino" value="3"/> &nbsp; Teleoperadores </div>';
        $cad .= '<div> <input type="radio" id="destino_4" name="destino" value="4"/> &nbsp; Supervisores </div>';
        $cad .= '<div> <input type="radio" id="destino_5" name="destino" value="5"/> &nbsp; Clientes </div>';
        $cad .= '<hr /><div id="divEmailEnvio" style="clear: both;">';
        
        if( $sql2 != '' ){
            $obj2s = Aux::findBySql($sql2)->all();
            foreach( $obj2s as $obj2 )
                if( !is_null($obj2->chk) )
                    $cad .= '<div title="' .$obj2->de .'"> <input type="checkbox" name="usuario2_' .$obj2->id .'" checked="checked" value="1"/> &nbsp;' .$obj2->email .'</div>';
                else
                    $cad .= '<div title="' .$obj2->de .'"> <input type="checkbox" name="usuario2_' .$obj2->id .'" value="1"/> &nbsp;' .$obj2->email .'</div>';
        }
        
        $cad .= '</div><hr />';
        foreach( $objs as $obj )
            if( !is_null($obj->chk) )
                $cad .= '<div> <input type="checkbox" name="usuario_' .$obj->id .'" checked="checked" value="1"/> &nbsp;' .$obj->usuario .'</div>';
            else
                $cad .= '<div> <input type="checkbox" name="usuario_' .$obj->id .'" value="1"/> &nbsp;' .$obj->usuario .'</div>';
        return $cad;
    } // eof 
    
    


    
} // class
