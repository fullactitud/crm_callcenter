<?php

namespace app\models\crm;

use Yii;
use app\models\crm\Entrada;

class xEntrada extends Entrada{
    public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.entrada';
    }
}
