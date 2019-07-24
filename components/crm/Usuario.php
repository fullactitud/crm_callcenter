<?php
namespace app\components\crm;
use Yii;

use app\models\xUsuario;
use app\models\Aux;

/**
 * Clase helper para Usuarios
 */
class Usuario{
    /**
     * Retorna el ID de usuario
     */
    public static function id(){ return \Yii::$app->user->getId(); } 
} // class