<?php
namespace app\models\crm;

use Yii;


class xInstrumento extends Instrumento{

    public static $schema = null;
    
    public static function tableName(){
        return self::$schema .'.instrumento';
    }



    
}
