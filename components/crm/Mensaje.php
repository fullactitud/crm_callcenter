<?php
namespace app\components\crm;
use Yii;

use app\components\crm\Request;
use app\components\crm\Mensaje;
use app\components\crm\Ayuda;
use app\components\crm\Listado;
use app\components\crm\Usuario;
use app\models\Aux;

/**
 * Clase helper para mensajes
 */
class Mensaje{
    
    
    /**
     * Mostrar mensajes
     */
    public static function mostrar(){

        // MENSAJES DEL SISTEMA
        $cad = '';
        $cad0 = Request::rq('x3txt') .'';
        if( $cad != '' ){
            if( substr($cad,0,1) != '*' )
                $cad .= '<div class="alert alert-success"><ul style="position:relative; left:1.0em;">' .$cad0 .'</ul></div>';
            else
                $cad .= '<div class="alert alert-danger"><ul style="position:relative; left:1.0em;">' .substr($cad0,1) .'</ul></div>';
        }

        // MENSAJES DE OTROS USUARIOS
        $id_usuario = Usuario::id();
        $sql = "select m.*, u.nombres || ' ' || u.apellidos as usuario, u.foto 
from a.mensaje m 
inner join a.usuario u on u.id=m.id_usuario 
inner join a.mensaje_usuario mu on m.id=mu.id_mensaje 
where m.st in ('1','2') and mu.st = '2' and mu.id_usuario = '" .$id_usuario ."' order by m.id asc;";
        $obj0s = Aux::findBySql($sql)->all();
        foreach( $obj0s as $obj ){
            $cad .= '<div id="div_msn_' .$obj->id .'" class="alert alert-success" ><table style="width:100%;"><tbody><tr>';
            
            if( !is_null($obj->foto) && $obj->foto!='' ) $cad .= '<td style="width:90%;">';
            else $cad .= '<td>';
              
            $cad .= '<b>' .$obj->usuario .'</b>:';
            
            $cad .= '<div style="padding: 1.0em;">';
            $cad .= '<p>' .$obj->de .'</p>';
            $cad .= '</div>';
            
            $cad .= '<div style="position: relative; text-align: right; right: 4.0em;">';
            $cad .= '<div class="btn btn-default" onClick="';
            $cad .= '$(\'#div_msn_' .$obj->id .'\').hide();';
            $cad .= 'carga( \'div_msn_' .$obj->id .'\', \'id=' .$obj->id .'\', \'leido\', \'soporte/mensaje\' );"> ';
                if( $obj->tp == 0 ) $cad .= 'Marcar como leido';
                else if( $obj->tp == 1 ) $cad .= 'Marcar como visto';
            $cad .= '</div></div>';
            
            $cad .= '</td>';
            if( !is_null($obj->foto) && $obj->foto!='' )
                $cad .= '<td style="width:10%;"><img src="img/foto/' .$obj->foto .'" style="width:100%;"/></td>';
            
            $cad .= '</tr></tbody></table></div>';
        }
                      
        return $cad;
    } // eof 
    




    
    /**
     * Marca un mensaje como leido
     */    
    public static function leido(){
        $id = (int)Request::rq('id');
        if( $id > 0 ){
            $id_usuario = Usuario::id();
            $obj = Aux::findBySql("select tp from a.mensaje where id='" .$id ."' limit 1;")->one();
            if( $obj->tp == 0 ) 
                Aux::findBySql("update a.mensaje_usuario set st='5', up='" .date('Y-m-d H:i:s') ."'::timestamp where id_mensaje='" .$id ."' and id_usuario='" .$id_usuario ."' ;")->one();
            else if( $obj->tp == 1 )
                Aux::findBySql("update a.mensaje_usuario set st='4' where id_mensaje='" .$id ."' and id_usuario='" .$id_usuario ."' ;")->one();
        }
    } // eof 



