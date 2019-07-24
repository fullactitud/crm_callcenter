<?php
namespace app\models\crm;

use Yii;
use app\models\crm\xData;

class xData extends Data{
    public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.data';
    }
    
    

    
 
}
