<?php

namespace app\models\crm;

use Yii;
use app\models\crm\Cabecera;

class xCabecera extends Cabecera{
    public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.cabecera';
    }
}
