<?php
namespace app\controllers\crm;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\xDbManager;
use app\models\AuthAssignment;
use app\components\crm\JSon;
use app\components\crm\JQGrid;
use app\components\crm\Menu;
use app\components\crm\Ayuda;
use app\models\Aux;


/**
 * Clase controller para la  ayuda
 */
class AyudaController extends Controller{
 
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
     * Despliega la ayuda
     *
     * @return string
     */
    public function actionDf(){
        $cad = '';

        $cad .= Ayuda::requerimientos();
        $cad .= Ayuda::paradigmas();
        $cad .= Ayuda::inicio();
        $vector = Ayuda::vector();
        foreach( $vector as $k => $v )
            $cad .= Ayuda::toHtml( $k, $v );        
        return $this->render('@views/crm/txt',array('txt' => $cad));
    } // eof 
    
} // class
