<?php

namespace app\models\crm;

use Yii;
use app\models\crm\Opcion;

class xOpcion extends Opcion{
      public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.option';
    }
}
