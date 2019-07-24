<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;


/**
 * Controller para el login
 */
class SiteController extends Controller{
    
    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
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
    } // eof 

    /**
     * Acción por defecto
     *
     * @return string
     */
    public function actionIndex(){
         return $this->redirect(['df/index']);
    } // eof 

    /**
     * Acción Login
     *
     * @return string
     */
    public function actionLogin(){
        if( !Yii::$app->user->isGuest ){
            //return $this->goHome();
            return $this->redirect(['df/index']);
        }
 
        Yii::$app->layout = 'login';
        $model = new LoginForm();
        if( $model->load(Yii::$app->request->post()) && $model->login() ){
            return $this->goBack();
        }
        if( $model->load(Yii::$app->request->get()) && $model->login() ){
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);

    } // eof 

    /**
     * Acción Logout
     *
     * @return string
     */
    public function actionLogout(){
        Yii::$app->user->logout();

        return $this->redirect(['site/login']);
    } // eof 


} // class
