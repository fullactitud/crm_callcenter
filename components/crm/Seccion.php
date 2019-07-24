<?php
namespace app\components\crm;
use Yii;
use app\models\Aux;

/**
 * Clase helper para SECCIONES
 */
class Seccion{
    /**
     * Despliega el titulo
     */    
    public static function titulo( $cad ){
        return '<div style="background-color:#eeeeee; line-height: 2.0em;">
  <div class="subtitulos" title="' .$cad .'"> &nbsp;  ' .$cad .'</div>
</div><br />';
    }
    
} // class

