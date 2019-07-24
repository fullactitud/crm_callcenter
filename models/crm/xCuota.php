<?php
namespace app\models\crm;

use Yii;
use app\models\crm\Cuota;

class xCuota extends Cuota{
    public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.cuota';
    }
    
}
