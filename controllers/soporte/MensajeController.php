<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;
use app\models\AuthAssignment;

use app\components\crm\Reporte;
use app\components\crm\Grafico;
use app\components\crm\Formato;
use app\components\crm\Request;
use app\components\crm\Ayuda;
use app\components\crm\Listado;
use app\components\crm\Mensaje;
use app\models\Aux;

/**
 * Controler para manejar MENSAJES
 */
class MensajeController extends Controller{

    /**
     * Procesos que se ejecutan antes que el controler carge
     */
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } 


    /**
     * Acción por defecto de MENSAJES
     */
    public function actionDf(){        
        $cad = '';
        $data = array();
        $cad .= '<div class="titulos"> Mensajes </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('mensaje');
        $cad .= Mensaje::form();
        $cad .= '<hr />';
        
        return $this->render('@views/crm/txt',array('txt'=>$cad));
    } // eof 

    
    /**
     * Acción de solicitud de soporte
     */
    public function actionSolicitud(){        
        $cad = '';
        $data = array();
        $cad .= '<div class="titulos"> Solicitud de Soporte </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('mensaje_a_soporte');
        $cad .= Mensaje::form2();
        $cad .= '<hr />';
        
        return $this->render('@views/crm/txt',array('txt'=>$cad));
    } // eof 


    /**
     * Eliminar Mensaje
     */
    public function actionDel(){        
        $txt = Mensaje::del();
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/df&x3txt=' .$txt, 302);
    } // eof 


    /**
     * Agregar Mensaje
     */
    public function actionAdd(){
        $id = (int)Mensaje::add();
        if( $id == 0 ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/df&', 302 );
        }else{
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/df&id=' .$id , 302);
        }
    } // eof 


    /**
     * Agregar Mensaje
     */
    public function actionAdd2(){
        $id = (int)Mensaje::add2();
        if( $id == 0 ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/solicitud&', 302 );
        }else{
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/solicitud&id=' .$id , 302);
        }
    } // eof 

    
    /**
     * Marca mensaje como leido
     */
    public function actionLeido(){        
        Mensaje::leido();
    } // eof 

    
} // class
