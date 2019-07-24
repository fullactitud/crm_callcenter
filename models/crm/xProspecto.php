<?php
namespace app\models\crm;

use Yii;
use app\models\crm\Prospecto;

class xProspecto extends Prospecto{
    
    public static $schema = null;
   
    public static function tableName(){
        return self::$schema .'.prospecto';
    }
    

    
    public function __construct(){
        parent::__construct();
    }
    
}
