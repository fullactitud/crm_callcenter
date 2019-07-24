<?php
namespace app\models\crm;

use Yii;
use app\models\crm\xDominio;

class xDominio extends Dominio{
        public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.dominio';
    }
 
}
