<?php
namespace app\components\crm;

use Yii;

use app\components\crm\Usuario;
use app\components\crm\Request;
use app\components\crm\Formato;
use app\components\crm\Cadena;

use app\models\Aux;

use app\models\crm\Instrumento;
use app\models\crm\xInstrumento;
use app\models\AuthAssignment;




/**
 * Implanta la Teleoperación de un instrumento
 */
class Encuesta{

    public static $orden = array();
    public static $codigo = array();

    

    /**
     * Muestra la barrida, permitiendola actualizarla
     */
    public static function verBarrida(){
        $cad = '';
        $cod_proyecto = Request::rq('cod_proyecto');
        $id_instrumento = (int)Request::rq('id_instrumento');
        if( $cod_proyecto == '' ) $cod_proyecto = Request::rq('x3proy');
        if( $id_instrumento == '' ) $id_instrumento = (int)Request::rq('x3inst');

        $obj = Aux::findBySql("select * from " .$cod_proyecto .".barrida where id_instrumento = '" .$id_instrumento ."' and st = '1' order by id desc;")->one();

        $cad = '<div class="titulos"> &nbsp; Barrida </div>';
        $cad .= Ayuda::toHtml('barrida');
        $cad .= '<div style="text-align: center;"><form id="form' .$id_instrumento .'" name="form' .$id_instrumento .'" action="' .Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/barridaup&" method="POST">' ."\n";
        $cad .= '<input type="hidden" id="cod_proyecto" name="cod_proyecto" value="' .$cod_proyecto .'" />' ."\n";
        $cad .= '<input type="hidden" id="id_instrumento" name="id_instrumento" value="' .$id_instrumento .'" />' ."\n";
       
        $cad .= '<hr><h2 style="color: #000000;"> Barrida Actual: <input type="text" id="barrida" name="barrida" value="' .$obj->barrida .'" style="font-weight: 700; border: none;" /></h2><hr />';
        $cad .='<button class="btn btn-default" type="button" onClick="$(\'#barrida\').val(\'0\');"> Reiniciar </button> &nbsp; ';
        $cad .='<button class="btn btn-primary" type="submit"> Actualizar Barrida </button>';
        $cad .= '</form></div>';    
        return $cad;      
    } // eof
    


    
    /**
     * Actualiza la barrida de un instrumento
     */    
    public static function upBarrida(){
        $cod_proyecto = Request::rq('cod_proyecto');
        if( $cod_proyecto == '' ) $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = (int)Request::rq('id_instrumento');
        if( $id_instrumento == '' ) $id_instrumento = (int)Request::rq('x3inst');
        $barrida = (int)Request::rq('barrida');
        $barrida++;
        Aux::findBySql("update " .$cod_proyecto .".barrida set barrida='". $barrida ."' where id_instrumento = '" .$id_instrumento ."' and st = '1';")->all();
        Yii::$app->getResponse()->redirect(Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/barrida&x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&', 302);
    } // eof
    


    
    /**
     * Retorna una instancia de prospecto
     */
    public static function prospecto2( $schema, $idInstrumento, $idProspecto = null ){
        if( (int)$idProspecto != 0 )
            $sql = "select p.* from " .$schema .".prospecto p where p.id = '" .$idProspecto ."' limit 1;";
        else{
            $sql = "select de,columna from " .$schema .".cabecera c where st in (1,2,5) and c.id_instrumento='" .$idInstrumento ."' and c.de='ID ZONA' limit 1;";
            $cab = Aux::findBySql($sql)->one();

            $fecha = (Request::rq('x3fecha'))?Request::rq('x3fecha'):date('Y-m-d');
            $sql = "select p.* from " .$schema .".prospecto p inner join " .$schema .".dominio d on d.cod = p." .$cab->columna ." inner join " .$schema .".cuota c on c.id_dominio = d.id where p.st in (1,2,1201) and p.id_instrumento='" .$idInstrumento ."' and c.fecha_encuesta='" .$fecha ."' order by de ASC limit 1;";
           
        }
      
        return Aux::findBySql($sql)->one();
    } // eof #######################################################





    /**
     * Verifica si la encuesta es efectiva
     */    
    public static function efectiva( $cod_proyecto = null, $id_instrumento = 0 ){
        if( $cod_proyecto != null && (int)$id_instrumento > 0 ){
            $es = Aux::findBySql("select codigo from " .$cod_proyecto .".entrada where id_instrumento = '" .$id_instrumento ."' and efectiva = '1';")->all();
            foreach( $es as $e ){
                if( (int)Request::rq('x3campo_' .$e->codigo) > 0 || Request::rq('x3campo_' .$e->codigo).'' != '' ){
                    return true;
                }
            }
        }    
        return false;
    } // eof
    
    

    /**
     * Salva la encuesta
     */
    public static function salvar(){
        $vct1 = array();
        $vct2 = array();
        $cod_proyecto = Request::rq('cod_proyecto');
        $id_instrumento = (int)Request::rq('id_instrumento');
        $op = (int)Request::rq('op');
        $idUsuario = Usuario::id();
        $idProspecto = (int)Request::rq('id_prospecto');
        $idCuota = (int)Request::rq('id_cuota');
        
        $config = self::configuracion();
        $efectiva = self::efectiva( $cod_proyecto, $id_instrumento );
        
        
        $cabeceras = Aux::findBySql("select de, columna from " .$cod_proyecto .".cabecera where id_instrumento = '" .$id_instrumento ."' and st in (1) order by orden asc limit 1000;")->all();        
        foreach( $cabeceras as $v ){
            $aux = Request::rq('x3campo_' .$v->de);
            if( !is_null($aux) && $aux != '' ){
                $vct1[] = $v->columna;
                $vct2[] = "'" .$aux ."'";
            }
        }
        $cad1 = substr(implode(',', $vct1),0,-1);
        $cad2 = substr(implode(',', $vct2),0,-1);
        $cant = count($vct1);
        
        
        // DEBO PARAMETRIZAR ESTO
        if( $efectiva == true ) $tipi = "1";
        else $tipi = '2';


        
        $sql = "select id_llamada from " .$cod_proyecto .".usuario_prospecto where id_usuario = '" .$idUsuario ."' and id_prospecto = '" .$idProspecto ."';";
        $obj = Aux::findBySql($sql)->all();
        if( count($obj) ){
            
            $sqlEnc = "insert into " .$cod_proyecto .".encuesta (id_usuario, id_prospecto, id_tipificacion, st) values ('" .$idUsuario ."', '" .$idProspecto ."', '" .$tipi ."' ,'5');";
            Aux::findBySql($sqlEnc)->one();
            $sqlEnc = "select id from " .$cod_proyecto .".encuesta where id_usuario = '" .$idUsuario ."' and st = '5' order by id DESC limit 1;";
            $oo = Aux::findBySql($sqlEnc)->one();
            $seqEnc = $oo->id;
            
            $sql1 = "update " .$cod_proyecto .".prospecto set ";
            for( $i=0 ; $i < $cant ; $i++ ) $sql1 .= " " .$vct1[$i] ." = " .$vct2[$i] .", ";
            $sql1 .= " up = now(), ";
            $sql1 .= " fin = now(), ";
            $sql1 .= " encuesta = '" .$seqEnc ."', ";
            $sql1 .= " st = '5', ";
            $sql1 .= " id_tipificacion = '" .$tipi ."' ";
            $sql1 .= " where id='" .$idProspecto ."';";
            

            $llamada = $obj[0]->id_llamada;
            

            
            $sql2 = "update " .$cod_proyecto .".usuario_prospecto set st='5' where id_usuario='" .$idUsuario ."' and id_instrumento='" .$id_instrumento ."' and id_prospecto='" .$idProspecto ."';";
            
            // DEBO PARAMETRIZAR ESTO
            if( $efectiva ){
                $sql3 = "update " .$cod_proyecto .".cuota set conteo=(conteo + 1) where id_instrumento='" .$id_instrumento ."' and id='" .$idCuota ."';";
                $sql4 = "update " .$cod_proyecto .".llamada set st='5', id_tipificacion='1', fin=now() where id='" .(int)$llamada ."';";
            }else{
                $sql3 = 'select 1';
                $sql4 = "update " .$cod_proyecto .".llamada set st='5', id_tipificacion='2', fin=now() where id='" .(int)$llamada ."';";
            }
            
            $r = Aux::findBySql($sql1)->one(); // Prospecto
            $r = Aux::findBySql($sql2)->one(); // usuario_prospecto
            $r = Aux::findBySql($sql3)->one(); // cuota
            $r = Aux::findBySql($sql4)->one(); // llamada
            
            // CUELGA TODAS LAS RESTANTES LLAMADAS DEL TLO
            $sql5 = "update " .$cod_proyecto .".llamada set st='6',id_tipificacion='3',fin=now() where id_usuario='" .$idUsuario ."' and st='4';";
            Aux::findBySql($sql5)->one();
            
            // HABILITA LOS RESTANTES PROSPECTOS DEL TLO
            $sqlP = "select id_prospecto from " .$cod_proyecto .".usuario_prospecto where id_usuario='" .$idUsuario ."' and id_instrumento='" .$id_instrumento ."' and st='4';";
            $objPs = Aux::findBySql($sqlP)->all();
            foreach( $objPs as $regp ){
                $sqlX = "update " .$cod_proyecto .".prospecto set id_tipificacion='3', st='2', barrida=(barrida -1) where st='4' and id='" .$regp->id_prospecto ."' and tlo='" .$idUsuario ."' and id_instrumento='" .$id_instrumento ."';";
                Aux::findBySql($sqlX)->one();
            } // foreach 
            $sql6 = "delete from " .$cod_proyecto .".usuario_prospecto where id_usuario='" .$idUsuario ."' and id_instrumento='" .$id_instrumento ."' and st='4';";
            Aux::findBySql($sql6)->one();
            
            $msn = 'Encuesta guardada correctamente';
        }else
            $msn = 'La encuesta <b>NO</b> fue guardada';
        
        $cad = 'proyecto/' .$cod_proyecto .'/' .$config->cod_instrumento .'/';
        $cad .= ( $op == 0 )?'contactar1':'ver';
        return $cad .'&x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&op=' .$op .'&x3txt=' .$msn;
    } // eof ##################################################
    
    
    
    

    /**
     * Establece cuantos campos de cabecera por linea se muestran al TLO
     */
    public static function presentacion( $present = null ){
        switch( $present ){  
        case 'x2': for( $i=0; $i<50; $i++ ) $vct2[] = '2'; break;
        case 'x3': for( $i=0; $i<50; $i++ ) $vct2[] = '3'; break;
        case 'x4': for( $i=0; $i<50; $i++ ) $vct2[] = '4'; break;
        case 'x5': for( $i=0; $i<50; $i++ ) $vct2[] = '5'; break;
        case 'x6': for( $i=0; $i<50; $i++ ) $vct2[] = '6'; break;
        default:
            $presentacion = $present;
            $vct2 = explode(',', $presentacion); // '2,2';
            break;
        }
        return $vct2;
    } // eof #######################################################

   


    /**
     * Despliega el listado de dominios o dispara la encuesta
     * según sea el caso
     */
    public static function contactar1( $op = 0 ){
       
        $cad = '';
        $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = Request::rq('x3inst');
        
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('teleoperar');

        if( is_null($op) ) $op = (int)Request::rq('op');
        
        
        $instrumentos = Aux::findBySql("select codigo, cuotas, dominios from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."'")->all();
        if( $op != 0 && count($instrumentos) > 0 ){
            $cad .= self::contactarSinCuota( $cod_proyecto, $instrumentos[0]->codigo, $id_instrumento, 1 );
                                  
        }else if( count($instrumentos) > 0 ){
            $cad .= '<center>';
            $cad .= '<div class="ui-corner" style="position:relative; width: 100%;">';
            $cad .= ' <div class="row">';
           
            if( $instrumentos[0]->cuotas == 1 )
                $cad .= self::contactarConCuota( $cod_proyecto, $instrumentos[0]->codigo, $id_instrumento, $op );
            else
                $cad .= self::contactarSinCuota( $cod_proyecto, $instrumentos[0]->codigo, $id_instrumento, $op );
            $cad .=  '</div>';            
            $cad .= '</div>';
            $cad .= '<div id="x3divContactables" class="row"></div>';
            $cad .= '</center>';            
        }else
            return 'Error al cargar el instrumento';


        $cad .= '<div id="x3DivEncuesta" class="row"></div>';
        return $cad;
        
    } // eof #######################################################



   
    /**
     * Despliega el listado de dominios, si se usan cuotas
     */
    public static function contactarConCuota( $cod_proyecto, $cod_instrumento, $id_instrumento, $op = 0 ){
        
        $cad =   '<label class="control-label col-sm-4 ui-corner-top ui-state-default ui-state-active ui-state-focus" style="margin:0; padding:0;" for="Zona">' .Yii::t('app/crm', 'Zona') .'</label>';    
        $cad .=   '<div id="x3divDominio2" class="col-sm-8  ui-widget-content" style="border-bottom:0;">';
        $cad .=     '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divDominio2\', \'x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&op=' .$op .'\', \'selectdominiosampliados\', \'crm/operacion/encuesta\' );carga( \'x3divProyectoTop\', \'x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&codInstrumento=' .$cod_instrumento .'&op=' .$op .'\', \'top\', \'proyecto/' .$cod_proyecto .'/' .$cod_instrumento .'\' );" />';
   
        $cad .=   '</div>';
              
        return $cad;
    } // eof #######################################################




    /**
     * Selecciona el prospecto siguiente
     * Usado cuando no se usan cuotas
     */
    public static function contactarSinCuota( $cod_proyecto, $cod_instrumento, $id_instrumento, $op = 0, $estado = '2' ){

        $cad = self::selectSiguiente( $cod_proyecto, $id_instrumento, $estado, $op );
          
        return $cad;
    } // eof #######################################################
    



    // DEBO PARAMETRIZAR LA TIPIFICACION
    /**
     * Recetea un prospecto
     * debe usarse si el prospecto se uso en una prueba
     */
    public static function clearProspecto( $schema, $idProspecto ){

        // UPDATE CUOTA
        $sql = "select id_tipificacion, cp.id_cuota " .$schema .".llamada l inner join " .$schema .".cuota_prospecto cp on cp.id_prospecto=l.id_prospecto where l.id_prospecto='" .$idProspecto ."' order by l.id DESC limit 1;";
        $obj = Aux::findBySql($sql)->one();
        if( $obj->id_tipificacion == '1' ){
            $sql = "update " .$schema .".cuota set conteo=(conteo -1) where id='" .$obj->id_cuota ."';";
            Aux::findBySql($sql)->one(); 
        }
        
        // DELETE LLAMADA
        $sql = "delete from " .$schema .".llamada where id_prospecto='" .$idProspecto ."';";
        Aux::findBySql($sql)->one(); 

        // DELETE USUARIO_PROSPECTO
        $sql = "delete from " .$schema .".usuario_prospecto where id_prospecto='" .$idProspecto ."';";
        Aux::findBySql($sql)->one();

        // DELETE LLAMAR_LUEGO
        $sql = "delete from " .$schema .".llamar_luego where id_prospecto='" .$idProspecto ."';";
        Aux::findBySql($sql)->one();

        // DELETE ENCUESTA
        $sql = "delete from " .$schema .".encuesta where id_prospecto='" .$idProspecto ."';";
        Aux::findBySql($sql)->one();
        
        // UPDATE PROSPECTO
        $dql = "update " .$schema .".prospecto set barrida='1', c001='',  c002='', c003='', c004='', c005='', c006='', c007='', c008='', c009='', c010='', c011='', c012='', c013='', c014='', c015='', c016='', c017='', c018='', c019='', c020='', c021='', c022='', c023='', c024='', c025='', c026='', c027='', c028='', c029='', c030='', c031='', c032='', c033='', c034='', c035='', c036='', c037='', c038='', c039='', c040='', c041='', c042='', c043='', c044='', c045='', c046='', c047='', c048='', c049='', c050='', c051='', c052='', c053='', c054='', c055='', c056='', c057='', c058='', c059='', c060='', c061='', c062='', c063='', c064='', c065='', c066='', c067='', c068='', c069='', c070='', c071='', c072='', c073='', c074='', c075='', c076='', c077='', c078='', c079='', c080='', c081='', c082='', c083='', c084='', c085='', c086='', c087='', c088='', c089='', c090='', c091='', c092='', c093='', c094='', c095='', c096='', c097='', c098='', c099='', llamar=null, reg up=null st='2' tlo=null inicio=null fin=null id_tipificacion=null encuesta='0' where id='" .$idProspecto ."';";
        Aux::findBySql($sql)->one();
        
    } // eof #######################################################
    



    // DEBO PARAMETRIZAR LA TIPIFICACION
    /**
     * Recetea todos los prospectos teleoperados por admin
     * Los prospectos teleoperados por admin, se consideran pruebas
     */
    public static function clearAdmin(){
        
        // UPDATE CUOTA
        $sql = "select id_tipificacion, cp.id_cuota from " .$schema .".llamada l inner join " .$schema .".cuota_prospecto cp on cp.id_prospecto=l.id_prospecto where l.id_usuario='1' order by l.id DESC limit 1;";
        $obj = Aux::findBySql($sql)->one();
        if( $obj->id_tipificacion == '1' ){
            $sql = "update " .$schema .".cuota set conteo=(conteo -1) where id='" .$obj->id_cuota ."';";
            Aux::findBySql($sql)->one(); 
        }
        
        // DELETE LLAMADA
        $sql = "delete from " .$schema .".llamada where id_usuario='1';";
        Aux::findBySql($sql)->one(); 

        // DELETE USUARIO_PROSPECTO
        $sql = "delete from " .$schema .".usuario_prospecto where id_usuario='1';";
        Aux::findBySql($sql)->one();

        // DELETE LLAMAR_LUEGO
        $sql = "delete from " .$schema .".llamar_luego where id_usuario='1';";
        Aux::findBySql($sql)->one();
        
        // DELETE ENCUESTA
        $sql = "delete from " .$schema .".encuesta where id_usuario='1';";
        Aux::findBySql($sql)->one();
        
        // UPDATE PROSPECTO
        $dql = "update " .$schema .".prospecto set barrida='1', c008='', c009='', c010='', c011='', c012='', c013='', c014='', c015='', c016='', c017='', c018='', c019='', c020='', c021='', c022='', c023='', c024='', c025='', c026='', c027='', c028='', c029='', c030='', c031='', c032='', c033='', c034='', c035='', c036='', c037='', c038='', c039='', c040='', c041='', c042='', c043='', c044='', c045='', c046='', c047='', c048='', c049='', c050='', c051='', c052='', c053='', c054='', c055='', c056='', c057='', c058='', c059='', c060='', c061='', c062='', c063='', c064='', c065='', c066='', c067='', c068='', c069='', c070='', c071='', c072='', c073='', c074='', c075='', c076='', c077='', c078='', c079='', c080='', c081='', c082='', c083='', c084='', c085='', c086='', c087='', c088='', c089='', c090='', c091='', c092='', c093='', c094='', c095='', c096='', c097='', c098='', c099='', llamar=null, up=null, st='2', tlo=null, inicio=null, fin=null, id_tipificacion=null, encuesta='0' where tlo='1';";
        Aux::findBySql($sql)->one();
        
    } // eof #######################################################




    /**
     * Retorna la configuración del instrumento (encuesta) 
     */
    public static function configuracion( $codProyecto = null, $idInstrumento = null ){
        if( is_null($codProyecto) )
            $codProyecto = Request::rq('x3proy');
        if( is_null($codProyecto) )
            $codProyecto = Request::rq('cod_proyecto');
        
        if( is_null($idInstrumento) )
            $idInstrumento = (int)Request::rq('x3inst');
        if( $idInstrumento == 0 )
            $idInstrumento = (int)Request::rq('id_instrumento');
        
        $vct = null;

        
        $sql = "select ";
        $sql .= ' \'' .$codProyecto .'\' as cod_proyecto ';
        $sql .= ', i.id as id_instrumento ';
        $sql .= ', i.codigo as cod_instrumento ';
        $sql .= ', i.de as de_instrumento ';
        $sql .= ', i.entrada_inicial  ';

        $sql .= ', i.siguiente ';
        $sql .= ', i.cuotas ';
        $sql .= ', i.dominios ';
        $sql .= ', i.desplegar ';
        $sql .= ', i.back ';
        $sql .= ', i.id_jqueryui as theme ';
        $sql .= ', i.llamar_luego ';
       
        

        


        

        $sql .= ", (select c1.de from " .$codProyecto .".cabecera c1 where c1.id_instrumento=i.id order by id asc limit 1 offset 0 ) as cab_nombre ";
        $sql .= ", (select c2.de from " .$codProyecto .".cabecera c2 where c2.id_instrumento=i.id order by id asc limit 1 offset 1 ) as cab_telf "; 
        $sql .= ", (select c3.de from " .$codProyecto .".cabecera c3 where c3.id_instrumento=i.id order by id asc limit 1 offset 2 ) as cab_fecha_ref ";
        $sql .= ", (select c4.de from " .$codProyecto .".cabecera c4 where c4.id_instrumento=i.id order by id asc limit 1 offset 3 ) as cab_agente ";
        $sql .= ", (select c5.de from " .$codProyecto .".cabecera c5 where c5.id_instrumento=i.id order by id asc limit 1 offset 4 ) as cab_dominio ";



        $sql .= ", (select co1.columna from " .$codProyecto .".cabecera co1 where co1.id_instrumento=i.id order by id asc limit 1 offset 0 ) as col_nombre ";
        $sql .= ", (select co2.columna from " .$codProyecto .".cabecera co2 where co2.id_instrumento=i.id order by id asc limit 1 offset 1 ) as col_telf ";
        $sql .= ", (select co3.columna from " .$codProyecto .".cabecera co3 where co3.id_instrumento=i.id order by id asc limit 1 offset 2 ) as col_fecha_ref ";
        $sql .= ", (select co4.columna from " .$codProyecto .".cabecera co4 where co4.id_instrumento=i.id order by id asc limit 1 offset 3 ) as col_agente ";
        $sql .= ", (select co5.columna from " .$codProyecto .".cabecera co5 where co5.id_instrumento=i.id order by id asc limit 1 offset 4 ) as col_dominio ";

        
        $sql .= ", b.barrida, b.paso ";        
        
        $sql .= " from " .$codProyecto .".instrumento i ";
        

        
        $sql .= " inner join " .$codProyecto .".barrida b on b.id_instrumento = i.id ";
        
        $sql .= " left join a.jqueryui j on i.id_jqueryui = j.id ";
        $sql .= " where i.id='" .$idInstrumento ."' ";
        
        $vct = Aux::findBySql($sql)->one();
        return $vct;
    } // eof 
    
    
    
    
    /**
     * Crea un campo SELECT con información ampliada del dominio
     */
    public static function selectDominiosAmpliados(){
        $cad = '';
        $op = (int)Request::rq('op'); // 1: prueba, 0:teleoperacion
        $config = self::configuracion();
        
        if( $op == 0 ){
            $fecha = date('Y-m-d');
            
            $carga = "carga( 'x3divContactables', 'x3proy=" .$config->cod_proyecto ."&x3inst=" .$config->id_instrumento ."&op=" .$op ."&x3cuota=' +$('#id_cuota').val(), 'getcontactable', 'proyecto/" .$config->cod_proyecto ."/" .$config->cod_instrumento ."' );";
            

            $cad .= '<select id="id_cuota" name="id_cuota" onChange="' .$carga .'" style="background-color:#ffffff; border:none; width:100%;">';
            $cad .= '<option value=""> --- Seleccione --- </option>';
            
            if( (int)$config->id_instrumento > 0 ){
               
                
                $sql = "select d.id, d.de, d.cod, c.fecha_ref, c.cuota, c.conteo, c.id as id_cuota from " .$config->cod_proyecto .".dominio d";
                $sql .= " left join " .$config->cod_proyecto .".cuota c on c.id_dominio = d.id and c.st='2' and c.fecha_encuesta='" .$fecha ."' ";
                $sql .= " where d.st in (1) and d.id_instrumento='" .$config->id_instrumento ."' order by d.cod ASC;"; 
                $regs = Aux::findBySql($sql)->all();
                
                /* &&&&&&&&&&&&&&   DEBO parametrizar el uso de  and p.c001 = c.fecha_ref */
                
                foreach( $regs as $v ){
                    $sql2 = "select count(*) as count ";
                    $sql2 .= " from ";
                    $sql2 .= " " .$config->cod_proyecto .".dominio d";
                    $sql2 .= " inner join " .$config->cod_proyecto .".prospecto p on p." .$config->columna_cod ."=d.cod and d.cod='" .$v->cod ."' ";
                    $sql2 .= " inner join " .$config->cod_proyecto .".cuota c on c.id_dominio = d.id and c.st='2' ";
                    $sql2 .= " inner join " .$config->cod_proyecto .".cuota_prospecto cp on cp.id_prospecto = p.id and cp.id_cuota=c.id and p.id_data = cp.id_data ";
                    $sql2 .= " where p.st in (1,2,1201) and p.id_instrumento='" .$config->id_instrumento ."' and p." .$config->columna_fecha_ref ." = c.fecha_ref and c.fecha_encuesta='" .$fecha ."';";
                    $reg2 = Aux::findBySql($sql2)->one();
                    $ax = '';
                    $tx_cuota = $tx_client = '';
                    $pendientes = max(0,($v->cuota - $v->conteo));
                    if( (int)$v->cuota != 0 )
                        $tx_cuota = ' &nbsp; cuota:' .$pendientes;
                    if( (int)$reg2->count != 0 )
                        $tx_client = ' &nbsp; clientes:' .$reg2->count .'';
                    $ax = ' &nbsp; '.$v->fecha_ref .$tx_cuota .$tx_client;
                    if( $pendientes > 0 )
                        $cad .= '<option value="' .$v->id_cuota .'"> &nbsp; ' .$v->de .'   ' .$ax .' </option>';
                } //for 
            }
            
            $cad .= '</select>';
        }else{
            $carga = "carga( 'x3divContactables', 'x3proy=" .$config->cod_proyecto ."&x3inst=" .$config->id_instrumento ."&x3cuota=0&op=" .$op ."', 'getcontactable', 'proyecto/" .$config->cod_proyecto ."/" .$config->cod_instrumento ."' );";
            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" onLoad="' .$carga .'" />';
        }
        
        return $cad;
    } // eof #######################################################
    



    /**
     * Retorna el prospecto siguiente
     * 1: Aleatorio
     * 2: por Id
     */
    public static function siguienteProspecto( $sqlBase, $tipo = 0, $op = 0, $conCuota = 0 ){
        $aux = '';
        $reg = null;
        if( $conCuota == 1 ){
            $aux = ", c.id as id_cuota, c.cuota, c.conteo ";
        }
        if( $op == 0 ){ // teleoperacion
            if( $tipo == 2 ){ // Seleccion por ID
                $sql = "select p.*, b.barrida as veces " .$aux;
                $sql .= $sqlBase ." order by id asc limit 1;";
                $reg = Aux::findBySql($sql)->one();
            }else{ // SELECCION ALEATORIA
                $sql = "select p.*, b.barrida as veces " .$aux;
                $sql .= $sqlBase ." order by random() limit 1;";
               
                $reg = Aux::findBySql($sql)->one();
            }
        }else{ // simulacion para ve el instrumento
            if( $tipo == 2 ){ // Seleccion por ID
                $sql = "select p.*, b.barrida as veces ";
                $sql .= $sqlBase ." order by id asc limit 1;";
                $reg = Aux::findBySql($sql)->one();
            }else{ // SELECCION ALEATORIA
                $sql = "select p.*, b.barrida as veces ";
                $sql .= $sqlBase ." order by random() limit 1;";
              
                $reg = Aux::findBySql($sql)->one();
            }
        }
        return $reg;
    } // eof 

    

    
    /**
     * Despliega un contactable y sus acciones
     */
    public static function selectSiguiente( $schema = null, $idInstrumento = null, $estado = 2, $op = 0, $codInstrumento = null, $fecha = null, $vct = null, $conCuota = 0 ){
       
        $cad = '';
        $cod_proyecto = $schema;
        $id_instrumento = $idInstrumento;
        $config = self::configuracion( $cod_proyecto, $id_instrumento );
        if( $id_instrumento != 0 ){
            if( false && is_array($vct) ){
                $cAux = "c.de ='" .implode("' or c.de = '", $vct) ."' ";
                // seleccionar las columnas de los datos claves
                $sql = "select de,columna from " .$cod_proyecto .".cabecera c where st='1' and c.id_instrumento='" .$id_instrumento ."' and (" .$cAux .") order by de ASC;";
                $cabs = Aux::findBySql($sql)->all();
                // PARAMETRIZAR
                foreach( $cabs as $cab ){
                    $abc = strtolower($cab->de);
                    switch( $abc ){
                    case 'id zona': $columna_cod = $cab->columna; break;
                    case 'movil': $columna_movil = $cab->columna; break;
                    case 'fecha de atención': $columna_fecha_ref = $cab->columna; break;
                    case 'nombre': $columna_nombre = $cab->columna; break;
                    }
                }
            }
            
            
            $columna_cod = $config->col_dominio;
            $columna_movil = $config->col_telf;
            $columna_fecha_ref = $config->col_fecha_ref;
            $columna_nombre = $config->col_nombre;

            // para que tome encuenta todas las tipificaciones efectivas
            $ids_efectivas = '';
            $regT = Aux::findBySql("select valor from " .$cod_proyecto .".tipificacion where id_estatus='1' and st='1' and id_instrumento='" .$id_instrumento ."'")->all();
            foreach( $regT as $regT2 ) $ids_efectivas .= $regT2->valor .',';
            $ids_efectivas = substr($ids_efectivas, 0, -1);
            if( $ids_efectivas == '' ) $ids_efectivas = 1;
            
            // quite el estado de la conssulta "a"
          
            if( $op == 0 ){
                $a = " from " .$cod_proyecto .".prospecto p";
                $a .= " inner join " .$cod_proyecto .".barrida b on b.id_instrumento = p.id_instrumento and p.barrida <= (b.barrida +" .($config->paso -1) .") ";
                $a .= " inner join " .$cod_proyecto .".data d on d.id = p.id_data ";
                $a .= " where  p.id_instrumento = '" .$id_instrumento ."' ";
                $a .= " and d.st in ('1') ";
                
                $sql = "select sum(cargados) as cargados, sum(efectivas) as efectivas, sum(no_disponibles) as no_disponibles from (

select count(*) as cargados, 0 as efectivas, 0 as no_disponibles " .$a ."

union select 0 as cargados, 0 as efectivas, count(*) as no_disponibles 
from " .$cod_proyecto .".prospecto p 
inner join " .$cod_proyecto .".barrida b on b.id_instrumento = p.id_instrumento and p.barrida <= (b.barrida +2) 
inner join " .$cod_proyecto .".data d on d.id = p.id_data 
where p.st in ('3','4','5') and p.id_instrumento = '" .$id_instrumento ."' 
and d.st in ('1')

union select 0 as cargados, count(*) as efectivas, 0 as no_disponibles 
from " .$cod_proyecto .".prospecto p 
inner join " .$cod_proyecto .".data d on d.id = p.id_data 
where p.st in ('3','4','5') and p.id_instrumento = '" .$id_instrumento ."' 
and d.st in ('1')
and p.id_tipificacion in ($ids_efectivas)

) x ;";
           
            }else{
                
                $sql = "select 0 as cargados, 0 as efectivas, 0 as no_disponibles ;"; 
            }
            

            $reg0 = Aux::findBySql($sql)->one();
            
            
            $contactables = (int)$reg0->cargados;
            if( $contactables > 0 ){
                $contactables = (int)$reg0->cargados;
                
                // Prospecto Siguiente aleatorio o por ID
                $reg = self::siguienteProspecto( $a, (int)$config->siguiente, $op, $conCuota );
              
                self::getBlockearProspecto( $cod_proyecto, $id_instrumento, $reg->id );
                
                $efectivas = $reg0->efectivas; 
                $barrida = (int)$reg->veces;
                $cuotaPendiente = $reg0->cargados - $reg0->no_disponibles;
                
                $cad .= '<div class="" style="width: 500px;">';
                
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4 ui-state-focus" style="margin:0;  padding:0; text-align:left;" for="Contactables">  &nbsp; '. Yii::t('app/crm', 'Cargados') .'</label>';
                $cad .=   '<div class="col-sm-8   ui-corner-bottom ui-helper-reset ui-widget-content" style="margin:0;  padding:0;">' .$contactables .'</div>';
                $cad .= '</div>';
                
                
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4 ui-state-default " style="margin:0;  padding:0; text-align:left;" for="Efectivas"> &nbsp; '. Yii::t('app/crm', 'Efectivas') .'</label>';
                $cad .=   '<div class="col-sm-8   ui-corner-bottom ui-helper-reset ui-widget-content" style="margin:0;  padding:0;">' .$efectivas .'</div>';
                $cad .= '</div>';
                
                
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4 ui-state-focus" style="margin:0;  padding:0; text-align:left;" for="Barrida"> &nbsp; '. Yii::t('app/crm', 'Barrida') .'</label>';
                $cad .=   '<div class="col-sm-8   ui-corner-bottom ui-helper-reset ui-widget-content">' .$barrida .'</div>';
                $cad .= '</div>';
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4 ui-state-default" style="margin:0;  padding:0; text-align:left;" for="Zona"> &nbsp; '. Yii::t('app/crm', 'Contactables') .'</label>';
                $cad .=   '<div class="col-sm-8  ui-corner-bottom  ui-widget-content">' .$cuotaPendiente .'</div>';
                $cad .= '</div>';
                $cad .= '</div>';
                
                   
                $idCuota = (int)$reg->id_cuota;
                $d1 = "'x3inst=" .$id_instrumento ."&x3cuota=" .$idCuota ."&x3prosp=" .$reg->id ."', 'contacto2'";
                $d2 = "'x3DivEncuesta', " .$d1 .", '" .$cod_proyecto ."/" .$config->codigo ."'";       
              
                
                $cad .= self::mostrar( $cod_proyecto, $id_instrumento, $config->cod_instrumento, $reg, $idCuota, $op );


                
            }else if( $op > 0 ){
                // solo ver las preguntas del instrumento
                $cad .= self::mostrar( $cod_proyecto, $id_instrumento, $config->cod_instrumento, null, 0, $op );
            }else{
                $cad .= '<hr />';
                $cad .= '<div class="row alert alert-info"> No existen Prospectos contactables </div>';
            }
        }

        return $cad;
    } // eof #######################################################
    
    



    
    
    /**
     * Muestra las cabeceras que se permiten mostrar
     * Orden preestablecido:
     * 1: nombre
     * 2: movil
     * 3: fecha ref
     * 4: agente
     * 5: dominio
     * 6...: todos los demas
     * @param string $cod_proyecto, Código del proyecto
     * @param integer $id_instrumento, ID del isntrumento
     * @param string $cod_instrumento, Código del instrumento
     * @param Objeto $registro, Objeto Prospecto
     * @param integer $id_cuota, ID de la cuota
     * @param integer $op, 1: Es una simulación
     * @return string
     */
    public static function mostrar( $cod_proyecto, $id_instrumento, $cod_instrumento, $registro, $id_cuota = null, $op = 0 ){
        
        $cad = '';

        $d1 = "'x3inst=" .$id_instrumento ."&x3cuota=0&x3prosp=0', 'contacto2'";
        $d2 = "'x3DivEncuesta', " .$d1 .", '" .$cod_proyecto ."/" .$cod_instrumento ."'";
        
        
        $cad .= '<br />'; 
        $cad .= '<div id="x3DivProspecto" class="row" style="text-align: left;">';
        
        $cad .= "<script>\n function x3cambiaAccion(){ $('#x3divLlamar').hide(); $('#x3divCancelar').show(); }; \n </script>\n";

        if( $op == 0 )
            $registro_id = $registro->id;
        else
            $registro_id = 0;
        
        
        

        

        if( $op == 0 ){
            
            $cad .= '<center><table width="100%"><tbody>';

            $cad .= self::mostrarCabeceras( $cod_proyecto, $id_instrumento, $registro );
        
            
            $cad .= '<tr><td colspan="4">';
            $cad .= '<div style="position:relative; top:10px; width: 100%; text-align:center;">';
            $cad .= '<div style="float:left;width: 35%;"> &nbsp; </div>';
            $cad .= '<div id="x3accion" style="float:left; width: 320px;text-align:center;">';
            
            if( false ){
                $cad .= '<div style="float:left;">';
                $cad .= '<button type="button" class="btn btn-default" style="width:150px;" onClick="" > Salvar Cambios </button>';
                $cad .= '</div>';
            }
            
            $cad .= '<div id="x3divLlamar" style="display:block; height: 3.8em;">';
            $cad .= '<button type="button" class="btn btn-primary" style="width:150px;" onClick="';
            $cad .= "carga( 'x3DivEncuesta', 'x3proy=" .$cod_proyecto ."&x3cuota=" .$id_cuota ."&x3inst=" .$id_instrumento ."&op=" .$op ."&x3prosp=" .$registro_id ."', 'contacto2', 'proyecto/" .$cod_proyecto ."/" .$cod_instrumento ."' ); x3cambiaAccion();";
            $cad .= '" > Realizar LLamada </button> &nbsp; ';
            $cad .= '</div>';
            
            $cad .= '<div id="x3divCancelar" style="display:none; height: 3.8em;"><center>';
            $cad .= '<button type="button" class="btn btn-danger" style="width:150px;" onClick="';
            $cad .= "carga( 'x3cnt', 'x3proy=" .$cod_proyecto ."&x3inst=" .$id_instrumento ."&x3prosp=" .$registro_id ."', 'esperar', 'crm/operacion/encuesta' );";
            $cad .= '" > Cancelar LLamada </button>';
            $cad .= '</center></div>';
            
            $cad .= '</div>';
            $cad .= '</div>';
            $cad .= '</td></tr>';
            
            $cad .= '</tbody></table></center>';
            $cad .= '</div>';        
        }else{
            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" onLoad="carga( \'x3DivEncuesta\', \'x3proy=' .$cod_proyecto .'&x3cuota=0&x3inst=' .$id_instrumento .'&op=1&x3prosp=0\', \'contacto2\', \'proyecto/' .$cod_proyecto .'/' .$cod_instrumento .'\' );" />';  
        }
        
        return $cad;
    } // eof 





    


    /**
     * Muestra las cabeceras que estan marcadas para ser mostradas
     */
    public static function mostrarCabeceras( $cod_proyecto, $id_instrumento, $prospecto ){
        $cad = '';
        $i = 0;
        $j = 0;
        $uno[$j] = '';
        $dos[$j] = '';
        $vct = array(3,6,9,12,15,18,21,24,27,30,33,36,39,42,45);
        
        $op = 0;
        
        $sql = "select de, columna, editar from " .$cod_proyecto .".cabecera c where c.st='1' and mostrar='1' and c.id_instrumento='" .$id_instrumento ."' order by id asc;";
        $cabs = Aux::findBySql($sql)->all();
        foreach( $cabs as $cab ){
            if( in_array($i,$vct) ){
                $j++;
                $uno[$j] = $dos[$j] = '';
            } 
            $uno[$j] .= '<td>' .$cab->de .'</td>';
            $aux = $cab->columna;
            
            if( $op == 0 && isset($prospecto->$aux) )
                $registro_aux = $prospecto->$aux;
            else
                $registro_aux = '';
            
            
            if( $cab->editar ){
                $dos[$j] .= '<td title="' .$registro_aux .'"><input type="" id="" name="" value="' .$registro_aux .'" style="border:solid 1px #eeeeee;"/></td>';
            }else{
                if( $i == 2 )
                    $dos[$j] .= '<td title="' .$registro_aux .'"> &nbsp; ' .Formato::date( $registro_aux, 'd,m Y') .'</td>';
                else if( $i == 1 )
                    $dos[$j] .= '<td title="' .$registro_aux .'"> &nbsp; ' .Formato::telf( $registro_aux, 'd,m Y') .'</td>';
                else
                    $dos[$j] .= '<td title="' .$registro_aux .'"> &nbsp; ' .$registro_aux .'</td>';
            }
            $i++;
        }

        for( $k=0 ; $k <= $j ; $k++ ){
            $cad .= '<tr class="ui-corner-top ui-state-default ui-state-active ui-state-focus" style="text-align: center; line-height: 2.0em; font-weight: 700;">';
            $cad .= $uno[$k];
            $cad .= '</tr>';
            $cad .= '<tr class="ui-corner-bottom ui-helper-reset ui-widget-content" style="font-size:1.5em; text-align: center;">';
            $cad .= $dos[$k];
            $cad .= '</tr>';
        }
        
        return $cad;
    } // eof 
    
    
    

    
    
    
    /**
     * lista de dominios, para select, NO usado en contacto1
     */
    public static function selectDominios_depreacte( $schema, $idInstrumento ){
        $cad = '<select id="id_dominio" name="id_dominio" onchange="alert(\'adr\');"  style="background-color:#ffffff; border:none; width:100%;">';
        $cad .= '<option value=""> --- Seleccione --- </option>';
        if( $idInstrumento != null ){
            $sql = "select id,de,cod from " .$schema .".dominio where st in (1,2,5) and id_instrumento='" .$idInstrumento ."' order by de ASC;";
            $regs = Aux::findBySql($sql)->all();
            foreach( $regs as $v )
                $cad .= '<option value="' .$v->id .'">' .$v->cod .' - ' .$v->de .'</option>';
        }
        return $cad .'</select>';
    } // eof #######################################################
    


    


    /**
     * Despliega un contactable y sus acciones
     */
    public static function getContactable( $idCuota, $fecha, $vct ){
       
        $cad = '';
        $id_cuota = (int)Request::rq('x3cuota');
        $op = (int)Request::rq('op');
       
        $config = self::configuracion();
    
        if( $config->id_instrumento != 0 && $idCuota != 0 && $op == 0 ){

            $columna_cod = $config->col_dominio;
            $columna_movil = $config->col_telf;
            $columna_fecha_ref = $config->col_fecha_ref;
            $columna_nombre = $config->col_nombre;
                        
            $sql = "select count(*) as count ";
            $a = " from ";
            $a .= " " .$config->cod_proyecto .".prospecto p";
            $a .= " inner join " .$config->cod_proyecto .".cuota_prospecto cp on cp.id_prospecto = p.id ";
            $a .= " inner join " .$config->cod_proyecto .".cuota c on c.id = cp.id_cuota and c.id_data = cp.id_data ";
            $a .= " inner join " .$config->cod_proyecto .".barrida b on b.id_instrumento=p.id_instrumento and p.barrida <= (b.barrida +2) ";
            $a .= " where p.st in (1,2,1201) and p.id_instrumento='" .$config->id_instrumento ."' and c.st in (1,2)  and p.c001 = c.fecha_ref and c.fecha_encuesta='" .$fecha ."' and c.id = '" .$idCuota ."'";
            $sql .= $a .';';
            $reg0 = Aux::findBySql($sql)->one();
            
            $contactables = (int)$reg0->count;
            if( $contactables > 0 ){
                $contactables = (int)$reg0->count;

                $reg = self::siguienteProspecto( $a, $config->siguiente, $op, '1' );
                
                self::getBlockearProspecto( $config->cod_proyecto, $config->id_instrumento, $reg->id );
                
                
                $efectivas = (int)$reg->conteo;
                $barrida = (int)$reg->veces;
                $cuota = (int)$reg->cuota;
                $pendiente = $cuota - $efectivas;
                $cuotaPendiente = ($pendiente > 0)?$pendiente:0;
                
                $cad .= '<div class="" style="width: 500px;">';
                    
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4 ui-state-focus" style="margin:0; padding:0; text-align:left;" > &nbsp; '. Yii::t('app/crm', 'Contactables') .'</label>';
                $cad .=   '<div class="col-sm-8   ui-corner-bottom ui-helper-reset ui-widget-content" style="margin:0;  padding:0;">' .$contactables .'</div>';
                $cad .= '</div>';
                
                
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4  ui-corner-top ui-state-default " style="margin:0;  padding:0; text-align:left;" for="Efectivas"> &nbsp; '. Yii::t('app/crm', 'Efectivas') .'</label>';
                $cad .=   '<div class="col-sm-8   ui-corner-bottom ui-helper-reset ui-widget-content" style="margin:0;  padding:0;">' .$efectivas .'</div>';
                $cad .= '</div>';
                
                
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4 ui-corner-top ui-state-focus" style="margin:0;  padding:0; text-align:left;" for="Barrida"> &nbsp; '. Yii::t('app/crm', 'Barrida') .'</label>';
                $cad .=   '<div class="col-sm-8   ui-corner-bottom ui-helper-reset ui-widget-content">' .$barrida .'</div>';
                $cad .= '</div>';
                $cad .= '<div class="row">';
                $cad .=   '<label class="control-label col-sm-4  ui-corner-top ui-state-default" style="margin:0;  padding:0; text-align:left;" for="Zona"> &nbsp; '. Yii::t('app/crm', 'Cuota por cumplir') .'</label>';
                $cad .=   '<div class="col-sm-8  ui-corner-bottom  ui-widget-content">' .$cuotaPendiente .'</div>';
                $cad .= '</div>';
                $cad .= '</div>';
                if( $efectivas >= $cuota ){
                    $cad .= '<hr />';
                    $cad .= '<div class="row alert alert-info"> Cuota cumplida! </div>';
                }else{
                    $cad .= self::getSinContactable( $config->cod_proyecto, $config->id_instrumento, $config->cod_instrumento, $op, $reg, $columna_nombre, $columna_fecha_ref, $columna_movil );
                }       
            }else{
                $cad .= '<hr />';
                $cad .= '<div class="row alert alert-info"> No existen Prospectos contactables </div>';
            }
        }
        return $cad;
    } // eof #######################################################
    
    

    /**
     * Despliega datos del contactable
     */
    public static function getSinContactable( $cod_proyecto, $id_instrumento, $cod_instrumento, $op = 0, $registro = null, $columna_nombre = null, $columna_fecha_ref = null, $columna_movil = null ){
         
        $cAux2 = $columna_nombre;
        $cAux3 = $columna_fecha_ref;
        $cAux4 = $columna_movil;
        $id_prospecto = $registro->id;
        $nombre = $registro->$cAux2;
        $fecha = $registro->$cAux3;
        $telefono = $registro->$cAux4;
        $id_cuota = $registro->id_cuota;
        
       
        $cad = '<br />'; 
        $cad .= '<div id="x3DivProspecto" class="row" style="text-align: left;">';
        
        $d1 = "'x3inst=" .$id_instrumento ."&x3cuota=" .$id_cuota ."&x3prosp=" .$id_prospecto ."', 'contacto2'";
        $d2 = "'x3DivEncuesta', " .$d1 .", '" .$cod_proyecto ."/" .$cod_instrumento ."'";       
        $cad .= "<script>\n function x3cambiaAccion(){ $('#x3divLlamar').hide(); $('#x3divCancelar').show(); }; \n </script>\n";
        $cad .= '<center><table width="100%"><tbody>
<tr class="ui-corner-top ui-state-default ui-state-active ui-state-focus"  style="text-align: center; line-height: 2.0em; font-weight: 700;"><td>Nombre</td><td>Fecha de Atención</td><td>Teléfono</td><td>&nbsp;Acción</td></tr>';

        $cad .= '<tr class="ui-corner-bottom ui-helper-reset ui-widget-content" style="font-size:1.5em; text-align: center;"><td>' .$nombre .'</td><td title="' .$fecha .'">' .Formato::date( $fecha, 'd,m Y') .'</td><td title="' .$telefono .'">' .Formato::telf($telefono) .'</td><td><div id="x3accion">';          
        $cad .= '<div id="x3divLlamar" onClick="';
        $cad .= "carga( 'x3DivEncuesta', 'x3proy=" .$cod_proyecto ."&x3cuota=" .$id_cuota ."&x3inst=" .$id_instrumento ."&x3prosp=" .$id_prospecto ."', 'contacto2', 'proyecto/" .$cod_proyecto ."/" .$cod_instrumento ."' ); x3cambiaAccion();";
        
        $cad .= '" style="color:#0000aa;font-weight:bold; cursor:hand; cursor:pointer; display:block;"> LLamar </div>';         
        $cad .= '<div id="x3divCancelar" onClick="';
        
        $cad .= "carga( 'x3cnt', 'x3proy=" .$cod_proyecto ."&x3inst=" .$id_instrumento ."&x3prosp=" .$id_prospecto ."', 'esperar', 'crm/operacion/encuesta' );";
        $cad .= '" style="color:#0000aa;font-weight:bold; cursor:hand; cursor:pointer; display:none;"> Cancelar </div>';
        $cad .= '</div>';
        $cad .= '</td></tr>
</tbody></table></center>';
        $cad .= '</div>';
        return $cad;
    } // eof 
    



    /**
     * Despliega la encuesta
     */
    public static function despliega( $idProspecto = 0, $idCuota = 0, $javascript = '' ){
        $idUsuario = Usuario::id();
        $config = self::configuracion();
        $op = (int)Request::rq('op');

        if( $op == 0 ){
            $sql = "select * from " .$config->cod_proyecto .".prospecto where id='" .(int)$idProspecto ."' and st in (4,1,2,1201) limit 1;";
            $prospecto = Aux::findBySql($sql)->one(); 
        }else $prospecto = null;
        
        $sql = "select e.*, 1 as num from " .$config->cod_proyecto .".entrada e inner join " .$config->cod_proyecto .".instrumento i on i.id=e.id_instrumento and i.entrada_inicial = e.codigo where e.id_instrumento='" .$config->id_instrumento ."' and e.st in (1,2,5) 
union select e.*, 2 as num from " .$config->cod_proyecto .".entrada e where e.id_instrumento='" .$config->id_instrumento ."' and e.st in (1,2,5) order by num asc, orden asc, id asc;";
        $entradas = Aux::findBySql($sql)->all();
        
        
        $x = '';
        $x .= "\n <script> \n
x3entAct = '" .$entradas[0]->id ."'; \n
x3entNext = '" .$entradas[0]->ir_a ."'; \n
x3respuestaAnt = '" .$config->entrada_inicial ."'; \n
function x3" .$config->cod_instrumento ."IrA( act, next, tp, valorOpcion ){" .$javascript ."}; \n
</script> \n";
        $x .= '<form id="form' .$config->id_instrumento .'" name="form' .$config->id_instrumento .'" action="' .Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$config->cod_proyecto .'/' .$config->cod_instrumento .'/salvar&op=' .$op .'" method="POST">' ."\n";
        $x .= '<input type="hidden" id="cod_proyecto" name="cod_proyecto" value="' .$config->cod_proyecto .'" />' ."\n";
        $x .= '<input type="hidden" id="id_instrumento" name="id_instrumento" value="' .$config->id_instrumento .'" />' ."\n";
        $x .= '<input type="hidden" id="id_prospecto" name="id_prospecto" value="' .$idProspecto .'" />' ."\n";
        $x .= '<input type="hidden" id="id_tlo" name="id_tlo" value="' .$idUsuario .'" />' ."\n";
        $x .= '<input type="hidden" id="id_cuota" name="id_cuota" value="' .$idCuota .'" />' ."\n";
        
        $x .= '<br /><div class="subtitulos"> Cuestionario Estudio Transaccional </div>';
        $x .= '<div class="titulos">' .strtoupper($config->de_instrumento) .'</div>'."\n";
        
        $x .= '<div class="alert alert-warning">Esta <strong>Llamada!</strong> esta siendo grabada.</div>';
  
        $x .= self::entrada( $config->cod_proyecto, $config->cod_instrumento, $config->id_instrumento, $prospecto, $entradas[0], 'block', $op );
        
        $cant = count($entradas);
        self::$codigo[0] = 0;
        for( $i=0; $i < $cant ; $i++ ){
            self::$codigo[$entradas[$i]->codigo] = $entradas[$i]->id;
            if( isset($entradas[$i]->ir_a) )
                self::$orden[$entradas[$i]->codigo] = $entradas[$i]->ir_a;
        }
        for( $i=1; $i < $cant ; $i++ ){
            $sig = self::ir_a( $entradas[$i], $i < $cant );
            $x .= self::entrada( $config->cod_proyecto, $config->cod_instrumento, $config->id_instrumento, $prospecto, $entradas[$i], 'none', $op );
        }        
        return $x;
    } // eof ##################################################
    


    
    /**
     * Despliega las entradas del instrumento
     */
    public static function entrada( $schema, $codInstrumento, $idInstrumento, $prospecto, $entrada, $display = 'none', $op = 0 ){
        
        $time = time();
        $hora = date('H', $time);
        $config = self::configuracion( $schema, $idInstrumento );


        if( $hora >= 5 && $hora < 12 ) $__SALUDO__ = 'Buenos Días';
        else if( $hora >= 12 )         $__SALUDO__ = 'Buenas Tardes';
        else                           $__SALUDO__ = 'Buenas Noches';

        $id_usuario = Usuario::id();
        $tloSql = "select nombres, apellidos from a.usuario where id='" .$id_usuario ."' limit 1;";
        $tloObj = Aux::findBySql($tloSql)->one();
       
      
        /* debo cambiar esto y parametrizar */
       
        $eObj = Aux::findBySql("select valor from a.config where de='empresa' limit 1;")->one();
        $__EMPRESA__ = $eObj->valor;
        $__OPERADOR__ = $tloObj->nombres .' ' .$tloObj->apellidos;
        if( is_null($prospecto) ){
            $__AGENTE__ = 'Agente';
            $__FECHA__ = Formato::date('2017-01-01');
            $__CLIENTE__ = 'Fulanito Rodriguez';
            $__TELEFONO__ = '0123 1234567';
        }else{
            $__AGENTE__ = '';
            $__FECHA__ = '';
            $__CLIENTE__ = '';
            $__TELEFONO__ = '';
            
            if( isset($config->col_agente) ){
                $xu = $config->col_agente;
                $__AGENTE__ = $prospecto->$xu;
            }
            if( isset($config->col_fecha_ref) ){
                $xu = $config->col_fecha_ref;    
                $__FECHA__ = Formato::date($prospecto->$xu);
            }
                       
           

        }


        
        $entrada->de = str_replace('__SALUDO__', $__SALUDO__, $entrada->de);
        $entrada->de = str_replace('__CLIENTE__', '<u>' .$__CLIENTE__.'</u>', $entrada->de);
        $entrada->de = str_replace('__OPERADOR__', '<u>' .$__OPERADOR__.'</u>', $entrada->de);
        $entrada->de = str_replace('__AGENTE__', $__AGENTE__, $entrada->de);
        $entrada->de = str_replace('__FECHA__', $__FECHA__, $entrada->de);
        $entrada->de = str_replace('__EMPRESA__', $__EMPRESA__, $entrada->de);
        $entrada->de = str_replace('__TELEFONO__', '<b>'.Formato::telf($__TELEFONO__).'</b>', $entrada->de);        
        $sig = $entrada->ir_a;
        
        $x = "\n <script> \n x3vctNextEnt['" .trim($entrada->codigo) ."'] = '" .trim($entrada->ir_a) ."'; \n x3vctEntTp['" .trim($entrada->codigo) ."'] = '" .trim($entrada->id_pregunta_tp) ."'; \n </script> \n";
        if( $display == '' || $display == NULL ) $display = 'none'; 
 
        if( $entrada->id_pregunta_tp == 1 ){
          
            if( strtolower($entrada->codigo) == 'x3fin' ){
                // ######################    Es el final de la encuesta
                $x .= '<div id="x3entrada_' .trim($entrada->codigo) .'" style="display:' .$display .'; ">';
                $x .= '<div><br /><br /><a id="a_' .trim($entrada->codigo) .'" name="a_' .trim($entrada->codigo) .'">&nbsp;</a>' .$entrada->de .'<br /><br /><br /></div>';
               
                if( $op == 0 ) // salvar encuesta
                    $final = '$(\'#form' .$idInstrumento .'\').submit();';
                else // ver instrumento, sin cargar prospecto
                    $final = 'location.href=\'' .Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/ver&op=1&x3proy=' .$schema .'&x3inst=' .$idInstrumento .'\';';
                
                $x .= '<div id="btn_' .trim($entrada->codigo) .'0" class="btn btn-default" onClick="' .$final .'" style="width:150px; "> Finalizar </div>' ."\n" .'<br /><br /><br /></div>';
                $x .= '</form>';
                
            }else{
                // ######################    No es una pregunta, es solo texto
                $x .= '<div id="x3entrada_' .trim($entrada->codigo) .'" style="display:' .$display .'; ">' ;
                $x .= '<br /><a id="a_' .trim($entrada->codigo) .'" name="a_' .trim($entrada->codigo) .'">&nbsp;</a>';
                $x .= $entrada->de .'</div>';
            }
        }else{
            $x .= '<div class="form-group" id="x3entrada_' .trim($entrada->codigo) .'" style="display:' .$display .';">';
            $x .= '<br /><a id="a_' .$entrada->codigo .'" name="a_' .$entrada->codigo .'">&nbsp;</a>';
            $x .= '<label for="p' .$entrada->id .'"><span style="color:#ccc;">' .strtoupper($entrada->codigo) .'</span>. ' .ucfirst($entrada->de) .'</label>';
            if( $entrada->id_pregunta_tp == 2 ){
                // ######################    campo de texto
                $ir3 = "x3IrA('" .$codInstrumento ."','" .$entrada->codigo ."', '" .$sig ."', '" .$entrada->id_pregunta_tp ."');";
                $x .= '<input class="form-control" type="text" id="x3campo_' .$entrada->codigo .'" name="x3campo_' .$entrada->codigo .'" onChange="' .$ir3 .'" /><br />';
                // x3Seleccionar(\'' .$entrada->codigo .'\', \'2\');x3entNext=\'' .$sig .'\';
            }else if( $entrada->id_pregunta_tp == 3 || $entrada->id_pregunta_tp == 4 || $entrada->id_pregunta_tp == 5 ){
                // ######################    campo de selección - Radio
               
                $sql = "select * from " .$schema .".entrada_op where id_entrada='" .$entrada->id ."' and st in (1,2,5) order by id ASC;";
              
                $options = Aux::findBySql($sql)->all();
                $x .= '<table border="0" style="width:100%"><tbody>';
                $ax1 = $ax2 = $esTabla = '';
                
                foreach( $options as $v ){
                    $eval = '';
                    if( !is_null($v->js) && $v->js != '' )
                        $eval = $v->js .';';
                    if( substr($v->valor,0,1) == '_' ){
                        // ######################    No es una opcion, Describe una seccion de opciones
                        $x .= '<tr><td style="width:4.0em;" > &nbsp; </td><td colspan="2" style="background-color:#eef;"> &nbsp; &nbsp; ' .strtoupper($v->de) .'</td></tr>'."\n";
                    }else if( $v->tp == 1 ){
                        // ######################    Corresponde a 1,2,3,4,5,6,7,8,9,10,ns/nc
                        if( $v->ir_a != null && $v->ir_a != '' )
                            $sig = trim($v->ir_a);
                        else
                            $sig = trim($entrada->ir_a);
                        $esTabla = 1;
                        $ir3 = "$('#x3campo_" .trim($v->id) ."').prop( 'checked', true ); x3IrA('" .$codInstrumento ."','" .trim($entrada->codigo) ."', '" .$sig ."', '" .trim($entrada->id_pregunta_tp) ."', '" .trim($v->id) ."');";
                        
                        $ax1 .= '<td style="text-align:center; width: 9%; padding: 0.3em;" > <input onClick="' .$eval .$ir3 .'" type="radio" id="x3campo_' .trim($v->id) .'" name="x3campo_' .trim($entrada->codigo) .'" value="' .$v->valor .'" /> </td>';

                        $ax2 .= '<td onClick="' .$eval .$ir3 .'" style="text-align:center; width: 9%; cursor:hand;cursor:pointer;" > ' .$v->de .' </td>'."\n";
                    }else{
                        if( $v->ir_a != null && $v->ir_a != '' )
                            $sig = trim($v->ir_a);
                        else
                            $sig = trim($entrada->ir_a);
                        
                        $ir3 = "$('#x3campo_" .trim($v->id) ."').prop( 'checked', true ); x3IrA('" .$codInstrumento ."','" .trim($entrada->codigo) ."', '" .$sig ."', '" .trim($entrada->id_pregunta_tp) ."', '" .trim($v->id) ."');";
                         
                        $x .= '<tr>'
                            .'<td style="width:4.0em;" > &nbsp; </td>'
                            .'<td style="position:relative; width:1.0em; top: 0.5em;" valign:middle;> <input onClick="' .$eval .$ir3 .'" type="radio" id="x3campo_' .trim($v->id) .'" name="x3campo_' .trim($entrada->codigo) .'" value="' .$v->valor .'" /> &nbsp;</td>'
                            .'<td onClick="' .$eval .$ir3 .'" style="padding-left: 0.5em; cursor:hand;cursor:pointer;"> ' .$v->de .'</td>'
                            .'</tr>'."\n";
                    }
                    unset($v);
                } // for 
                if( $esTabla == 1 ) $x .= '<tr>' .$ax1 .'</tr><tr>' .$ax2 .'</tr>';
                $x .= '</tbody></table>'."\n";
            }
            $x .= '</div>'."\n";
            


            // ###################    Cuando sea campo de texto => imprimir boton de 'Siguiente'
             if( trim($entrada->id_pregunta_tp) == '2' )
                $x .= '<div id="btn_' .trim($entrada->codigo) .'" class="btn btn-default" onClick="x3IrA(\'' .$codInstrumento .'\',\'' .$entrada->codigo .'\', \'' .$sig .'\', \'' .$entrada->id_pregunta_tp .'\');" style="width:150px; display:' .$display .';"> Siguiente </div>' ."\n";
            
        }
        return $x;
    } // eof ##################################################
        

    
    
    /**
     * Retorna la entrada hacia donde se debe redirigir
     */
    public static function ir_a( $entrada, $cant ){
        if( $entrada->ir_a != null && $entrada->ir_a != '' )
            return self::$codigo[$entrada->ir_a];
        else if( $cant ){
            return $entrada->id +1;
        }else
            return 0;
    } // eof ##################################################
    



    /**
     * Entrada siguiente
     */
    public static function siguiente( $schema, $codInstrumento, $idInstrumento, $prospecto, $entrada, $op = 0 ){
        $x = '';
        $cant = count($entrada);
        self::$codigo[0] = 0;
        for( $i=0; $i < $cant ; $i++ ){
            self::$codigo[$entrada[$i]->codigo] = $entrada[$i]->id;
            if( isset($entrada[$i]->ir_a) )
                self::$orden[$entrada[$i]->codigo] = $entrada[$i]->ir_a;
        }    
        for( $i=1; $i < $cant ; $i++ ){
            $sig = self::ir_a( $entrada[$i], $i < $cant );

            $x .= self::entrada( $schema, $codInstrumento, $idInstrumento, $prospecto, $entrada[$i], 'none', $op );
        }
        return $x;
    } // eof ##################################################
    



    // no usado
    public static function fin_deprecate( $next = 0 ){ // type="submit"
        $x = '<div class="form-group" id="x3entrada_0" style="display:none;"><hr />';
        $x .= '<label for="pfinal">Gracias por su coperación</label>';
        $x .= '</div>';
        $x .= '<div id="btn_0" class="btn btn-default" onClick="" style="width:150px; display:none;"> Finalizar </div>'."\n";
        $x .= '<br /></form>';
        return $x;
    } // eof ##################################################    
    
        

    

    /**
     * Cancela una llamada     
    */
    public static function colgar( $schema = null, $idInstrumento = null, $idProspecto = null, $idLlamada = null, $idTipificacion = null ){
        if( $schema == null ){
            $config = self::configuracion();
            $schema = $config->cod_proyecto;
            $idInstrumento = $config->id_instrumento;
        }
        $id_usuario = \Yii::$app->user->identity->id;
        
        if( (int)$idProspecto == 0 ){
            $sql = "select l.*,p.id_instrumento from " .$schema .".llamada l inner join " .$schema .".prospecto p on p.id=l.id_prospecto  where l.id_usuario='" .$id_usuario ."' and l.st='4' order by l.id DESC limit 1;";
            $obj = Aux::findBySql($sql)->one();
            $idProspecto = $obj->id_prospecto;
            $idLlamada = $obj->id;
        }
        

        if( (int)$idLlamada == 0 ){
            $sql = "select id from " .$schema .".llamada where id_prospecto='" .$idProspecto ."' and id_usuario='" .$id_usuario ."' and st='4' order by id DESC;";
            $obj = Aux::findBySql($sql)->one();
            $idLlamada = $obj->id;
        }


        if( (int)$idTipificacion == 0 ) $idTipificacion = 22;
        
        
        // Tipificar llamada
        $sql = "update " .$schema .".llamada set st='5', id_tipificacion='" .$idTipificacion ."', fin=now() where id='" .$idLlamada ."';";
        Aux::findBySql($sql)->one();
        // Tipificar llamada que dejo activas -> las elimina 
        $sql = "update " .$schema .".llamada set st='0', id_tipificacion='3', fin=now() where st='4' and id_usuario='" .$id_usuario ."';";
        Aux::findBySql($sql)->one();

        
        // Elimimar asignacion del prospecto al usuario
        $sql = "delete from " .$schema .".usuario_prospecto where id_prospecto='" .$idProspecto ."' and id_usuario='" .$id_usuario ."';";
        // Elimimar asignaciones de prospectos al usuario pendientes
        $sql = "delete from " .$schema .".usuario_prospecto where id_usuario='" .$id_usuario ."';";
        Aux::findBySql($sql)->one();

         $seq = '';

        // Update prospecto
        switch( $idTipificacion ){
        case '16':case '17':case '18':case '19':case '23':case '26':
        case '27':case '30':case '31':case '32':case '35':case '36':
            $sql = "update " .$schema .".prospecto set st='2', id_tipificacion='".$idTipificacion."', up=now(), " .$seq ." fin=now() where id='" .$idProspecto ."';";
            Aux::findBySql($sql)->one();
            break;
            
        case '20':case '21':case '22':case '25':
        case '28':case '29':case '33':case '34':
            $sql = "update " .$schema .".prospecto set st='5', id_tipificacion='".$idTipificacion."', up=now(), " .$seq ." fin=now() where id='" .$idProspecto ."';";
            Aux::findBySql($sql)->one();
            break;
           
        default:
            echo $idTipificacion;
            break;
        }

        // desasignar prospectos pendientes 
        $sql = "update " .$schema .".prospecto set st='2', id_tipificacion='3', up=now() where st='4' and tlo='" .$id_usuario ."';";
        Aux::findBySql($sql)->one();
    } // eof #######################################################

   
    
    
    /**
     * Despliega las opciones para colgar una llamada
     */
    public static function esperar( $idCuota = null, $idProspecto = null, $llamada = null ){
        Yii::$app->layout = 'embebido';
        $cad = '';
        $id_usuario = Usuario::id();
        
        $config = self::configuracion();
        $schema = $config->cod_proyecto;
        $codInstrumento = $config->cod_instrumento;
        $idInstrumento = $config->id_instrumento;
        $llamada = Request::rq('x3llamada');

        
        
 
        $prospecto = self::prospecto2( $schema, $idInstrumento, $idProspecto );
       

        
        $cad = '';
        $cad .= '<br /><br />';  
        $cad .= '<table width="100%" border="0"><tbody>';
        $clase = ' class="ui-button ui-corner-all ui-widget" ';        
        

        if( $llamada == null ){
            $sql = "select id from " .$schema .".llamada where id_prospecto='" .$prospecto->id ."' and id_usuario='" .$id_usuario ."' and st='4';";
            $obj = Aux::findBySql($sql)->one();
            $llamada = $obj->id;
        }
        
        $c1 = "carga( 'x3DivEncuesta', 'x3proy=" .$schema ."&x3cuota=" .$idCuota ."&x3inst=" .$idInstrumento ."&x3prosp=" .$idProspecto ."', 'contacto2', 'proyecto/" .$schema ."/" .$codInstrumento ."' );";
        $onclick1 = ' onClick="' .$c1 .'" ';

        
        $st = '';
        $style1 = ' style="width: 100%; '.$st .' color: #009966; line-height: 1.5em; height: 100%; font-weight:700;" ';
        $style2 = ' style="width: 100%; '.$st .' color: #773333; line-height: 1.2em; height: 4.8em; font-weight:700; vertical-align:middle;" ';

        $aux1 = $clase .$onclick1 .$style1;

  
        $sqlX = "select valor, de from " .$schema .".tipificacion where st='1' and valor > '10' and id !='15' order by id ASC;";
        $objX = Aux::findBySql($sqlX)->all();
       
            $c32 = Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/llamar_luego&x3proy=' .$schema .'&x3inst=' .$idInstrumento .'&x3prosp=' .$idProspecto ."&x3res=" .$ox->id ."&x3llamada=" .$llamada;
        
        $h = 0;
        foreach( $objX as $ox ){
            $c3 = Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/colgar&x3proy=' .$schema .'&x3inst=' .$idInstrumento .'&x3prosp=' .$idProspecto ."&x3res=" .$ox->id ."&x3llamada=" .$llamada;
            
            $aux2 = $clase .$style2;
            if( $h == 0 ){
                if( $config->llamar_luego == 1 && $ox->valor == '7' )
                    $cad .= '<tr style="height:4.8em;"><td width="24%"><a href="' .$c32 .'"><div ' .$aux2 .'  >' .Cadena::acortar('Llamar Luego') .'</div></a></td>';
                else if( $ox->valor == '15' )
                    $cad .= '<tr style="height:4.8em;"><td width="24%"><div ' .$aux1 .' >' .$ox->de .'</div></td>';
                else
                    $cad .= '<tr><td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .'  >' .Cadena::acortar($ox->de) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 1;
            }else if( $h == 1 ){
                $cad .= '<td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .'  ">' .Cadena::acortar($ox->de) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 2;
            }else if( $h == 2 ){
                $cad .= '<td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .'  >' .Cadena::acortar($ox->de) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 3;
            }else{
                $cad .= '<td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .' >' .Cadena::acortar($ox->de) .'</div></a></td></tr>';
                $h = 0;
            }
        } // for 
        return $cad .'</tbody></table>';
    } // eof ##################################################




    


    


    /**
     * Bloquea el prospecto
     */
    // #######################  
    public static function getBlockearProspecto( $schema, $id_instrumento, $id_prospecto ){
       
        $id_usuario = \Yii::$app->user->identity->id;        
        // Verifica si el prospecto, ya esta asignado a si mismo
        $sql = "select * from " .$schema .".usuario_prospecto where st='4' and id_usuario='" .$id_usuario ."' and id_prospecto='" .$id_prospecto ."' and id_instrumento='" .$id_instrumento ."';";
        $obj = Aux::findBySql($sql)->all();
        if( count($obj) == 0 ){  // Bloquea el Prospecto
       
            $sql = "update " .$schema .".prospecto set st='4', tlo='" .$id_usuario ."', up=now(), inicio=now(), barrida=barrida +1 where id='" .$id_prospecto ."';";
            Aux::findBySql($sql)->one();
            
            // &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&   crea una llamada
       
            $sql = "select barrida from " .$schema .".barrida where st='1' and id_instrumento='" .$id_instrumento ."';";
            $objB = Aux::findBySql($sql)->one();
            $sql = "insert into " .$schema .".llamada (st, id_usuario, id_prospecto, reg, id_tipificacion, barrida) values ('4', '" .$id_usuario ."', '" .$id_prospecto ."', now(), '4', '" .$objB->barrida ."');";
            Aux::findBySql($sql)->one();
            $sql = "select id from " .$schema .".llamada where st='4' and id_usuario='" .$id_usuario ."' and id_prospecto='" .$id_prospecto ."' and id_tipificacion = '4';";
            $obj = Aux::findBySql($sql)->one();
                
            // &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&  registra el prospecto con el usuario                
            $sql = "insert into " .$schema .".usuario_prospecto (st, id_usuario, id_prospecto, reg, id_llamada, id_instrumento) values ('4','" .$id_usuario ."','" .$id_prospecto ."',now(),'" .$obj->id ."','" .(int)$id_instrumento ."');";
            Aux::findBySql($sql)->one();                
            
            // fin    
        }else{
            // finaliza la llamada anterior
            $sql = "update " .$schema .".llamada set st='6', id_tipificacion='3' where id_usuario = '" .$id_usuario ."' and id_prospecto='" .$id_prospecto ."' and id_tipificacion='5' limit 1;";
            Aux::findBySql($sql)->one();
            
            // crea una llamada
            $sql = "insert into " .$schema .".llamada (st, id_usuario, id_prospecto, reg, id_tipificacion) values ('4','" .$id_usuario ."','" .$id_prospecto ."', now(), '4');";
            Aux::findBySql($sql)->one();
            $sql = "select id from " .$schema .".llamada where st='4' and id_usuario='" .$id_usuario ."' and id_prospecto='" .$id_prospecto ."' and id_tipificacion = '4' order by id DESC;";
            $obj = Aux::findBySql($sql)->one();
            
            // registra el prospecto con el usuario                
            $sql = "update " .$schema .".usuario_prospecto set id_llamada='" .$obj->id ."', reg= now() where id_usuario='" .$id_usuario ."'  and id_prospecto='" .$id_prospecto ."' limit 1;";
            Aux::findBySql($sql)->one();
        }
        return; // $obj;
    } // eof ##################################################
    


    
    /**
     *  Llamadas pendientes
     */
    public static function getProspectoPendiente(){
        return null;
    } // eof ##################################################
    
    


    // creo que no lo uso
    public static function seleccione_deprecate( $proyecto, $inst, $columna_cod, $dom, $fecha ){
        $sql = "select p.*, b.barrida as veces, c.cuota, c.conteo from " .$proyecto .".cuota c inner join " .$proyecto .".dominio d on c.id_dominio = d.id inner join " .$proyecto .".prospecto p on p." .$columna_cod ."=d.cod and d.id='" .$dom ."' inner join " .$proyecto .".barrida b on b.id_instrumento=c.id_instrumento where p.st in (1,2,1201) and c.id_instrumento='" .$inst ."' and c.fecha_encuesta='" .$fecha ."' order by random() limit 1;";
        $obj = new Aux();
        $reg = Aux::findBySql($sql)->one();
        return $reg;
    } // eof #######################################################
    



    /**
     * Todas las tareas que sed debn hacer para colgar un TLO
     */
    public static function colgarTLO( $schema, $inst = 0 ){
        if( $inst != 0 ){
            $sql1 = "select id_prospecto from " .$schema .".llamada where id_instrumento='" .$inst ."' and st != '5' and st != '6' ;";
            $objs1 = Aux::findBySql($sql1)->all();
            foreach( $objs1 as $obj ){
                $sql2 = "update " .$schema .".prospecto set id_tipificacion='3',st='2' where id_instrumento='" .$inst ."' id='" .$obj->id_prospecto ."' ;";
                $objs2 = Aux::findBySql($sql)->one();
            }
            $sql3 = "update " .$schema .".llamada set id_tipificacion='3' where id_instrumento='" .$inst ."' and st != '5' and st != '6' ;";
            $objs3 = Aux::findBySql($sql3)->all();
            $sql3 = "delete from " .$schema .".usuario_prospecto where id_instrumento='" .$inst ."';";
            $objs3 = Aux::findBySql($sql3)->all();
        }
        return;
    } // eof ##################################################



   
    /**
     * lista de dominios, para select, NO usado en contacto1
     */
    public static function selectDominio( $schema ){
        $op = Request::rq('x3op');
        $inst = Request::rq('x3inst');
        $cad = '<label for="id_dominio">Zona:</label>';
        $cad .= '<select class="form-control" id="id_dominio" name="id_dominio" onchange="alert(\'Pendiente\');"  style="width:60%;">';
        $cad .= '<option value=""> --- Seleccione --- </option>';
        if( $inst != null ){
            $sql = "select id,de,cod from " .$schema .".dominio where st in (1) and id_instrumento='" .$inst ."' order by cod ASC;";
            $regs = Aux::findBySql($sql)->all();
            foreach( $regs as $v )
                $cad .= '<option value="' .$v->id .'">' .$v->cod .' - ' .$v->de .'</option>';
        }
        return $cad .'</select> &nbsp; &nbsp; ';
    } // eof #######################################################
    

 
     
} // class
