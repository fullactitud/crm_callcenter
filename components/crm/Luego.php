<?php
namespace app\components\crm;
use Yii;

use app\components\crm\Request;
use app\components\crm\Listado;
use app\components\crm\Usuario;
use app\models\Aux;

/**
 * Clase que maneja el proceso LLAMAR LUEGO
 */
class Luego{

    /**
     * Proceso para agregar un llamar luego
     */
     public static function add(){
         $id_usuario = Usuario::id();
         $cod_proyecto = Request::rq('cod_proyecto');
         $id_instrumento = Request::rq('id_instrumento');
         $cod_instrumento = Request::rq('cod_instrumento');
         $id_prospecto = Request::rq('id_prospecto');
         $fecha = Request::rq('fecha');
         $hora = Request::rq('hora');
         $obs = trim(Request::rq('obs').'');
         
         if( $fecha == '' || $hora == '' ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$cod_proyecto .'/' .$cod_instrumento .'/contactar1&x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&op=0&', 302 );
        }else{
             $llamar = $fecha.' '.$hora.':00';
             $sql = "with upsert as 
(update " .$cod_proyecto .".llamar_luego set id_usuario='" .$id_usuario ."', id_prospecto='" .$id_prospecto ."', id_instrumento='" .$id_instrumento ."', llamar='" .$llamar ."', obs='" .$obs ."' where id_usuario='" .$id_usuario ."' and id_prospecto='" .$id_prospecto ."' and id_instrumento='" .$id_instrumento ."' returning *)
    insert into " .$cod_proyecto .".llamar_luego (id_usuario, id_prospecto, id_instrumento, llamar, obs) 
select '" .$id_usuario ."','" .$id_prospecto ."','" .$id_instrumento ."','" .$llamar ."','" .$obs ."' where not EXISTS (select * from upsert)";
             Aux::findBySql($sql)->one();
             $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$cod_proyecto .'/' .$cod_instrumento .'/contactar1&x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&op=0&', 302 );
         }
     } // eof 


  

    
   
    
    
    
    

    /**
     * Formulario llamar luego
     */
    public static function df(){
        $cad = '';
        $cad .= '<link rel="stylesheet" type="text/css" href="' .Yii::$app->params['baseUrl'] .'css/clockpicker.css" />';
        $cad .= '<script type="text/javascript" src="' .Yii::$app->params['baseUrl'] .'js/clockpicker.js"></script>';
              
        $cod_proyecto = Request::rq('cod_proyecto');
        $id_instrumento = Request::rq('id_instrumento');
        $cod_instrumento = Request::rq('cod_instrumento');
        $id_prospecto = Request::rq('id_prospecto');        
        $id_usuario = Usuario::id();
        $fecha = date('Y-m-d');
        $hora = '14:00';
        
        $cad .= '<div class="titulos"> Agendar Llamada </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('llamar_luego');

        $sql = "select * from " .$cod_proyecto .".prospecto where st = '4' and id_instrumento='" .$id_instrumento ."' and id='" .$id_prospecto ."';";
        $prospecto = Aux::findBySql($sql)->one();

        $cad .= '<center><table width="100%"><tbody>';        
        $cad .= Encuesta::mostrarCabeceras( $cod_proyecto, $id_instrumento, $prospecto );
        $cad .= '</tbody></table></center>';
        
        $cad .= '<hr/>';
        $cad .= '<form class="form-horizontal" id="form32" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/luego/add" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="cod_proyecto" value="' .$cod_proyecto .'" />
        <input type="hidden" name="id_instrumento" value="' .$id_instrumento .'" />
        <input type="hidden" name="cod_instrumento" value="' .$cod_instrumento .'" />
        <input type="hidden" name="id_prospecto" value="' .$id_prospecto .'" />
        <input type="hidden" name="id_usuario" value="' .$id_usuario .'" />

<div class="form-group">

  <div class="col-sm-12" style="clear: both;"> Observaciones </div>
  <div class="col-sm-12" style="clear: both; height: 5.5em;">
    <textarea id="obs" class="form-control" name="obs" style="z-index:1; width: 99%;" ></textarea>
  </div>

  <div style="clear: both; height:2.2em;">
  <div class="control-label col-sm-1" for="de"> Fecha </div>
  <div class="col-sm-2">
    <input id="fecha" class="form-control" name="fecha" value="' .$fecha .'" placeholder="Fecha" style="z-index:1; width: 100%; text-align:center;" type="text" />
  </div>
  <div class="col-sm-2" >
    <input id="hora" class="form-control clockpicker " data-autoclose="true" name="hora" value="' .$hora .'" placeholder="Hora" style="z-index:1; width: 100%;text-align:center;" type="text" />
  </div>
  <div class="col-sm-1"> &nbsp; </div>
  <div class="col-sm-2">
<!-- http://127.0.0.1/crm/web/index.php?r=proyecto/dtg/rtfyghujk/contactar1&x3proy=dtg&x3inst=1&op=0&x3txt=Encuesta%20guardada%20correctamente -->
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/luego/df"></a>
<button type="button" class="btn btn-default" style="">   No Agendar   </button>
  </div>
  <div class="col-sm-2">
    <button type="submit" class="btn btn-primary" id="btnsalvar" style="">   Guardar   </button>
  </div>
</div>
</form>';

        $cad .= '<script type="text/javascript">' ."\n";
        $cad .= '$(".clockpicker").clockpicker().find("input").change(function(){console.log(this.value);});' ."\n";
        $cad .= '$( function(){ $( "#fecha" ).datepicker({dateFormat: \'yy-mm-dd\'}); });' ."\n";
        $cad .= '</script>' ."\n";



  

        
            $cad .= '<hr/>';
        
        return $cad;
    } // eof #######################################################


    // <button type="button" class=""





    
    /**
     * Listado llamar luego
     */    
    public static function listado( $cod_proyecto = '' , $id_instrumento = 0, $all = false ){
        $id_usuario = Usuario::id();
        if( $cod_proyecto == '' )
            $cod_proyecto = Request::rq('cod_proyecto');
        if( $id_instrumento == 0 ) 
            $id_instrumento = Request::rq('id_instrumento');
        
        $dato[] = 'ID';
        if( $all ) $dato[] = 'Usuario';
        $dato[] = 'Prospecto';
        $dato[] = 'Fecha';
        $dato[] = 'Estatus';
        $data[] = $dato; unset($dato);

        if( $all )
            $objs = Aux::findBySql("select ll.id, ll.llamar, s.de as estatus, p.id as id_prospecto, p.id as prospecto, u.nombres || ' ' || u.apellidos as usuario from " .$cod_proyecto .".llamar_luego ll inner join " .$cod_proyecto .".prospecto p on p.id=ll.id_prospecto inner join a.estatus s on ll.st=s.id inner join a.usuario u on u.id=ll.id_usuario where ll.id_instrumento='" .$id_instrumento ."' and (ll.st == '2' or ll.st == '5') order by ll.st asc, ll.llamar asc;")->all();
        else
            $objs = Aux::findBySql("select ll.id, ll.llamar, s.de as estatus, p.id as id_prospecto, p.id as prospecto from " .$cod_proyecto .".llamar_luego ll inner join " .$cod_proyecto .".prospecto p on p.id=ll.id_prospecto inner join a.estatus s on ll.st=s.id  where ll.id_usuario='" .$id_usuario ."' and ll.id_instrumento='" .$id_instrumento ."' and (ll.st == '2' or ll.st == '5') order by ll.st asc, ll.llamar asc;")->all();
        foreach( $objs as $obj ){
            $dato[] = $obj->id;
            if( $all ) $dato[] = $obj->usuario;
            $dato[] = $obj->id_prospecto .' ' .$obj->prospecto .'';
            $dato[] = $obj->llamar;
            $dato[] = $obj->estatus;
            $data[] = $dato; unset($dato);
        }
        return Listado::listado($data);
    } // eof


    
    /**
     * Seleccionar proximo prospecto de  llamar luego
     */
    public static function next( $cod_proyecto = '' , $id_instrumento = 0 ){
        $id_usuario = Usuario::id();
        if( $cod_proyecto == '' )
            $cod_proyecto = Request::rq('cod_proyecto');
        if( $id_instrumento == 0 ) 
            $id_instrumento = Request::rq('id_instrumento');
        
        $obj = Aux::findBySql("select ll.llamar, p.id as id_prospecto from " .$cod_proyecto .".llamar_luego ll inner join " .$cod_proyecto .".prospecto p on p.id=ll.id_prospecto where ll.id_usuario='" .$id_usuario ."' and ll.id_instrumento='" .$id_instrumento ."' and ll.st == '2' order by ll.llamar asc limit 1;")->one();  
        return $obj->id;
    } // eof
    
    

    
     public static function carga(){
         $cad = '';
       
         return $cad;
     } // eof 
    
    
} // class
