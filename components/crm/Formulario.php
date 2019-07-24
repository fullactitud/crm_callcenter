<?php
namespace app\components\crm;

use Yii;

use app\models\Proyecto;
use app\models\Usuario;
use app\models\Perfil;

use app\models\xMenu;
use app\models\xProyecto;
use app\models\xUsuario;
use app\models\xPerfil;
use app\models\Aux;


/**
 * Clase helper para formularios
 */
class Formulario{
    

    /**
     * Crea un formulario
     * ej. formulario( $form, $obj, $reg, $titulo )
     * @param $form, 
     * @param $obj, 
     * @param $reg, 
     * @param $titulo, 
     */
    public static function formulario1( $form, $obj, $reg, $titulo ){
        Yii::$app->layout = 'embebido';
       
        
        $cad = '<div class="titulos">' .$titulo .'</div>';
        $cad .= '<div><form class="form-horizontal" id="' .Cadena::texto2id($titulo) .'">';
        
        
        
        foreach( $form as $f ){            
            $valor = isset($reg->$f[1])?$reg->$f[1]:'';            
            
            switch( $f[2] ){
                
            case 'hidden':
                 $cad .= '<input type="hidden" id="'. $f[1] .'" name="'. $obj.'['.$f[1] .']" value="' .$valor .'"/>';
                break;

                
                
            case 'text':
                $cad .= '
<div class="form-group">
  <label class="control-label col-sm-4" for="'. $f[1] .'">'. Yii::t('app/crm', $f[0]) .'</label>
  <div class="col-sm-8">
    <input id="'. $f[1] .'" class="form-control" name="'. $f[1] .'" value="' .$valor .'" placeholder="'. Yii::t('app/crm', $f[0]) .'" style="z-index:1" type="text" onChange="update( \'' .$reg->id .'\', \'119\', \''. $f[1] .'\', $(\'#'. $f[1] .'\').val() );"/>
  </div>
</div>
';   
                break;

                
            case 'password':
                $cad .= '
<div class="form-group">
  <label class="control-label col-sm-4" for="'. $f[1] .'">'. Yii::t('app/crm', $f[0]) .'</label>
  <div class="col-sm-8">
    <input id="'. $f[1] .'" class="form-control" name="'. $f[1] .'" placeholder="'. Yii::t('app/crm', $f[0]) .'" style="z-index:1" type="password"/>
  </div>
</div>
';                
                break;

               
            case 'file':
                $cad .= '<div class="form-group">';
                if( $f[0] == 'imagen' || $f[1] == 'foto' ){
                    if( isset($reg->$f[1]) && file_exists(Yii::$app->basePath .'/web/img/' .$f[1] .'/' .$reg->$f[1]) ){
                        $foto = 'img/' .$f[1] .'/' .$reg->$f[1];
                    }else{
                        $foto = 'img/foto/0.png';
                    }
                    $cad .= '<center><img src="' .$foto .'" class="img-thumbnail img-responsive" style="width: 100px;" /></center>';
                }

             
                
                $cad .= ' <br/>

  <label class="control-label col-sm-4" for="'. $f[1] .'">'. Yii::t('app/crm', $f[0]) .'</label>
  <div class="col-sm-4">
    <input type="file" id="'. $f[1] .'" name="'. $f[1] .'" class="ui-button ui-corner-all ui-widget" style="font-size:0.75em;z-index:1;" />

  </div>
</div>
   ';
                break;
                
                

                
            case 'date':
                break;



                
            case 'textarea':
                $cad .= ' 
<div class="form-group">
  <label class="control-label col-sm-4" for="'. $f[1] .'">'. Yii::t('app/crm', $f[0]) .'</label>
  <div class="col-sm-8">
    <textarea id="'. $f[1] .'" class="form-control" name="'. $f[1] .'" placeholder="'. Yii::t('app/crm', $f[0]) .'" style="z-index:1" rows="5"></textarea>
  </div>
</div>
   ';
                break;
                
                

                
            case 'checkbox':
                
                $aux = 0;
                $cad .= '<div class="form-group"><label class="control-label col-sm-4" >'. Yii::t('app/crm', $f[0]) .'</label><div class="col-sm-8">';
                foreach( $f[4] as $k=>$v ){
                    $aux++;
                    if( $k == $valor ) $selected = ' checked="checked" ';
                    else $selected = '';
                    $cad .= '<div class="checkbox">
    <label class="control-label"><input type="checkbox" id="'. $f[1] .'_' .$aux .'" name="'. $obj.'['.$f[1] .']" value="' .$k .'" '.$selected .'/>' .Yii::t('app/crm', $v) .'</label>
  </div>';
                }
                $cad .= '</div></div>';
                break;

                
                
            case 'select':
                
                $cad .= '<div class="form-group"><label class="control-label" for="'. $f[1] .'">Select list:</label>';
                
                $cad .= '<select class="form-control" id="'. $f[1] .'" name="'. $obj.'['.$f[1] .']" >';
                foreach( $f[4] as $k=>$v ){
                    if( $k == $valor ) $selected = ' selected="selected" ';
                    else $selected = '';
                    $cad .= '<option value="' .$k .'" ' .$selected .'>' .Yii::t('app/crm', $v) .'</option>';
                }
                $cad .= '</select></div>';
                break;
                
            case 'radio':
                $aux = 0;
                $cad .= '<div class="form-group"><label class="control-label col-sm-4" for="'. $f[1] .'">'. Yii::t('app/crm', $f[0]) .':  &nbsp; &nbsp; </label><div class="col-sm-8">';
                foreach( $f[4] as $k=>$v ){
                    $aux++;
                    if( $k == $valor ) $selected = ' checked="checked" ';
                    else $selected = '';

                    
                    
                    
                    $cad .= '<div class="radio-inline">
    <label class="control-label"><input type="radio" id="'. $f[1] .'_' .$aux .'" name="'. $obj.'['.$f[1] .']" value="' .$k .'" '.$selected .'> ' .Yii::t('app/crm', $v) .'</label>
  </div>';
                }
                $cad .= '</div></div>';
                break;
                
            } // switch
        } // foreach
        
        $cad .= '
<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar">   '. Yii::t('app/crm', 'SALVAR') .'   </button>

  </div>
</div>';
        
        $cad .= '</form></div>';
        
        return $cad;
    } // eof #######################################################
    
    





} // class