    /**
     * Listado de mensajes de un usuario
     */
    public static function listado(){
        $cad = '';
        $sql6 = '';
        
        $dato[] = 'Mensaje';
     
        $dato[] = 'Vistos';
        $dato[] = 'Editar';
        $dato[] = 'Eliminar';
        $data[] = $dato; unset($dato);

        $id_usuario = Usuario::id();
        if( $id_usuario != 1 )
            $sql6 = " and id_usuario = '" .$id_usuario ."' ";
        
        $obj0s = Aux::findBySql("select m.*, s.de as estatus, u.nombres || ' ' || u.apellidos as usuario
from a.mensaje m 
inner join a.estatus s on s.id=m.st 
inner join a.usuario u on u.id=m.id_usuario 
where m.st != '0' " .$sql6 ." order by m.id desc;")->all();
        foreach( $obj0s as $reg0 ){

                $dato[] = $reg0->de;

            $sql1 = "select count(id) as count from a.mensaje_usuario where st in ('4','5') and id_mensaje='" .$reg0->id ."';";
            $reg1 = Aux::findBySql($sql1)->one();
           

            $dato[] = (int)$reg1->count;
            
            
            $dato[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/df&id=' .$reg0->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reg0->de .'"/></a>';
                $dato[] = '<a href="javascript:if( confirm(\'Eliminar: ' .$reg0->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/del&id=' .$reg0->id .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reg0->de .'"/></a>';
                $data[] = $dato; unset($dato);
        }
        return Listado::listado( $data );
    } // eof 






    /**
     * Listado de mensajes
     */
    public static function listado2(){
        $dato[] = 'Mensaje';
        $dato[] = 'Estado';
        $data[] = $dato; unset($dato);
        $id_usuario = Usuario::id(); //        $id_usuario = \Yii::$app->user->identity->id;
        $obj0s = Aux::findBySql("select m.*, s.de as estatus, u.nombres || ' ' || u.apellidos as usuario
from a.mensaje m 
inner join a.estatus s on s.id=m.st 
inner join a.usuario u on u.id=m.id_usuario 
where m.st != '0'  and id_usuario = '" .$id_usuario ."'  order by m.id desc;")->all();
        foreach( $obj0s as $reg0 ){
            $sql1 = "select s.id, s.de as estatus from a.estatus s where s.id = (select max(st) from a.mensaje_usuario mu where mu.id_mensaje='" .$reg0->id ."');";
            $reg1 = Aux::findBySql($sql1)->one();
            if( $reg1->id != 0 && $reg1->id != 5 ){
                $dato[] = '<u><i>' .substr($reg0->fecha,0,19).'</i></u>. &nbsp; &nbsp;'.$reg0->de;    
                $dato[] = $reg1->estatus;
                $data[] = $dato; unset($dato);
            }
            unset($reg1);
        }
        return Listado::listado( $data );
    } // eof 
    
    




    /**
     * Formulario para agregar mensajes a cualquier usuario
     */
    public static function form(){
        $cad = '';
        $id = (int)Request::rq('id');
        $de = '';
        $st = 1;
        $tp = 0;
        $id_usuario = Usuario::id(); 
        $fecha = date('Y-m-d');
        
        if( $id > 0 ){
            $sql6 = "select * from a.mensaje where id='" .$id ."' and id_padre = '0' ";
            if( $id_usuario != 1 )
                $sql6 = " and id_usuario = '" .$id_usuario ."' ";
            $obj6s = Aux::findBySql($sql6)->all();
            if( count($obj6s) > 0 ){
                $de = $obj6s[0]->de;
                $st = $obj6s[0]->st;
                $tp = $obj6s[0]->tp;
                $id_usuario = $obj6s[0]->id_usuario;
                $fecha = substr($obj6s[0]->fecha, 0, 10);
            }
        }
        $height0 = '5.6';
        $height = '2.8';
        
        $cad = '';

        $cad .= '<script>
  $( function(){$( "#fecha" ).datepicker({ dateFormat: \'yy-mm-dd\' });} );
  </script>';
        
        $cad .= '<form class="form-horizontal" id="datosf" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/add" method="POST" enctype="multipart/form-data"> 
<input type="hidden" id="id" name="id" value="' .$id .'"/>
';
        
        
        
        $cad .= '<div class="form-group"> <br/>';
        $cad .= '<div class="col-sm-8">';
        
        
        
        
        $cad .= '<div style="clear:both;  height: ' .$height .'em;"> Cuerpo del Mensaje </div>';
        $cad .= '<div class="" style="clear:both;  height: ' .$height0 .'em;"><textarea id="de" class="form-control" name="de"  placeholder="Mensaje" style="z-index:1; width: 100%;" >' .$de .'</textarea></div>';


        
       $cad .= '<div style="clear:both; height: ' .$height .'em;">';
       
       $cad .= '<div class="control-label col-sm-1"> Fecha </div>';
       $cad .= '<div class="col-sm-3"><input id="fecha" class="form-control" name="fecha" value="' .$fecha .'" style="z-index:1; width: 100%;" type="text" /></div>';
$cad .= '<div class="col-sm-1">&nbsp;</div>';       


       $cad .= '<div class="control-label col-sm-1"> Tipo </div>
<div class="col-sm-2"><select id="tp" name="tp" class="form-control" >';
       if( $tp == 0 ) 
           $cad .= '<option value="0" selected="selected"> &nbsp; Notificación</option>';
       else
           $cad .= '<option value="0"> &nbsp; Notificación</option>';
       if( $tp == 1 )
           $cad .= '<option value="1" selected="selected"> &nbsp; Solicitud</option>';
       else
           $cad .= '<option value="1"> &nbsp; Solicitud</option>';
       $cad .= '</select></div>';
       $cad .= '<div class="col-sm-1">&nbsp;</div>';
       
       $cad .= '<div class="control-label col-sm-1"> Estado </div>
<div class="col-sm-2"><select id="st" name="st" class="form-control" >';
       if( $st == 1 ) 
           $cad .= '<option value="1" selected="selected"> &nbsp; Activo</option>';
       else
           $cad .= '<option value="1"> &nbsp; Activo</option>';
       if( $st == 3 )
           $cad .= '<option value="3" selected="selected"> &nbsp; En espera</option>';
       else
           $cad .= '<option value="3"> &nbsp; En espera</option>';
       
       $cad .= '<option value="0"> &nbsp; Eliminar</option>';
       $cad .= '</select></div>';



       $cad .= '</div><div style="clear:both; height: ' .$height .'em;">';

       $cad .= '<div class="col-sm-3"></div>
  <div class="col-sm-3">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/df">
      <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:100%;">   Recetear   </div>
    </a>
  </div>
  <div class="col-sm-1"></div>
  <div class="col-sm-3">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:100%;">   Salvar   </button>
  </div>';
       
       
       $cad .= '</div>';
       
       $cad .= '<hr />';       
          
       
       $cad .= Mensaje::listado();
       $cad .= '</div>';
       $cad .= '<div class="col-sm-1">&nbsp;</div>';
       $cad .= '<div class="col-sm-3">' .Mensaje::usuarios( $id ) .'</div>';
       
       $cad .= '';
       
$cad .= '</div>
</form>';
       return $cad;
    } // eof 
    






    /**
     * Formulario para agregar mensajes para soporte
     */
    public static function form2(){
        $cad = '';
        $id = (int)Request::rq('id');
        $de = '';
        $st = 1;
        $tp = 0;
        $id_usuario = Usuario::id(); 
        $fecha = date('Y-m-d');
        
        if( $id > 0 ){
            $sql6 = "select * from a.mensaje where id='" .$id ."' and id_padre = '0' ";
            if( $id_usuario != 1 )
                $sql6 = " and id_usuario = '" .$id_usuario ."' ";
            $obj6s = Aux::findBySql($sql6)->all();
            if( count($obj6s) > 0 ){
                $de = $obj6s[0]->de;
                $st = $obj6s[0]->st;
                $tp = $obj6s[0]->tp;
                $id_usuario = $obj6s[0]->id_usuario;
                $fecha = substr($obj6s[0]->fecha, 0, 10);
            }
        }
        $height0 = '11.0';
        $height = '2.8';
        
        $cad = '';


        
        $cad .= '<form class="form-horizontal" id="datosf" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/add2" method="POST" enctype="multipart/form-data"> <input type="hidden" id="id" name="id" value="' .$id .'"/>';
        
        
        
        $cad .= '<div class="form-group"> <br/>';
        
        $cad .= '<div class="col-sm-12">';

        $cad .= '<div class="" style="clear:both;  height: ' .$height0 .'em;"><textarea id="de" class="form-control" name="de"  placeholder="Mensaje" style="z-index:1; width: 100%; height: 10.0em;" >' .$de .'</textarea></div>';
        $cad .= '</div>';

        
       $cad .= '<div style="clear:both; height: ' .$height .'em;">';
       $cad .= '<div class="col-sm-3"></div>
  <div class="col-sm-3">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/solicitud">
      <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:100%;">   Recetear   </div>
    </a>
  </div>
  <div class="col-sm-1"></div>
  <div class="col-sm-3">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:100%;">   Salvar   </button>
  </div>';
       $cad .= '</div>';

       $cad .= '<div><br /><hr /><br /></div>';
       $cad .= Mensaje::listado2();
       $cad .= '</div>';

$cad .= '</form>';
       return $cad;
    } // eof 


    
    



    /**
     * Usuarios a los que se les puede enviar mensajes
     */
    public static function usuarios( $id ){
        $cad = '';
        $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario, mu.id as chk
from a.usuario u
inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
 left join a.mensaje_usuario mu on mu.id_usuario = u.id and mu.id_mensaje='" .$id ."' 
where u.st = '1' order by usuario asc;";
       
        $objs = Aux::findBySql($sql)->all();
        $cad .= '<h3> Destinatarios </h3>';
        $cad .= '<div> <input type="radio" id="destino_1" name="destino" value="1"/> &nbsp; Todos </div>';
        $cad .= '<div> <input type="radio" id="destino_2" name="destino" value="2"/> &nbsp; Personal de Soporte </div>';
        $cad .= '<div> <input type="radio" id="destino_3" name="destino" value="3"/> &nbsp; Teleoperadores </div>';
        $cad .= '<div> <input type="radio" id="destino_4" name="destino" value="4"/> &nbsp; Supervisores </div>';
        $cad .= '<div> <input type="radio" id="destino_5" name="destino" value="5"/> &nbsp; Clientes </div>';
        $cad .= '<hr />';
        foreach( $objs as $obj )
            if( !is_null($obj->chk) )
                $cad .= '<div> <input type="checkbox" name="usuario_' .$obj->id .'" checked="checked" value="1"/> &nbsp;' .$obj->usuario .'</div>';
            else
                $cad .= '<div> <input type="checkbox" name="usuario_' .$obj->id .'" value="1"/> &nbsp;' .$obj->usuario .'</div>';
        return $cad;
    } // eof 









    /**
     * Proceso para agregar mensajes a un usuario
     */
    public static function add(){
        $padre_valor = '';
        $padre_campo = '';
        $padre_asign = '';
        
        $fecha_valor = '';
        $fecha_campo = '';
        $fecha_asign = '';
        
        $txt = Mensaje::mostrar();
 

        $id = (int)Request::rq('id');
        $id_padre = (int)Request::rq('id_padre');
        $id_usuario = Usuario::id();
        $fecha = Request::rq('fecha').'';
        $tp = (int)Request::rq('tp');
        $de = trim(Request::rq('de'));
        $st = (int)Request::rq('st');
   

        if( $fecha != '' ){
            $fecha_asign = ", fecha='" .$fecha ."' ";
            $fecha_campo = ', fecha ';
            $fecha_valor = ", '" .$fecha ."' ";
        }

            $padre_asign = ", id_padre='" .$id_padre ."' ";
            $padre_campo = ', id_padre ';
            $padre_valor = ", '" .$id_padre ."' ";
        
        
        if( $de == '' ){
            return 0;
        }else{

            $sql = "WITH upsert AS 
(UPDATE a.mensaje SET de='" .$de ."', id_usuario='" .$id_usuario ."', tp='" .$tp ."', st='" .$st ."' " .$fecha_asign ." " .$padre_asign ." WHERE id='" .$id ."' RETURNING *)
    INSERT INTO a.mensaje ( de, id_usuario, tp, st " .$fecha_campo ." " .$padre_campo ." ) 
SELECT '" .$de ."','" .$id_usuario ."','" .$tp ."','" .$st ."' " .$fecha_valor ." " .$padre_valor ." WHERE NOT EXISTS (SELECT * FROM upsert)";
            $obj = Aux::findBySql($sql)->one();
            $sql = "select id from a.mensaje where de='" .$de ."' and id_usuario='" .$id_usuario ."' order by id desc;";
            $obj = Aux::findBySql($sql)->one();
            

            // agregar mensaje_usuario
            Aux::findBySql("delete from a.mensaje_usuario where id_mensaje='" .$obj->id ."';")->one();
            
            $dest = (int)Request::rq('destino');
            switch( $dest ){
            case '1': // todos
                $sql = "select distinct id from a.usuario u ";
                $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                $sql .= " where u.st = '1' ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u )
                    Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
                break;
            case '2': // Personal de Soporte
                $sql = "select distinct id from a.usuario u ";
                $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                $sql .= " where u.st = '1' and aa.item_name in ('soporte','admin') ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u )
                    Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
                break;
            case '3': // Teleoperadores
                $sql = "select distinct id from a.usuario u ";
                $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                $sql .= " where u.st = '1' and aa.item_name in ('tlo') ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u )
                    Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
                break;
            case '4': // Supervisores
                $sql = "select distinct id from a.usuario u ";
                $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                $sql .= " where u.st = '1' and aa.item_name in ('supervisor') ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u )
                    Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
                break;
            case '5': // Clientes
                $sql = "select distinct id from a.usuario u ";
                $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                $sql .= " where u.st = '1' and aa.item_name in ('cliente') ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u )
                    Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
                break;
            }
            
            
            $sql = "select distinct id from a.usuario u ";
            $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
            $sql .= " where u.st = '1' ;";
            $objs = Aux::findBySql($sql)->all();
            foreach( $objs as $u ){
                if( !is_null(Request::rq('usuario_' .$u->id)) )
                    Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
            }
            
            
            
            
            
            return $obj->id;
        } // else 



        
    } // eof 






    /**
     * Proceso para agregar mensajes para soporte
     */
    public static function add2(){
        $padre_valor = '';
        $padre_campo = '';
        $padre_asign = '';
        
        $fecha_valor = '';
        $fecha_campo = '';
        $fecha_asign = '';
        
        $txt = Mensaje::mostrar();

        $id = (int)Request::rq('id');
        $id_padre = (int)Request::rq('id_padre');
        $id_usuario = Usuario::id(); 
        $fecha = date('Y-m-d H:i:s');
        $tp = '1';
        $de = trim(Request::rq('de'));
        $st = '2';
   

        
        $fecha_asign = ", fecha='" .$fecha ."' ";
        $fecha_campo = ', fecha ';
        $fecha_valor = ", '" .$fecha ."' ";
        
        
        $padre_asign = ", id_padre='" .$id_padre ."' ";
        $padre_campo = ', id_padre ';
        $padre_valor = ", '" .$id_padre ."' ";
        
        
        if( $de == '' ){
            return 0;
        }else{

            $sql = "WITH upsert AS 
(UPDATE a.mensaje SET de='" .$de ."', id_usuario='" .$id_usuario ."', tp='" .$tp ."', st='" .$st ."' " .$fecha_asign ." " .$padre_asign ." WHERE id='" .$id ."' RETURNING *)
    INSERT INTO a.mensaje ( de, id_usuario, tp, st " .$fecha_campo ." " .$padre_campo ." ) 
SELECT '" .$de ."','" .$id_usuario ."','" .$tp ."','" .$st ."' " .$fecha_valor ." " .$padre_valor ." WHERE NOT EXISTS (SELECT * FROM upsert)";
            $obj = Aux::findBySql($sql)->one();
            $sql = "select id from a.mensaje where de='" .$de ."' and id_usuario='" .$id_usuario ."' order by id desc;";
            $obj = Aux::findBySql($sql)->one();
            



            // Personal de Soporte
            $sql = "select distinct id from a.usuario u ";
            $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
            $sql .= " where u.st = '1' and aa.item_name in ('soporte','admin') ;";
            $objs = Aux::findBySql($sql)->all();
            foreach( $objs as $u )
                Aux::findBySql("insert into a.mensaje_usuario (id_mensaje, id_usuario) values ('" .$obj->id ."','" .$u->id ."');")->one();
                
                
            return $obj->id;
        } // else 

    } // eof 




    
    /**
     * Elimina un mensaje
     */
    public static function del(){
        $id = (int)Request::rq('id');
        $res = Aux::findBySql("select id from a.mensaje_usuario where id_mensaje='" .$id ."' and st='5';")->all();
        if( count($res) > 0 ){
            $cad = 'El mensaje no se puede eliminar, porque ya fue entregado&id=' .$id;
        }else{
            $res = Aux::findBySql("delete from a.mensaje where id='" .$id ."';")->all();
            $cad = 'Mensaje eliminado';
        }
        return $cad;
    } // eof 


    
    public static function leidos(){
        $id = (int)Request::rq('id');
        $cad = '';
        
        return $cad;
    } // eof 

    
  

    
} // class
