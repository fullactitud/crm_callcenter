<?php

namespace app\models\crm;

use Yii;
use app\models\crm\Encuesta;

class xEncuesta_deprecate extends Encuesta{

    public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.encuesta';
    }


}
