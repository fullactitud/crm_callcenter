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
use app\components\crm\Llamada;
use app\models\Aux;


/**
 * Controler para manejar las llamadas
 */
class LlamadaController extends Controller{
    
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
     * Proecsos que se ejecutan antes que el controller carge
     */    
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } // eof

    


    
    /**
     * Acción por defecto para LLAMADAS
     */
    public function actionDf(){
        $cad = '<div class="titulos"> Llamadas Activas</div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('llamada'); 
        $cad .= '<hr />';

        $cad .= Llamada::listado();

        return $this->render('@views/crm/txt', array('txt'=>$cad));
    } // eof #######################################################


    /**
     * colgar llamada
     */
    public function actionColgar(){
        $cad = Llamada::Colgar();
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/llamada/df&x3txt=' .$cad, 302);
        return $this->render('@views/crm/txt', array('txt'=>$cad));
    } // eof #######################################################

    
    
} // class
