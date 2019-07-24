<?php
namespace app\components\crm;


use Yii;


use app\components\crm\Grafico;
use app\components\crm\JQGrid;
use app\components\crm\Menu;
use app\components\crm\Request;
use app\components\crm\Formato;
use app\components\crm\Cadena;
use app\components\crm\Ayuda;
use app\components\crm\Seccion;



use app\models\Aux;

use app\models\test\cProyecto;
use app\models\crm\Instrumento;
use app\models\crm\xInstrumento;
use app\models\AuthAssignment;

/**
 * Clase helper para reportes
 */
class Reporte{
    



    
    
    function init_deprecate(){
        parent::init();

        $this->orden = array();
        $this->codigo = array();
    } // eof #######################################################

    
    public static function JQGrid_deprecate( $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        return JQGrid::listado( $this->schema(), $cabecera, $registros, $titulo, $json, $this->schema().$subact, $detail );
    } // eof #######################################################




    



    /**
     * Resumen de todos los instrumentos
     */
    public static function resumenes(){
        //   INICIALIZACION
        
        $cad = $cad2 = '';
        $dataAux = array();
        $data = array();

        $dato[] = 'Proyecto';
        $dato[] = 'Instrumento';
        $dato[] = 'Última día';
        $dato[] = '1 día antes';
        $dato[] = '2 días antes';
        $dato[] = '3 días antes';
        $dato[] = '4 días antes';
        $dato[] = '5 días antes';
        $dato[] = '6 días antes';

        $dia = date('Y-m-d');
        $f = Aux::findBySql("select de from a.fechas where id='1';")->one();
        if( isset($f->de) && $f->de != $dia ){    
            for( $g=0; $g < 7 ; $g++ ){
                $diaAx = date('Y-m-d', strtotime('-' .$g .' day', strtotime($dia)));
                Aux::findBySql("update a.fechas set de='" .$diaAx ."' where id='" .($g+1) ."';")->one();  
            }
        }


        
        $cad .= Seccion::titulo('Cuadro de comportamiento de los últimos 7 días');
        
        $instrumentos = '';
        $data[] = $dato; unset($dato);
        //   PROYECTOS
        $sql0 = "select id, de, codigo from a.proyecto where st=1 order by de asc;";
        $obj0s = Aux::findBySql($sql0)->all();
        foreach( $obj0s as $obj0 ){
            
            //    INSTRUMENTOS
            $sql1 = "select id, de, codigo, cuotas from " .$obj0->codigo .".instrumento where st=1 order by de asc;";
            $obj1s = Aux::findBySql($sql1)->all();
            foreach( $obj1s as $obj1 ){

                $instrumentos .= "data0.addColumn('number', '" .$obj0->de.' - '. $obj1->de ."');\n";

                
                //    CUADRO DE CUOTA Y CONTEO POR INSTRUMENTO, ULTIMOS 7
                $dato[] = $obj0->de;
                $dato[] = $obj1->de;
                if( $obj1->cuotas == 1 ){ // conteo por cuotas
                    $sql = "select f.de, sum(c.cuota) as cuota, sum(c.conteo) as conteo 
from a.fechas f 
left join " .$obj0->codigo .".cuota c on f.de= c.fecha_encuesta and id_instrumento='" .$obj1->id ."' 
group by f.de order by f.de desc;";
                }else{ // conteo por tipificacion de prospectos
                    $sql = "select de, sum(efectivas) as conteo, sum(cargados) as cuota from (
select distinct
f.de, d.id as id_data, count(p.*) as efectivas, d.cantidad as cargados
from a.fechas f 

left join " .$obj0->codigo .".prospecto p on p.id_tipificacion = '1' and f.de = to_char(p.up,'YYYY-MM-DD')
left join " .$obj0->codigo .".data d on d.id_instrumento='" .$obj1->id ."' and p.id_data = d.id
 group by f.de,d.cantidad, d.id
) a group by de order by de desc";
                }
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $obj ){
                    if( $obj->cuota > 0 ){
                        $porc = round(($obj->conteo / $obj->cuota) * 100,2);
                        if( $obj->conteo < $obj->cuota )
                            $dato[] = '<span style="color: #aa0000;">' .$obj->conteo.' de '.$obj->cuota .'.</span> <span style="color: #000077;">' .$porc .' %</span>' ;
                        else
                            $dato[] = '<span style="color: #008800;">' .$obj->conteo.' de '.$obj->cuota .'.</span> <span style="color: #000077;">' .$porc .' %</span>' ;    
                        $dataAux[$obj0->codigo .'-'. $obj1->id][] = $porc;    
                    }else
                        $dato[] = '';
                }
                $data[] = $dato; unset($dato);
                
                
                    
                    
                    
                
                //    GRAFICO DE CUOTA Y CONTEO POR INSTRUMENTO
                $aux = '';
                $vct[] = $obj0->codigo;
                $vct[] = $obj0->de;
                $vct[] = $obj1->id;
                $vct[] = $obj1->de;
                $sql = "select fecha_encuesta, sum(cuota) as cuota, sum(conteo) as conteo from " .$obj0->codigo .".cuota where id_instrumento='" .$obj1->id ."' group by fecha_encuesta order by fecha_encuesta asc;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $obj )
                    $aux .= "['" .Formato::date($obj->fecha_encuesta, 'd,m') ."'," .$obj->cuota .',' .$obj->conteo .'],';
                $vct[] = $aux;
                if( $aux != '' ){
                    $cad2 .= '<hr /><br />' .Seccion::titulo('' .$obj0->de .' - ' .$obj1->de .'');
                    $cad2 .= self::chartResumen( $vct );
                }
                unset($vct);

                
            }
        }
        $cad .= Listado::listado($data); unset($data);


        return $cad .'<hr />';
    } // eof ##################################################
    



    /**
     * Grafico del reporte de resumen, versión 0
     */
    static function chartResumen0( $instrumentos, $param ){
        return "<script type=\"text/javascript\">\n
      google.charts.load('current', {'packages':['line']});\n
      google.charts.setOnLoadCallback(drawChart0);\n
    function drawChart0(){\n
      var data0" ." = new google.visualization.DataTable();\n
      data0.addColumn('string', 'Días');\n
      " .$instrumentos ."
      data0.addRows([" .$param ."]);\n
      var options0 = {
        chart: {title: 'Comportamiento histórico por día'},
        height: 500,
        axes: {x: {0: {side: 'top'}}}
      };\n
      var chart0 = new google.charts.Line(document.getElementById('x3GraficoLog0'));\n
      chart0.draw(data0, options0);\n
    }\n
  </script>\n
  <div id=\"x3GraficoLog0\"></div>\n";
    }
    
    
    
    /**
     * Grafico del reporte de resumen
     */
    static function chartResumen( $vct ){
        $cad = '<div style="subtitulo"> Resumen de</div>';
        $cad .= '<div style="subtitulo">' .$vct[1] .' - ' .$vct[3] .'</div>';
        $cad .= "<script type=\"text/javascript\">\n
      google.charts.load('current', {'packages':['line']});\n
      google.charts.setOnLoadCallback(drawChart" .$vct[0] ."_" .$vct[2] .");\n
    function drawChart" .$vct[0] ."_" .$vct[2] ."(){\n
      var data" .$vct[0] ."_" .$vct[2] ." = new google.visualization.DataTable();\n
      data" .$vct[0] ."_" .$vct[2] .".addColumn('string', 'Días');\n
      data" .$vct[0] ."_" .$vct[2] .".addColumn('number', 'Planificado');\n
      data" .$vct[0] ."_" .$vct[2] .".addColumn('number', 'Ejecutado');\n
      data" .$vct[0] ."_" .$vct[2] .".addRows([" .substr($vct[4],0,-1) ."]);\n
      var options" .$vct[0] ."_" .$vct[2] ." = {
        chart: {title: 'Comportamiento histórico por día'},
        height: 500,
        axes: {x: {0: {side: 'top'}}}
      };\n
      var chart" .$vct[0] ."_" .$vct[2] ." = new google.charts.Line(document.getElementById('x3GraficoLog" .$vct[0] ."_" .$vct[2] ."'));\n
      chart" .$vct[0] ."_" .$vct[2] .".draw(data" .$vct[0] ."_" .$vct[2] .", options" .$vct[0] ."_" .$vct[2] .");\n
    }\n
  </script>\n
  <div id=\"x3GraficoLog" .$vct[0] ."_" .$vct[2] ."\"></div>\n";
              return $cad;
    }
    
    
    
    
    
    











    /**
     * Listado de Reportes
     */
    public static function index( $cod_proyecto, $idInstrumento, $codInstrumento ){
        $id_usuario = \Yii::$app->user->identity->id;
        $objs = Aux::findBySql("select r.id, r.tp, r.de, r.codigo from a.reporte r inner join a.reporte_instrumento ri on ri.id_reporte=r.id where r.st = '1' and ri.st='1' and ri.proyecto='" .$cod_proyecto ."' and id_instrumento='" .$idInstrumento ."' order by ri.orden asc, r.de asc;")->all();
        if( count($objs) > 0 )
            foreach( $objs as $obj )
                if( $obj->tp == 1 )
                    $registros[] = array( $obj->de, $obj->codigo, $obj->tp );
                else
                    $registros[] = array( $obj->de, $obj->id, $obj->tp );
        else
            $registros= array();
        return '<br /><br />' .Mensaje::mostrar() .Listado::listado5( $cod_proyecto, $codInstrumento, $idInstrumento, $registros );
    } // eof ##################################################


   


   
    /**
     * Reporte de resumen
     */
    public static function resumen( $cod_proyecto = null, $id_instrumento = null, $onlyExcel = false ){
        if( $cod_proyecto == null ){
            $schema = $cod_proyecto = Request::rq('x3proy');
            if( $cod_proyecto.'' == '' ) $schema = $cod_proyecto = Request::rq('cod_proyecto');
        }else $schema = $cod_proyecto = (int)$cod_proyecto;
        if( $id_instrumento == null ){
            $inst = $id_instrumento = (int)Request::rq('x3inst');
            if( $id_instrumento == 0 ) $inst = $id_instrumento = Request::rq('id_instrumento');
        }else $inst = $id_instrumento = (int)$id_instrumento;
        $ax = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."';")->one();
        $codInstrumento = $ax->codigo;
        
        $data = null;
        $cad = '';

        $desde = Request::rq('x3desde');
        $hasta = Request::rq('x3hasta');
        $tlo = Request::rq('x3tlo');
       
            
           
        $reporte = Request::rq('x3rpt');
        $data_tp = Request::rq('x3data_tp');
        $fecha_tp = Request::rq('x3fecha_tp');
        $id_dominio = Request::rq('x3dom');
        $agente = Request::rq('x3agente');

        if( $inst > 0 ){
            $sql = "select de from " .$schema .".instrumento where id='" .$inst ."' and st = '1';";
            $obj = Aux::findBySql($sql)->one();
            $titulo = $obj->de;
        }else $titulo = 'Todos los Instrumentos';


        $idInstrumento = $inst;
        
        $hoy = date('Y-m-d');

        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'resumen';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($id_dominio) ) $id_dominio = null;
        if( is_null($agente) ) $agente = null;        
        
        

        

        $cabecera[] = 'Instrumento';
        $cabecera[] = 'Fecha';

        $cabecera[] = 'Ejecutado';
        $cabecera[] = 'Planificado';
        $cabecera[] = 'Excedente';
        $cabecera[] = 'Cumplimiento';
        $cabecera[] = 'LLamadas';
        $cabecera[] = 'Efectividad';
        $data[] = $cabecera;
        

        if( (int)$inst == 0 )
            $sql1 = "select id,de from " .$schema .".instrumento where st='1';";
        else
            $sql1 = "select id,de from " .$schema .".instrumento where st='1' and id='" .$inst ."';";
        $obj1 = Aux::findBySql($sql1)->all();


        if( $fecha_tp == 1 ){ // fecha de encuesta
            $ff = " (fecha_encuesta >= '" .$desde ."' and fecha_encuesta <=  '" .$hasta ."') ";
            $fff = ' fecha_encuesta ';
            $f4 = " and ( ((l.reg)::date)::text  >= '" .$desde ."' and ((l.reg)::date)::text  <=  '" .$hasta ."' ) ";
        }else{
            $ff = " (fecha_ref >= '" .$desde ."' and fecha_ref <=  '" .$hasta ."') ";
            $fff = ' fecha_ref ';
            $f4 = '';
        }

        foreach( $obj1 as $reg1 ){ // recorro cada instrumento
            $sql2 = "select id, de, cod from " .$schema .".dominio where st='1' and id_instrumento='" .$reg1->id ."' order by cod ASC;";
            $obj2 = Aux::findBySql($sql2)->all();

            
            
            if( count($obj2) > 0 ){
                foreach( $obj2 as $reg2 ){ // recorro cada dominio
                                        
                    $sql3 = "select d.cod, c." .$fff ." as fecha, d.de as dominio, sum(cuota) as cuota, sum(conteo) as cuenta from " .$schema .".cuota c inner join " .$schema .".dominio d on d.id=c.id_dominio and d.st='1' where " .$ff ."  and c.st in (1,2,5) and c.id_instrumento='" .$reg1->id ."' and c.id_dominio='".$reg2->id."' group by de, cod, " .$fff ." order by " .$fff ." ASC;";
                    
                    $obj3 = Aux::findBySql($sql3)->all();
                    foreach( $obj3 as $reg3 ){
                        $dt[] = $reg1->de .' - ' .$reg3->dominio;
                        $dt[] = $reg3->fecha;
                        $dt[] = $reg3->cuenta;
                        $dt[] = $reg3->cuota;
                        $dt[] = max( $reg3->cuenta - $reg3->cuota, 0);
                        if( $reg3->cuota > 0 )
                            $dt[] = min(round(($reg3->cuenta / $reg3->cuota) * 100,2),100) .' %';
                        else
                            $dt[] = '0 %';
                        
                        $sql4 = "select count(l.*) as count 
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on l.id_prospecto = p.id  
inner join " .$schema .".dominio d on p.c007 = d.cod and d.st='1' where d.id_instrumento='" .$reg1->id ."' and d.id='".$reg2->id."' and l.st in (4,5)  " .$f4 ."  ;";
                           
                              
                        $obj4 = Aux::findBySql($sql4)->one();
                        $dt[] = $obj4->count;
                        if( $obj4->count > 0 )
                            $dt[] = min(round(($reg3->cuenta / $obj4->count) * 100,4),100) .' %';
                        else
                            $dt[] = '0 %';
                        $data[] = $dt;
                        unset($dt);
                    }
                }
            }else{
                $sql3 = "select sum(cuota) as cuota, c." .$fff ." as fecha, sum(conteo) as cuenta from " .$schema .".cuota c where " .$ff ."  and c.st in (1,2,5) and c.id_instrumento='" .$reg1->id ."' group by " .$fff ." order by " .$fff ." ASC;";

                $obj3s = Aux::findBySql($sql3)->all();
                if( count($obj3s) > 0 ){
                    $obj3 = $obj3s[0];
                    $dt[] = $reg1->de;
                    $dt[] = $obj3->fecha;
                    $dt[] = $obj3->cuenta;
                    $dt[] = $obj3->cuota;
                    $dt[] = max($obj3->cuenta - $obj3->cuota,0);
                    if( $obj3->cuota > 0 )
                        $dt[] = min(round(($obj3->cuenta / $obj3->cuota) * 100,2),100) .' %';
                    else
                        $dt[] = '0 %';
                    
                    $sql4 = "select count(l.*) as count 
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on l.id_prospecto = p.id  
 where p.id_instrumento='" .$reg1->id ."' and l.st in (4,5);";
                    $obj4 = Aux::findBySql($sql4)->one();
                    $dt[] = $obj4->count;
                    if( $obj4->count > 0 )
                        $dt[] = min(round(($obj3->cuenta / $obj4->count) * 100,4),100) .' %';
                    else
                        $dt[] = '0 %';
                    
                    $data[] = $dt;
                    unset($dt);
                }
            }
        }

        $t_ejecutado = $t_planificado = $t_excedente = $t_llamadas = 0;
        foreach( $data as $vc1 ){
            $t_ejecutado += $vc1[2];
            $t_planificado += $vc1[3];
            $t_excedente += $vc1[4];
            $t_llamadas += $vc1[6];
        }
        $vc2[] = 'Total';
        $vc2[] = '';
        $vc2[] = $t_ejecutado;
        $vc2[] = $t_planificado;
        $vc2[] = $t_excedente;


        if( $t_planificado > 0 )
            $vc2[] = min(round(($t_ejecutado / $t_planificado) * 100,2),100) .' %';
        else
            $vc2[] = '0 %';

        $vc2[] = $t_llamadas;
        
        if( $t_llamadas > 0 )
            $vc2[] = min(round(($t_ejecutado / $t_llamadas) * 100,4),100) .' %';
        else
            $vc2[] = '0 %';
        
        $data[] = $vc2;
       


        if( $onlyExcel == false ){ 
            
            $style[] = 'padding:0.3em;';
            $style[] = 'text-align:center; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            
            $cad = Reporte::filtro1( 'Resumen General', $schema, $codInstrumento, null, 'resumen', 'resumen');
            
            $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
            $cad .= Reporte::toHtml( $data, $style );
            $cad .= '<br /><hr />';
            $cad .= Grafico::toChart1($schema, $idInstrumento);
            
            $cad .= '<br /><hr /><br /><br />';
        }else{
            $cad .= Reporte::hojaDeCalculo( $data, true );
        }
            
        return $cad;
    } // eof ##################################################
    













    /**
     * Transfiere a HTML, versión 1
     */
    public static function toHtml( $data, $style = null, $size = '0.8em' ){
        $c = '';
        $c1 = '#fffff6';
        $c2 = '#f6f6ff';
        $c3 = '#fff6ff';
        $c1 = '#aa0000';
        $c2 = '#00aaaa';
        $c3 = '#0000aa';
        $cad = '<div style="font-size: 0.8em;"><table border="0" style="width:100%;">';        
        $cad .= '<tbody>';
        $aux = $aux2 = '';
        $j = 0;
        foreach( $data as $v1 ){
            if( $j == 0 ){
                $cad .= '<tr>';
                foreach( $v1 as $v )
                    $cad .= '<td style="font-size:' .$size .'; background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700;"> ' .strtoupper($v) .' </td>';
                $cad .= '</tr>';
            }else{
                if( $c == $c1 ) $c = $c2;
                else if( $c == $c2 ) $c = $c3;
                else $c = $c1;

                $cad .= '<tr>';
                $i = 0;
                foreach( $v1 as $v2 ){
                    if( $style != null ) $s = ' style="' .$style[$i] .'; border-bottom: solid 1px '.$c.'; font-size:' .$size .';" ';

                    else $s = '';
                    if( $i == 0 ){
                        if( $aux == $v2 ){
                            $cad .= '<td' .$s .'>  </td>';
                            $aux2 = $v2;
                        }else
                            $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                        $aux = $v2;
                    }else
                        $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                    $i++;
                } // for 
                $cad .= '</tr>';
            }
            $j++;
        } // for 
        return $cad .'</tbody></table></div>';
    } // eof ##################################################









    /**
     * Transfiere a HTML, versión 3
     */
    public static function toHtml3( $data, $style = null ){
        $c = '';
        $c1 = '#fffff6';
        $c2 = '#f6f6ff';
        $c3 = '#fff6ff';
        $c1 = '#aa0000';
        $c2 = '#00aaaa';
        $c3 = '#0000aa';
        $cad = '<div style="font-size: 0.8em;"><table border="0" style="width:100%;">';        
        $cad .= '<tbody>';
        $aux = $aux2 = '';
        $j = 0;
   
        for( $s=6; $s <= 324 ;$s+=3) $saltar[] = $s;
     
        
        foreach( $data as $v1 ){
            if( $j == 0 ){
                $cad .= '<tr>';
                $contador = 0;
                $marcado = 0;
                foreach( $v1 as $v ){
                    $contador++;
                    $marcado--;

                    if( $contador == 3 ){
                        $cad .= '<td colspan="4" style="font-size:0.8em;background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700;"> ' .strtoupper($v) .' </td>';
                        $marcado = 3;
                    }else if( $contador > 3 ){
                        $cad .= '<td colspan="3" style="font-size:0.8em;background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700;"> ' .strtoupper($v) .' </td>';
                        $marcado = 2;
                    }else{
                        $cad .= '<td style="font-size:0.8em;background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700;"> ' .strtoupper($v) .' </td>';
                    }
                }
                $cad .= '</tr>';
            }else if( $j == 1 ){
                $cad .= '<tr>';
                $contador = 0;
                $marcado = 0;
                foreach( $v1 as $v ){
                    $cad .= '<td style="font-size:0.8em;background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700;"> ' .strtoupper($v) .' </td>';
                }
                $cad .= '</tr>';
            }else{
                if( $c == $c1 ) $c = $c2;
                else if( $c == $c2 ) $c = $c3;
                else $c = $c1;
                $cad .= '<tr>';
                $i = 0;
                foreach( $v1 as $v2 ){
                    if( $style != null && isset($style[$i]) ) $s = ' style="' .$style[$i] .'; border-bottom: solid 1px '.$c.';" ';
                    else $s = '';
                    if( $i == 0 ){
                        if( $aux == $v2 ){
                            $cad .= '<td' .$s .'>  </td>';
                            $aux2 = $v2;
                        }else
                            $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                        $aux = $v2;
                    }else
                        $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                    $i++;
                } // for 
                $cad .= '</tr>';
            }
            $j++;
        } // for 
        return $cad .'</tbody></table></div>';
    } // eof ##################################################






    


    /**
     * Grafico 1
     */
    public static function toChart1( $schema, $idInstrumento ){
        $aux = '';
        $sql = "select fecha_encuesta, sum(cuota) as cuota, sum(conteo) as conteo from " .$schema .".cuota where id_instrumento='" .$idInstrumento ."' group by fecha_encuesta order by fecha_encuesta asc;";
        $objs = Aux::findBySql($sql)->all();
        foreach( $objs as $obj )
            $aux .= "['" .Formato::date($obj->fecha_encuesta, 'd,m') ."'," .$obj->cuota .',' .$obj->conteo .'],';
        $cad = "<script type=\"text/javascript\">\n
      google.charts.load('current', {'packages':['line']});\n
      google.charts.setOnLoadCallback(drawChart);\n
    function drawChart(){\n
      var data = new google.visualization.DataTable();\n
      data.addColumn('string', 'Días');\n
      data.addColumn('number', 'Planificado');\n
      data.addColumn('number', 'Ejecutado');\n
      data.addRows([" .substr($aux,0,-1) ."]);\n
      var options = {
        chart: {
          title: 'Comportamiento historico',
          subtitle: 'por días'
        },
        width: 900,
        height: 500,
        axes: {
          x: {0: {side: 'top'}}
        }
      };\n
      var chart = new google.charts.Line(document.getElementById('x3GraficoLog'));\n
      chart.draw(data, options);\n
    }\n
  </script>\n
  <div id=\"x3GraficoLog\"></div>";
             return $cad;
    } // eof ##################################################

    


    /**
     * Grafico 2
     */
    public static function toChart2( $schema, $idInstrumento, $data, $periodo = null){
        $aux = '';
   
        foreach( $data as $reg )
            $aux .= "['" .$reg[0] ."'," .$reg[1] .',' .$reg[2] .'],';
        $cad = "<script type=\"text/javascript\">\n

      google.charts.load('current', {'packages':['corechart']});\n
      google.charts.setOnLoadCallback(drawChart);\n
    function drawChart(){\n
       var data = new google.visualization.DataTable();\n

 

      data.addColumn('string', 'Teleoperadores');\n
      data.addColumn('number', 'Efectivas');\n
      data.addColumn('number', 'No Efectivas');\n
      data.addRows([" .substr($aux,0,-1) ."]);\n

     var view = new google.visualization.DataView(data);\n
      view.setColumns([0, 1,
                       { calc: 'stringify',
                         sourceColumn: 1,
                         type: 'string',
                         role: 'annotation' },
                       2]);

      var options = {
        chart: {
          title: 'Comportamiento historico',
          subtitle: '" .$periodo ."',
        },
        width: 900,
        height: 500,
      };\n

      var chart = new google.visualization.BarChart(document.getElementById('x3GraficoLog'));\n
      chart.draw(data, options);\n
    }\n
  </script>\n
  <div id=\"x3GraficoLog\"></div>";
        
             return $cad;
    } // eof ##################################################

    

    /**
     * Grafico 5
     */
    public static function toChart5( $schema, $data, $periodo = null){
        $aux = '';        
        $cad = "<script type=\"text/javascript\">
      google.charts.load(\"current\", {packages:[\"corechart\"]});";
        $vct[] = 'Total de Llamadas';
        $vct[] = 'Clientes Contactados';
        $vct[] = 'Encuestas Planificadas';
        $vct[] = 'Encuestas Efectivas';
        for( $i=0 ; $i < 4 ; $i++ ){
            $cad .= "google.charts.setOnLoadCallback(drawChart" .$i .");
      function drawChart" .$i ."(){
        var data" .$i ." = google.visualization.arrayToDataTable([
          ['Instrumento', 'Cantidad'],";
            
            foreach( $data[$i] as $vt )
                $cad .= "  ['" .$vt[0] ."', " .$vt[1] ."],";
            
            $cad .= " ]);
        var options" .$i ." = {
          title: '" .$vct[$i] ."',
          is3D: true,
        };
       var chart" .$i ." = new google.visualization.PieChart(document.getElementById('x3Grafico" .$i ."'));
        chart" .$i .".draw(data" .$i .", options" .$i .");
      }";
        }
        
        $cad .= "</script>
<table style=\"border:none;width:100%;text-align:center;\"><tbody><tr>
<td style='width: 48%;'><div id='x3Grafico0' style='width: 100%;'></div></td>
<td style='width: 48%;'><div id='x3Grafico1' style='width: 100%;'></div></td>
</tr><tr>
<td style='width: 48%;'><div id='x3Grafico2' style='width: 100%;'></div></td>
<td style='width: 48%;'><div id='x3Grafico3' style='width: 100%;'></div></td>
</tr></tbody></table>";
        return $cad;
    } // eof ##################################################


    /**
     * Gráfico 4
     */
    public static function toChart4( $schema, $data, $periodo = null){
        $aux = '';        
        $cad = "<script type=\"text/javascript\">
      google.charts.load(\"current\", {packages:['bar']});";
        $vct[] = 'Total de Llamadas';
        $vct[] = 'Clientes Contactados';
        $vct[] = 'Encuestas Planificadas';
        $vct[] = 'Encuestas Efectivas';
        for( $i=0 ; $i < 4 ; $i++ ){
            if( count($data[$i]) > 0 ){
                $cad .= "google.charts.setOnLoadCallback(drawChart" .$i .");
      function drawChart" .$i ."(){
        var data" .$i ." = google.visualization.arrayToDataTable([
          ['Diana', '" .$vct[$i] ."'],";
            
                foreach( $data[$i] as $vt )
                    $cad .= "  ['" .$vt[0] ."', " .$vt[1] ."],";
            
                $cad .= " ]);
        var options" .$i ." = {
        chart: {
          title: '" .$vct[$i] ."'          
        },
        bars: 'horizontal'
       };
       var chart" .$i ." = new google.charts.Bar(document.getElementById('x3Grafico" .$i ."'));
        chart" .$i .".draw(data" .$i .", options" .$i .");
      }";
            }
        }
        
        $cad .= "</script>
<table style=\"border:none;width:100%;text-align:center;\"><tbody>
<tr><td style='width: 100%;'><div id='x3Grafico0' style='width: 100%;height:1000px;'></div></td></tr>
<tr><td style='width: 100%;'><div id='x3Grafico1' style='width: 100%;height:1000px;'></div></td></tr>
<tr><td style='width: 100%;'><div id='x3Grafico2' style='width: 100%;height:1000px;'></div></td></tr>
<tr><td style='width: 100%;'><div id='x3Grafico3' style='width: 100%;height:1000px;'></div></td></tr>
</tbody></table>";
        return $cad;
    } // eof ##################################################
    
    
    
    

   


    /**
     * Transfiere a HTML, versión 2
     */
    public static function toHtml2( $data, $style = null, $size = '0.8em' ){
        $c = '';
        $c1 = '#fffff6';
        $c2 = '#f6f6ff';
        $c3 = '#fff6ff';
        $c1 = '#aa0000';
        $c2 = '#00aaaa';
        $c3 = '#0000aa';
        $cad = '<div style="font-size: ' .$size .';"><table border="0" style="width: 950px;">'; // 60.0em - 65        
        $cad .= '<tbody>';
        $aux = $aux2 = '';
        $j = 0;
        $min = 150;
        $max = 250;
        foreach( $data as $v1 ){
            $i = 0;
            if( $j == 0 ){
                $cad .= '<tr>';
                foreach( $v1 as $v ){
                    $longitud = strlen($v);
                    $width = round( $longitud * 10 );
                    if( $width < $min ) $width = $min;
                    if( $width > $max ) $width = $max;
                    $cad .= '<td style="font-size:' .$size .'; background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700; min-width:' .$width .'px; ' .$style[$i] .';"> ' .strtoupper($v) .' </td>' ."\n";
                }
                $cad .= '</tr>';
            }else{
                if( $c == $c1 ) $c = $c2;
                else if( $c == $c2 ) $c = $c3;
                else $c = $c1;
                $cad .= '<tr>';
                foreach( $v1 as $v2 ){
                    if( $style != null && isset($style[$i]) ) $s = ' style="' .$style[$i] .'; border-bottom: solid 1px '.$c.';font-size:' .$size .'" ';
                    else $s = '';
                    $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                    $i++;
                } // for 
                $cad .= '</tr>';
            }
            $j++;
        } // for 
        return $cad .'</tbody></table></div>';
    } // eof ##################################################

    


    
    

    /**
     * Transfiere tabla a excel, versión 1
     */
    public static function tabla2xlsx( $objPHPExcel, $data ){
        $aux = $aux2 = '';
        $i = 0;
        foreach( $data as $v1 ){
            $i++;
            $j = 0;
            $ii = 0;
            foreach( $v1 as $v2 ){
                $j++;
                if( $i == 0 ){
                    $objRichText2 = new \PHPExcel_RichText();
                    $objRed = $objRichText2->createTextRun($v2);
                    $objRed->getFont()->setBold(true);
                    $objRed->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_DARKBLUE ) );              
                    $objPHPExcel->getActiveSheet()->setCellValue($this->letra($j).$i, $objRichText2);
                }else{
                    if( $ii == 0 ){
                        if( $aux == $v2 ){
                            $objPHPExcel->getActiveSheet()->setCellValue($this->letra($j).$i, '');
                            $aux2 = $v2;
                        }else
                            $objPHPExcel->getActiveSheet()->setCellValue($this->letra($j).$i, $v2);
                        $aux = $v2;
                    }else
                        $objPHPExcel->getActiveSheet()->setCellValue($this->letra($j).$i, $v2);
                }
                $ii++;
            } // for 
        } // for 
        $objPHPExcel->getActiveSheet()->getColumnDimension('a')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('b')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('c')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0);
        return $objPHPExcel;
    } // eof ##################################################










    /**
     * Transfiere tabla a excel, versión 2
     */
    public static function tabla2xlsx2( $objPHPExcel, $data ){
        $aux = $aux2 = '';
        $i = 0;
        foreach( $data as $v1 ){
            $i++;
            $j = 0;
            $ii = 0;
            foreach( $v1 as $v2 ){
                $j++;
                if( $i == 0 ){
                    $objRichText2 = new \PHPExcel_RichText();
                    $objRed = $objRichText2->createTextRun($v2);
                    $objRed->getFont()->setBold(true);
                    $objRed->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_DARKBLUE ) );              
                    $objPHPExcel->getActiveSheet()->setCellValue(Reporte::letra($j).$i, $objRichText2);
                }else
                    $objPHPExcel->getActiveSheet()->setCellValue(Reporte::letra($j).$i, $v2);
                $ii++;
            } // for 
        } // for 
        $objPHPExcel->getActiveSheet()->getColumnDimension('a')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('b')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('c')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0);
        return $objPHPExcel;
    } // eof ##################################################









    
    
    /**
     * Cambia números por letras 
     */
    public static function letra( $p ){
    
        $v = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o','16'=>'p','17'=>'q','18'=>'r','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w','24'=>'x','25'=>'y','26'=>'z','27'=>'aa','28'=>'ab','29'=>'ac','30'=>'ad','31'=>'ae','32'=>'af','33'=>'ag','34'=>'ah','35'=>'ai','36'=>'aj','37'=>'ak','38'=>'al','39'=>'am','40'=>'an','41'=>'ao','42'=>'ap','43'=>'aq','44'=>'ar','45'=>'as','46'=>'at','47'=>'au','48'=>'av','49'=>'aw','50'=>'ax','51'=>'ay','52'=>'az','53'=>'ba','54'=>'bb','55'=>'bc','56'=>'bd','57'=>'be','58'=>'bf','59'=>'bg','60'=>'bh','61'=>'bi','62'=>'bj','63'=>'bk','64'=>'bl','65'=>'bm','66'=>'bn','67'=>'bo','68'=>'bp','69'=>'bq','70'=>'br','71'=>'bs','72'=>'bt','73'=>'bu','74'=>'bv','75'=>'bw','76'=>'bx','77'=>'by','78'=>'bz','79'=>'ca','80'=>'cb','81'=>'cc','82'=>'cd','83'=>'ce','84'=>'cf','85'=>'cg','86'=>'ch','87'=>'ci','88'=>'cj','89'=>'ck','90'=>'cl','91'=>'cm','92'=>'cn','93'=>'co','94'=>'cp','95'=>'cq','96'=>'cr','97'=>'cs','98'=>'ct','99'=>'cu','100'=>'cv','101'=>'cw','102'=>'cx','103'=>'cy','104'=>'cz','105'=>'da','106'=>'db','107'=>'dc','108'=>'dd','109'=>'de','110'=>'df','111'=>'dg','112'=>'dh','113'=>'di','114'=>'dj','115'=>'dk','116'=>'dl','117'=>'dm','118'=>'dn','119'=>'do','120'=>'dp','121'=>'dq','122'=>'dr','123'=>'ds','124'=>'dt','125'=>'du','126'=>'dv','127'=>'dw','128'=>'dx','129'=>'dy','130'=>'dz');
        return $v[$p];
    } // eof #######################################################






    

    /**
     * Genera una Hoja de Calculo
     */    
    public static function hojaDeCalculo( $data, $onlyExcel = false ){
        if( true ){
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            if( ! defined('EOL') )
                define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');        
        }
        set_include_path('../vendor/PHPExcel/');
        include_once('PHPExcel/IOFactory.php');
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("callycall.com/crm")
                                                            ->setLastModifiedBy("callycall.com/crm")
                                                            ->setTitle("Office 2007 XLSX Test Document")
                                                            ->setSubject("Office 2007 XLSX Test Document")
                                                            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                                            ->setKeywords("office 2007 openxml php")
                                                            ->setCategory("crm");        
        
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel = self::tabla2xlsx2( $objPHPExcel, $data );
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');
        $objPHPExcel->setActiveSheetIndex(0);
        
        $nfile = 'reporte_' .date('Ymd_his') .'.xlsx';
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(Yii::getAlias('@webroot') .'/reportes/public/' .$nfile);

        if( $onlyExcel == true )
            return Yii::$app->params['baseUrl'] .'/reportes/public/' .$nfile;
        else
            return '<a href="' .Yii::$app->params['baseUrl'] .'/reportes/public/' .$nfile .'"><button class="btn btn-default" title="Descargar el reporte como Hoja de Calculo"> Hoja de Calculo </button></a>';
        
    } // eof 
    
    



    

    
    /**
     * Transfiere HTML para excel
     */
    public static function html2xlsx( $objPHPExcel, $cad ){
        $objAux = simplexml_load_string( $cad );
        $i = 0;
        foreach( $objAux->children() as $tr ){
            $i++;
            $j = 0;
            foreach( $tr->children() as $td ){
                $j++;
                $tipo = $td->getName(); // obtengo el nombre, para usar en el stilo
                if( $tipo == 'th' ){
                    $objRichText2 = new \PHPExcel_RichText();
                    $objRed = $objRichText2->createTextRun($td[0]);
                    $objRed->getFont()->setBold(true);
                    $objRed->getFont()->setColor( new \PHPExcel_Style_Color( \PHPExcel_Style_Color::COLOR_DARKBLUE ) );              
                    $objPHPExcel->getActiveSheet()->setCellValue(Reporte::letra($j).$i, $objRichText2);
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue(Reporte::letra($j).$i, $td[0]);
                }
            }
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('a')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('b')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('c')->setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0);
        return $objPHPExcel;
    } // eof ##################################################




  





    /**
     * Controles para filtrar reportes
     * @param string accion. es la accion del controlador, junto con los parametros ej. dc&x3inst=2
     */
    public static function filtro1( $subtitulo, $schema, $codInstrumento, $inst = null, $reporte = null, $accion = null, $vct = null ){
        $cad = '';
        $titulo = '';
        $desde            = Request::rq('x3desde');
        $hasta            = Request::rq('x3hasta');
        $tlo              = Request::rq('x3tlo');
        if( $inst == null ) 
            $inst         = (int)Request::rq('x3inst');
        if( $reporte == null )
            $reporte      = Request::rq('x3rpt');
        $data_tp          = Request::rq('x3data_tp');
        $fecha_tp         = Request::rq('x3fecha_tp');
        $id_dominio       = Request::rq('x3dom');
        $agente           = Request::rq('x3agente');
        $idTlo            = Request::rq('x3tlo');
        $dominioOagente   = Request::rq('x3dominioOagente');

        if( !is_null($inst) ){
            $sql = "select de from " .$schema .".instrumento where id='" .(int)$inst ."' and st = '1';";
            $obj = Aux::findBySql($sql)->all();
            if( count($obj) )
                $titulo = $obj[0]->de;
        }else $titulo = 'Todos los Instrumentos';
        
        $hoy = date('Y-m-d');

        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'resumen';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($id_dominio) ) $id_dominio = null;
        if( is_null($agente) ) $agente = null;        
        if( is_null($dominioOagente) ) $dominioOagente = null;  


        $cad .= '<div class="subtitulos">' .$titulo .'</div>';
        $cad .= '<div class="titulos">' .$subtitulo .'</div>';
        
        $cad .= '
  <script>
  $( function() {
    $( "#x3desde" ).datepicker({ dateFormat: \'yy-mm-dd\' });
    $( "#x3hasta" ).datepicker({ dateFormat: \'yy-mm-dd\' });
  } );
  </script>
';




        
        $cad .= '<form id="x3FormFiltro" name="x3FormFiltro" method="POST" class="form-inline" action="' .Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$schema .'/' .$codInstrumento .'/' .$accion .'">';

        $cad .= '<input type="hidden" id="cod_proyecto" name="cod_proyecto" value="' .$schema .'" />';
        $cad .= '<input type="hidden" id="x3inst" name="x3inst" value="' .(int)$inst .'" />';



        if( !is_null($vct) && isset($vct['data_tp']) && $vct['data_tp'] == true ){
            $cad .= '
 <div class="form-group">
<label for="data_tp">Tipo de Data:</label>
        <select id="x3data_tp" name="x3data_tp" class="form-control">';
                  if( $data_tp == 1 ){
                      $cad .= '<option value="1" selected="selected"> Efectiva </option><option value="0"> No Efectiva </option><option value="2"> Todo </option>';
                  }else if( $data_tp == 0 ){
                      $cad .= '<option value="1"> Efectiva </option><option value="0" selected="selected"> No Efectiva </option><option value="2"> Todo </option>';
                  }else 
                      $cad .= '<option value="1"> Efectiva </option><option value="0"> No Efectiva </option><option value="2" selected="selected"> Todo </option>';
                  $cad .= '</select> &nbsp; &nbsp; </div>';
        }
        
              
        
        if( !is_null($vct) && isset($vct['fecha_tp']) && $vct['fecha_tp'] == true ){
            $cad .= '<div class="form-group">
<label for="fecha_tp">Consultar por:</label>
        <select id="x3fecha_tp" name="x3fecha_tp" class="form-control">';
                  if( $fecha_tp == 1 ){
                      $cad .= '<option value="1" selected="selected"> Fecha de Encuesta </option><option value="0"> Fecha de Atención </option>';
                  }else{
                      $cad .= '<option value="1"> Fecha de Encuesta </option><option value="0" selected="selected"> Fecha de Atención </option>';
                  }
                  $cad .= '</select> &nbsp; &nbsp; </div>';
        }
        
        
        $cad .= '<div class="form-group">
<label for="x3desde">Fecha Desde:</label>
<input class="form-control" type="text" id="x3desde" name="x3desde" style="width:100px;" value="' .$desde .'"/> &nbsp; &nbsp; </div>

 <div class="form-group">
<label for="x3hasta">Fecha Hasta:</label>
<input class="form-control" type="text" id="x3hasta" name="x3hasta" style="width:100px;" value="' .$hasta .'"/> &nbsp; &nbsp; </div>


<br /><br />';



              if( !is_null($vct) && isset($vct['id_dominio']) && $vct['id_dominio'] == true ){
                  $cad .= '<div id="x3SelectDominio" class="form-group">';
                  $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3SelectDominio\', \'x3inst=' .$inst .'\', \'selectdominio\', \'' .$schema .'/' .$codInstrumento .'\' );" />';            
                  $cad .= '<label for="id_dominio">Zona:</label>
        <select id="x3dom" name="x3dom" class="form-control"  style="width:200px;">
</select> &nbsp; &nbsp; </div>';
              }

        
        
              if( !is_null($vct) && isset($vct['agente']) && $vct['agente'] == true )
                  $cad .= '<div class="form-group">
<label id="x3SelectAgente" for="agente">Oficina:</label>
        <select id="x3agente" name="x3agente" class="form-control"  style="width:500px;" ></select> &nbsp; &nbsp;</div>';


                        if( !is_null($vct) && isset($vct['tlo']) && $vct['tlo'] == true ){
                            $cad .= '<div class="form-group">
<label id="x3SelectTLO" for="agente">Teleoperador:</label>
        <select id="x3tlo" name="x3tlo" class="form-control"  style="width:500px;" >';
                                  $sql = "select distinct u.id, nombres, apellidos from a.usuario u inner join " .$schema .".llamada l on l.id_usuario=u.id where u.st = '1' order by nombres asc, apellidos asc;";
                                  $objsTLO = Aux::findBySql($sql)->all();
                                  foreach( $objsTLO as $tlo )
                                      $cad .= '<option value="'.$tlo->id.'"> ' .$tlo->nombres .' ' .$tlo->apellidos .' </option>';
                                  $cad .= '</select> &nbsp; &nbsp;</div>';
                        }


                        if( !is_null($vct) && isset($vct['dominioOagente']) && $vct['dominioOagente'] == true ){
                            $cad .= '<div class="form-group">
<label for="fecha_tp">Consultar por:</label>
        <select id="x3dominioOagente" name="x3dominioOagente" class="form-control">';
                                  if( $dominioOagente == 7 ){
                                      $cad .= '<option value="7" selected="selected"> Dominio </option><option value="2"> Agente </option>';
                                  }else{
                                      $cad .= '<option value="7"> Dominio </option><option value="2" selected="selected"> Agente </option>';
                                  }
                                  $cad .= '</select> &nbsp; &nbsp; </div>';
                        }


                  
                        $cad .= '<div class="form-group">
  <button type="submit" class="btn btn-primary">  Consultar  </button>
</div>
<hr />
';
    
                        $cad .= '</form>';


    
                        return $cad;
    
    } // eof ##################################################



    /**
     * Relaciona defierentes campos de diferentes tablas
     */
    public static function relacion( $codProyecto, $tabla1, $tabla2, $dominio = null ){
        /*
          1. cuota        id_instrumento  id_dominio  cod_dominio 
          2. llamada      id_usuario  id_prospecto id_tipificacion  observaciones  
          3. tipificacion
          4. prospecto    id_instrumento id_data tlo  id_tipificacion st 
          5. instrumento  st    
          6. estatus
          7. usuario
          8. dominio  
        */
        
        $tablas = array();
        $vctAux['cuota'] = ' ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.llamada.id_prospecto = ' .$codProyecto .'.cuota_prospecto.id_prospecto '; $tablas['cuota'] = array($codProyecto .'.cuota_prospecto');
        $vctAux['dominio'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.llamada.id_prospecto = ' .$codProyecto .'.prospecto.id '; $tablas['dominio'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.prospecto', $codProyecto .'.cuota');
        $vctAux['estatus'] = ' ' .$codProyecto .'.llamada.st = a.estatus.id ';
        $vctAux['instrumento'] = ' ' .$codProyecto .'.instrumento.id = ' .$codProyecto .'.prospecto.id_instrumento and ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.llamada.id_prospecto '; $tablas['instrumento'] = array($codProyecto .'.prospecto');
        $vctAux['prospecto'] = ' ' .$codProyecto .'.llamada.id_prospecto = ' .$codProyecto .'.prospecto.id '; 
        $vctAux['tipificacion'] = ' ' .$codProyecto .'.llamada.id_tipificacion = ' .$codProyecto .'.tipificacion.id ';
        $vctAux['usuario'] = ' ' .$codProyecto .'.llamada.id_usuario = a.usuario.id ';
        $inner['llamada'] = $vctAux; unset($vctAux);
        $tbs['llamada'] = $tablas; unset($tablas);
        
        $tablas = array();        
        $vctAux['dominio'] = ' ' .$codProyecto .'.cuota.id_dominio = ' .$codProyecto .'.dominio.id ';
        $vctAux['estatus'] = ' ' .$codProyecto .'.cuota.st = a.estatus.id ';
        $vctAux['instrumento'] = ' ' .$codProyecto .'.cuota.id_instrumento = ' .$codProyecto .'.instrumento.id ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.llamada.id_prospecto = ' .$codProyecto .'.cuota_prospecto.id_prospecto '; $tablas['llamada'] = array($codProyecto .'.cuota_prospecto');
        $vctAux['prospecto'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota '; $tablas['prospecto'] = array($codProyecto .'.cuota_prospecto');
        $vctAux['tipificacion'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.prospecto.id_tipificacion = ' .$codProyecto .'.tipificacion.id '; $tablas['tipificacion'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.prospecto');
        $vctAux['usuario'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.prospecto.tlo = a.usuario.id '; $tablas['usuario'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.prospecto');        
        $inner['cuota'] = $vctAux; unset($vctAux);
        $tbs['cuota'] = $tablas; unset($tablas);
        
        $tablas = array();
        $vctAux['cuota'] = ' ' .$codProyecto .'.cuota.id_dominio = ' .$codProyecto .'.dominio.id ';
        $vctAux['estatus'] = ' ' .$codProyecto .'.dominio.st = a.estatus.id ';
        $vctAux['instrumento'] = ' ' .$codProyecto .'.dominio.id_instrumento = ' .$codProyecto .'.instrumento.id ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.llamada.id_prospecto = ' .$codProyecto .'.prospecto.id '; $tablas['llamada'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.prospecto', $codProyecto .'.cuota');
        $vctAux['prospecto'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.cuota.id_dominio = ' .$codProyecto .'.dominio.id '; $tablas['prospecto'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.cuota');
        $vctAux['tipificacion'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.cuota.id_dominio = ' .$codProyecto .'.dominio.id and ' .$codProyecto .'.prospecto.id_tipificacion = ' .$codProyecto .'.tipificacion.id '; $tablas['tipificacion'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.prospecto', $codProyecto .'.cuota');
        $vctAux['usuario'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.cuota.id_dominio = ' .$codProyecto .'.dominio.id and ' .$codProyecto .'.prospecto.tlo = a.usuario.id '; $tablas['usuario'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.prospecto', $codProyecto .'.cuota');
        $inner['dominio'] = $vctAux; unset($vctAux);
        $tbs['dominio'] = $tablas; unset($tablas);
        
       
        $tablas = array();
        $vctAux['cuota'] = ' 1 = 2 ';
        $vctAux['dominio'] = ' 1 = 2 ';
        $vctAux['estatus'] = ' ' .$codProyecto .'.tipificacion.st = a.estatus.id ';
        $vctAux['instrumento'] = ' ' .$codProyecto .'.instrumento.id = ' .$codProyecto .'.tipificacion.id_instrumento ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.llamada.id_tipificacion = ' .$codProyecto .'.tipificacion.id ';
        $vctAux['prospecto'] = ' ' .$codProyecto .'.prospecto.id_tipificacion = ' .$codProyecto .'.tipificacion.id ';
        $vctAux['usuario'] = ' ' .$codProyecto .'.prospecto.id_tipificacion = ' .$codProyecto .'.tipificacion.id and a.usuario.id  = ' .$codProyecto .'.prospecto.tlo '; $tablas['usuario'] = array($codProyecto .'.prospecto');
        $inner['tipificacion'] = $vctAux; unset($vctAux);
        $tbs['tipificacion'] = $tablas; unset($tablas);

        $tablas = array();
        $vctAux['cuota'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota '; $tablas['cuota'] = array($codProyecto .'.cuota_prospecto');
        $vctAux['dominio'] = ' ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.cuota_prospecto.id_prospecto and ' .$codProyecto .'.cuota.id = ' .$codProyecto .'.cuota_prospecto.id_cuota and ' .$codProyecto .'.cuota.id_dominio = ' .$codProyecto .'.dominio.id '; $tablas['dominio'] = array($codProyecto .'.cuota_prospecto', $codProyecto .'.cuota');
        $vctAux['estatus'] = ' ' .$codProyecto .'.prospecto.st = a.estatus.id ';
        $vctAux['instrumento'] = ' ' .$codProyecto .'.prospecto.id_instrumento = ' .$codProyecto .'.instrumento.id ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.llamada.id_prospecto = ' .$codProyecto .'.prospecto.id ';
        $vctAux['tipificacion'] = ' ' .$codProyecto .'.prospecto.id_tipificacion = ' .$codProyecto .'.tipificacion.id ';
        $vctAux['usuario'] = ' ' .$codProyecto .'.prospecto.tlo = a.usuario.id ';
        $inner['prospecto'] = $vctAux; unset($vctAux);
        $tbs['prospecto'] = $tablas; unset($tablas);

        $tablas = array();
        $vctAux['cuota'] = ' ' .$codProyecto .'.cuota.id_instrumento = ' .$codProyecto .'.instrumento.id ';
        $vctAux['dominio'] = ' ' .$codProyecto .'.instrumento.id = ' .$codProyecto .'.prospecto.id_instrumento and ' .$codProyecto .'.dominio.cod = ' .$codProyecto .'.prospecto.' .$dominio .' '; $tablas['dominio'] = array($codProyecto .'.prospecto');
        $vctAux['tipificacion'] = ' ' .$codProyecto .'.instrumento.id = ' .$codProyecto .'.prospecto.id_instrumento and ' .$codProyecto .'.prospecto.id_tipificacion = ' .$codProyecto .'.tipificacion.id '; $tablas['tipificacion'] = array($codProyecto .'.prospecto');
        $vctAux['prospecto'] = ' ' .$codProyecto .'.instrumento.id = ' .$codProyecto .'.prospecto.id_instrumento ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.instrumento.id = ' .$codProyecto .'.prospecto.id_instrumento and ' .$codProyecto .'.prospecto.id = ' .$codProyecto .'.llamada.id_prospecto ';$tablas['llamada'] = array($codProyecto .'.prospecto');
        $vctAux['estatus'] = ' ' .$codProyecto .'.instrumento.st = a.estatus.id ';
        $inner['instrumento'] = $vctAux; unset($vctAux);
        $tbs['instrumento'] = $tablas; unset($tablas);

       
        $tablas = array();
        $vctAux['cuota'] = ' 1 = 2 ';
        $vctAux['dominio'] = ' ' .$codProyecto .'.prospecto.tlo = a.usuario.id and ' .$codProyecto .'.dominio.cod = ' .$codProyecto .'.prospecto.' .$dominio .' '; $tablas['dominio'] = array($codProyecto .'.prospecto');
        $vctAux['instrumento'] = ' ' .$codProyecto .'.prospecto.tlo = a.usuario.id and ' .$codProyecto .'.prospecto.id_instrumento = ' .$codProyecto .'.instrumento.id'; $tablas['instrumento'] = array($codProyecto .'.prospecto');
        $vctAux['tipificacion'] = ' ' .$codProyecto .'.prospecto.tlo = a.usuario.id and ' .$codProyecto .'.tipificacion.id = ' .$codProyecto .'.prospecto.id_tipificacion '; $tablas['tipificacion'] = array($codProyecto .'.prospecto');
        $vctAux['prospecto'] = ' ' .$codProyecto .'.prospecto.tlo = a.usuario.id ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.llamada.id_usuario = a.usuario.id ';
        $vctAux['estatus'] = ' ' .$codProyecto .'.usuario.st = a.estatus.id ';
        $inner['usuario'] = $vctAux; unset($vctAux);
        $tbs['usuario'] = $tablas; unset($tablas);
        
        $tablas = array();
        $vctAux['cuota'] = ' ' .$codProyecto .'.cuota.st = a.estatus.id ';
        $vctAux['dominio'] = ' ' .$codProyecto .'.dominio.st = a.estatus.id ';
        $vctAux['instrumento'] = ' ' .$codProyecto .'.instrumento.st = a.estatus.id ';
        $vctAux['llamada'] = ' ' .$codProyecto .'.llamada.st = a.estatus.id ';
        $vctAux['prospecto'] = ' ' .$codProyecto .'.prospecto.st = a.estatus.id ';
        $vctAux['tipificaion'] = ' ' .$codProyecto .'.tipificacion.st = a.estatus.id '; 
        $vctAux['usuario'] = ' ' .$codProyecto .'.usuario.st = a.estatus.id ';
        $inner['estatus'] = $vctAux; unset($vctAux);
        $tbs['estatus'] = $tablas; unset($tablas);

        $aux = array();
        if( isset($tbs[$tabla1][$tabla2]) ) $aux = $tbs[$tabla1][$tabla2];
        
        return array( $inner[$tabla1][$tabla2], $aux );
    }



    /**
     * Reportes no genericos
     */
    public static function reportNoGeneric( $cod_proyecto = null, $cod_reporte = null, $cod_instrumento = null, $id_instrumento = null, $onlyExcel = null ){
        $cad= '';
        switch( $cod_reporte ){
        case 'efectividad':
            $cad = Reporte::efectividad( $cod_proyecto, $cod_instrumento, $id_instrumento, $columna_fecha_ref = 'c0001', $columna_cod = 'c0001', $columna_ente = 'c0001', true); break;
        case 'supervisor':
            $cad = Reporte::supervisor( $cod_proyecto, $cod_instrumento, $id_instrumento, $columna_fecha_ref = 'c0001', $columna_cod = 'c0001', $columna_ente = 'c0001', true); break;
        case 'supervisor_detalle':
            $cad = Reporte::supervisorDetalle( $cod_proyecto, $cod_instrumento, $id_instrumento, $columna_fecha_ref = 'c0001', $columna_cod = 'c0001', $columna_ente = 'c0001', true); break;
        case 'efectividad_detalle':
            $cad = Reporte::efecividadDetalle( $cod_proyecto, $cod_instrumento, $id_instrumento, $columna_fecha_ref = 'c0001', $columna_cod = 'c0001', $columna_ente = 'c0001', true); break;
        case 'estadisticas':
            $cad = Reporte::supervisor( $cod_proyecto, $cod_instrumento, $id_instrumento, $columna_fecha_ref = 'c0001', $columna_cod = 'c0001', $columna_ente = 'c0001', true); break;
        case 'dc':
            $cad = Reporte::dc( $cod_proyecto, $cod_instrumento, $id_instrumento, true); break;
        case 'resumen':
        default:
            $cad = Reporte::$cod_reporte($cod_proyecto, $id_instrumento, $onlyExcel); break;
        }
        return $cad;
    } // eof 
    

    
    /**
     * Reportes genericos
     * @param string accion. es la accion del controlador, junto con los parametros ej. dc&x3inst=2
     */
    public static function reportGeneric( $codProyecto = null, $idReporte = null, $id_instrumento = null, $onlyExcel = null ){
        $cad = '';
        $titulo = '';
        $subtitulo = '';
        $accion = '';
        $vct = null;
        $hoy = date('Y-m-d');
        $data = null;
        
        
        // ###### PARAMETROS
        if( $codProyecto == null ) 
            $codProyecto  = Request::rq('x3proy');
        $desde            = Request::rq('x3desde');
        $hasta            = Request::rq('x3hasta');
        $tlo = (int)Request::rq('x3tlo');
        if( $idReporte ==  null )
            $idReporte = Request::rq('x3id');
        $idInstrumento         = (int)Request::rq('x3inst');
        $data_tp = Request::rq('x3data_tp');
        $fecha_tp = Request::rq('x3fecha_tp');
        $id_dominio = Request::rq('x3dom');
        $agente = Request::rq('x3agente');
        $dominioOagente   = Request::rq('x3dominioOagente');
        
        // ###### VALIDACIONES
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
       
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($id_dominio) ) $id_dominio = null;
        if( is_null($agente) ) $agente = null;
        if( is_null($dominioOagente) ) $dominioOagente = null;  
        if( $idInstrumento > 0 ){
            $sql = "select de from " .$codProyecto .".instrumento where id='" .$idInstrumento ."' and st = '1';";
            $obj = Aux::findBySql($sql)->one();
            $titulo = $obj->de;
        }else $titulo = 'Todos los Instrumentos';
        
        
        $tabla = array();
        $dominio = null;
        
        
        $auxSQLselect = 'select ';
        $auxSQLfrom = ' from ';
        $auxSQLwhere = ' where 1=1 ';
        $ic = 0;
        $sql = "select de, tabla, campo from a.reporte_campo where id_reporte='" .$idReporte ."' and st = '1' order by orden asc, id asc;";
        $objs = Aux::findBySql($sql)->all();
        foreach( $objs as $obj ){
            $cabecera[] = $obj->de;
            $vCampos[] = $obj->campo;
            $auxSQLselect .= $codProyecto .'.' .$obj->tabla .'.' .$obj->campo .', ';
            
            $tabla[] = $codProyecto .'.' .$obj->tabla;
            if( $ic > 0 ){
                $rel = self::relacion( $codProyecto, $tabla1, $obj->tabla, $dominio );
                $auxSQLwhere .= ' and (' .$rel[0] .') ';
                foreach( $rel[1] as $tb )
                    $tabla[] = $tb;
            }
            $tabla1 = $obj->tabla;
            $ic++;
        }
        $data[] = $cabecera;
        $auxSQLselect = substr($auxSQLselect,0,-2) .' ';
        
        $tabla = array_unique($tabla);
        $auxSQLfrom = ' from ' .implode(', ', $tabla) .' ';
        
        $auxSQL = $auxSQLselect .$auxSQLfrom .$auxSQLwhere;
        
        $obj1s = Aux::findBySql($auxSQL)->all();
        foreach( $obj1s as $obj1 ){
            foreach( $vCampos as $reg )
                $registro[] = $obj1->$reg;
            $data[] = $registro;
            unset($registro);
        }
        
        
        if( $onlyExcel == null ){
            // //////////////////////////////////////////////
            
            $style[] = 'padding:0.3em;';
            $style[] = 'text-align:center; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            $style[] = 'text-align:right; padding:0.3em;';
            
            $cad .= '<div class="subtitulos">' .$titulo .'</div>';
            $cad .= '<div class="titulos">' .$subtitulo .'</div>';
            
            $cad .= '<script>
  $( function() {
    $( "#x3desde" ).datepicker({ dateFormat: \'yy-mm-dd\' });
    $( "#x3hasta" ).datepicker({ dateFormat: \'yy-mm-dd\' });
  } );
  </script>';
            
            $cad .= '<form id="x3FormFiltro" name="x3FormFiltro" method="POST" class="form-inline" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/report&' .$accion .'">';
            $cad .= '<input type="hidden" id="x3proy" name="x3proy" value="' .$codProyecto .'" />';
            $cad .= '<input type="hidden" id="x3inst" name="x3inst" value="' .(int)$idInstrumento .'" />';
            $cad .= '<input type="hidden" id="x3id" name="x3id" value="' .(int)$idReporte .'" />';
            
            
            if( !is_null($vct) && isset($vct['data_tp']) && $vct['data_tp'] == true ){
                $cad .= ' <div class="form-group"><label for="data_tp">Tipo de Data:</label><select id="x3data_tp" name="x3data_tp" class="form-control">';
                if( $data_tp == 1 ){
                    $cad .= '<option value="1" selected="selected"> Efectiva </option><option value="0"> No Efectiva </option><option value="2"> Todo </option>';
                }else if( $data_tp == 0 ){
                    $cad .= '<option value="1"> Efectiva </option><option value="0" selected="selected"> No Efectiva </option><option value="2"> Todo </option>';
                }else 
                    $cad .= '<option value="1"> Efectiva </option><option value="0"> No Efectiva </option><option value="2" selected="selected"> Todo </option>';
                $cad .= '</select> &nbsp; &nbsp; </div>';
            }
            
            if( !is_null($vct) && isset($vct['fecha_tp']) && $vct['fecha_tp'] == true ){
                $cad .= '<div class="form-group"><label for="fecha_tp">Consultar por:</label><select id="x3fecha_tp" name="x3fecha_tp" class="form-control">';
                if( $fecha_tp == 1 ){
                    $cad .= '<option value="1" selected="selected"> Fecha de Encuesta </option><option value="0"> Fecha de Atención </option>';
                }else{
                    $cad .= '<option value="1"> Fecha de Encuesta </option><option value="0" selected="selected"> Fecha de Atención </option>';
                }
                $cad .= '</select> &nbsp; &nbsp; </div>';
            }
            
            $cad .= '<div class="form-group"><label for="x3desde">Fecha Desde:</label><input class="form-control" type="text" id="x3desde" name="x3desde" style="width:100px;" value="' .$desde .'"/> &nbsp; &nbsp; </div>';

            $cad .= ' <div class="form-group"><label for="x3hasta">Fecha Hasta:</label><input class="form-control" type="text" id="x3hasta" name="x3hasta" style="width:100px;" value="' .$hasta .'"/> &nbsp; &nbsp; </div>';
            
            $cad .= '<br /><br />';
            
            if( !is_null($vct) && isset($vct['id_dominio']) && $vct['id_dominio'] == true ){
                $cad .= '<div id="x3SelectDominio" class="form-group">';
                $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3SelectDominio\', \'x3inst=' .$idInstrumento .'\', \'selectdominio\', \'proyecto/' .$codProyecto .'/' .$codInstrumento .'\' );" />';            
                $cad .= '<label for="id_dominio">Zona:</label>
        <select id="x3dom" name="x3dom" class="form-control"  style="width:200px;">
</select> &nbsp; &nbsp; </div>';
            }
            
            if( !is_null($vct) && isset($vct['agente']) && $vct['agente'] == true )
                $cad .= '<div class="form-group"><label id="x3SelectAgente" for="agente">Oficina:</label><select id="x3agente" name="x3agente" class="form-control"  style="width:500px;" ></select> &nbsp; &nbsp;</div>';
            
            if( !is_null($vct) && isset($vct['tlo']) && $vct['tlo'] == true ){
                $cad .= '<div class="form-group"><label id="x3SelectTLO" for="agente">Teleoperador:</label><select id="x3tlo" name="x3tlo" class="form-control"  style="width:500px;" >';
                $sql = "select distinct u.id, nombres, apellidos from a.usuario u inner join " .$codProyecto .".llamada l on l.id_usuario=u.id where u.st = '1' order by nombres asc, apellidos asc;";
                $objsTLO = Aux::findBySql($sql)->all();
                foreach( $objsTLO as $tlo )
                    $cad .= '<option value="'.$tlo->id.'"> ' .$tlo->nombres .' ' .$tlo->apellidos .' </option>';
                $cad .= '</select> &nbsp; &nbsp;</div>';
            }
            
            if( !is_null($vct) && isset($vct['dominioOagente']) && $vct['dominioOagente'] == true ){
                $cad .= '<div class="form-group"><label for="fecha_tp">Consultar por:</label><select id="x3dominioOagente" name="x3dominioOagente" class="form-control">';
                if( $dominioOagente == 7 ){
                    $cad .= '<option value="7" selected="selected"> Dominio </option><option value="2"> Agente </option>';
                }else{
                    $cad .= '<option value="7"> Dominio </option><option value="2" selected="selected"> Agente </option>';
                }
                $cad .= '</select> &nbsp; &nbsp; </div>';
            }
            
            $cad .= '<div class="form-group">
  <button type="submit" class="btn btn-primary">  Consultar  </button>
</div><hr />';
            $cad .= '</form>';

            $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
            $cad .= Listado::listado2( $data, '', $style );
            $cad .= '<br /><hr /><br /><br />';            
        }else{
            $cad .= Reporte::hojaDeCalculo( $data, true ) .'<br /><br />';
            
        } // es solo excel
        
        

        return $cad;
    } // eof 
    
    
    
    
    
    
    
    // lista de dominios, para select, NO usado en contacto1
    public function actionSelectdominio_DEPRECATE(){
        
        Yii::$app->layout = 'embebido';
        $op = Request::rq('x3op');
        $inst = Request::rq('x3inst');
        $cad = '';

        $cad .= '<label for="id_dominio">Zona:</label>';
        
        $cad .= '<select class="form-control" id="id_dominio" name="id_dominio" onchange="alert(\'Pendiente\');"  style="width:60%;">';
        $cad .= '<option value=""> --- Seleccione --- </option>';
        if( $inst != null ){
            $sql = "select id,de,cod from " .$schema .".dominio where st in (1) and id_instrumento='" .$inst ."' order by cod ASC;";
            $regs = Aux::findBySql($sql)->all();
            foreach( $regs as $v )
                $cad .= '<option value="' .$v->id .'">' .$v->cod .' - ' .$v->de .'</option>';
        }
        echo $cad .'</select> &nbsp; &nbsp; ';
    } // eof #######################################################
    


    /**
     * Cuenta las respuestas
     */
    public static function contarRespuestas( $schema, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $ini = null, $fin = null, $idDominio = '', $ente = '', $fecha_tp = '1', $pregunta = null, $valor = null ){
        if( $ini == null ) $ini = date('Y-m-d');
        if( $fin == null ) $fin = $ini;
        if( $fecha_tp == '1' ) // fecha_encuesta
            $fecha = "reg >= '$ini 01:01:01'::timestamp and reg <= '$fin 23:59:59'::timestamp ";
        else
            $fecha = " $columna_fecha_ref >= '$ini' and $columna_fecha_ref <= '$fin' ";
        
        $sql = "select count(*) as count from $schema.prospecto where id_instrumento='" .$idInstrumento ."' and " .$fecha ." and st = '5' ";
        
        if( $idDominio != '' )
            $sql .= " and $columna_cod = '$idDominio' ";                   
        if( $ente != '' )
            $sql .= " and $columna_ente = '$ente'";

        if( !is_null($pregunta) ){
            if( $valor !== null )
                $sql .= " and $pregunta = '$valor' ";   
            else
                $sql .= " and pg_column_size($pregunta) > 0 ";
         
        }

        $obj = Aux::findBySql($sql)->one();
        
        return $obj->count;

    } // eof ####################################################################
    

    /**
     * Transforma un número a una escala
     */
    public static function int2Escala(){
        $aux[10] = 'Totalmente Satisfecho';
        $aux[9] = 'Muy Satisfecho';
        $aux[8] = 'Bastante Satisfecho';
        $aux[7] = 'Algo Satisfecho';
        $aux[6] = 'Indiferente';
        $aux[5] = 'Indiferente';
        $aux[4] = 'Algo insatisfecho';
        $aux[3] = 'Bastante insatisfecho';
        $aux[2] = 'Muy insatisfecho';
        $aux[1] = 'Totalmente insatisfecho';
        $aux[99] = 'No Sabe / No Contesta';
        $aux['Ns/NC'] = 'No Sabe / No Contesta';
        $aux['No Sabe / No Contesta'] = 'No Sabe / No Contesta';
        
        return $aux;
    } // eof 



    /**
     * Valores de las entradas
     */
    public static function entradaValor( $schema, $idInstrumento ){
        $aux = array();
        $sql = "select e.codigo, et.valor, et.tp, c.columna 
from " .$schema .".entrada e 
inner join " .$schema .".cabecera c on e.codigo = c.de and c.id_instrumento=e.id_instrumento and c.st='1'  
left join " .$schema .".entrada_op et on et.id_entrada=e.id and et.st='1' and substr(et.valor, 1, 1) != '_' 

where e.st = '1' and e.id_instrumento='" .$idInstrumento ."' and e.id_pregunta_tp in (2,3,4) order by e.orden ASC, et.orden asc;";
        $objs = Aux::findBySql($sql)->all();
        foreach( $objs as $reg )
            $aux[] = array( $reg->codigo, $reg->valor, $reg->tp, $reg->columna );
        return $aux;
    } // eof #######################################################
    
    /**
     * Retorna un vector que relaciona el código con la pregunta
     */
    public static function codigo2Pregunta( $schema, $idInstrumento ){
        $sql = "select e.codigo, e.de from " .$schema .".entrada e where e.st = '1' and e.id_instrumento='" .$idInstrumento ."' and e.id_pregunta_tp in (2,3,4) order by e.orden ASC;";
        $objs = Aux::findBySql($sql)->all();
        foreach( $objs as $reg ) $aux[$reg->codigo] = $reg->de;
        return $aux;
    } // eof #######################################################
    
    /**
     * Retorna un vector que relaciona un valor con la pregunta
     */
    public static function valor2Respuesta( $schema, $idInstrumento ){
        $sql = "select e.codigo, et.valor, et.de from " .$schema .".entrada e left join " .$schema .".entrada_op et on et.id_entrada=e.id and et.st='1' and substr(et.valor, 1, 1) != '_' where e.st = '1' and e.id_instrumento='" .$idInstrumento ."' and e.id_pregunta_tp in (2,3,4) order by e.orden ASC;";
        $objs = Aux::findBySql($sql)->all();
        foreach( $objs as $reg ) $aux[$reg->codigo][$reg->valor] = $reg->de;        
        return $aux;
    } // eof #######################################################

    




    
    /**
     * Reporte de estadisticas
     */
    public static function estadisticas( $schema, $codInstrumento, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $onlyExcel = false ){
        set_time_limit(5000); // Limite de tiempo
        $data = array();
        $cad = '';






        if( $schema != '' ) $cod_proyecto = $schema;
        else{
            $schema = $cod_proyecto = Request::rq('x3proy');
            if( $cod_proyecto.'' == '' ) $schema = $cod_proyecto = Request::rq('cod_proyecto');
        }
        if( $idInstrumento > 0 )
            $inst = $id_instrumento = $idInstrumento;
        else{
            $idInstrumento = $inst = $id_instrumento = (int)Request::rq('x3inst');
            if( $id_instrumento == 0 ) $inst = $id_instrumento = Request::rq('id_instrumento');
        }
        
        $ax = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."';")->one();
        $codInstrumento = $ax->codigo;

        
        $vctZ['id_dominio'] = true;
        $vctZ['agente'] = true;
        $vctZ['fecha_tp'] = true;
        
        // DEL FILTRO
        if( $onlyExcel == false )
            $cad .= self::filtro1( 'Estadísticas de Encuestas', $schema, $codInstrumento, $idInstrumento, 'estadisticas', 'estadisticas', $vctZ );
        $desde      = Request::rq('x3desde');
        $hasta      = Request::rq('x3hasta');
        $tlo        = Request::rq('x3tlo');
        $reporte    = Request::rq('x3rpt');
        $data_tp    = Request::rq('x3data_tp');
        $fecha_tp   = Request::rq('x3fecha_tp');
        $idDominio = Request::rq('x3dom');
        $agente     = Request::rq('x3agente');
        $hoy = date('Y-m-d');
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'estadisticas';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($idDominio) ) $idDominio = null;
        if( is_null($agente) ) $agente = null;    

        
        
        $vct = array();
        $select = '';
        $filtro = '';



        // CABECERA
        $vct[] = 'TOTAL DE ENCUESTAS';
        $vct[] = 'PREGUNTA';
        $vct[] = 'RESPUESTA';
        $vct[] = 'TOTAL';
        $vct[] = '%';
        $vct[] = 'TOP THREE BOX';
        $vct[] = 'BOTTOM TWO BOX';
        $style[] = 'text-align:center; padding:0.3em;';
        $style[] = 'text-align:center; padding:0.3em;';
        $style[] = 'text-align:center; padding:0.3em;';
        $style[] = 'text-align:center; padding:0.3em;';
        $style[] = 'text-align:center; padding:0.3em;';
        $style[] = 'text-align:center; padding:0.3em;';
        $style[] = 'text-align:center; padding:0.3em;';
        




        $cadAux = "<table class='items' width='100%' style='font-size:0.75em;'><tr><td>
                    <table align='center' id='tabla_inicio' class='table table-bordered' style='width: 100%' ><thead>
                        <tr style='color:#ffffff; background-color: #005382;'>
                        	<th align='center'> TOTAL DE ENCUESTAS </th>
                        	<th align='center' width='300'> PREGUNTA  </th>
                        	<th align='center' width='750'> RESPUESTA </th>
                        	<th align='center'> TOTAL </th>
                        	<th align='center'> &#037; </th>
                        	<th align='center'> TOP THREE BOX </th>
                            <th align='center'> BOTTOM TWO BOX </th>
                            <th align='center'> &nbsp; </th>
                        </tr></thead>";



         
        $total_enc = self::contarRespuestas( $schema, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $desde, $hasta, $idDominio, $agente, $fecha_tp );
      
        $vct3 = array();
        $vct99 = array();
        $vct1 = self::entradaValor( $schema, $idInstrumento );
        $escala = self::int2Escala();
        
            
            
        $sg1 = 0;
        $sg2 = 0;
        
        foreach( $vct1 as $vct ){ // Contar total para pregunta
            $vct2[$vct[0] .'_' .$vct[1]] = self::contarRespuestas( $schema, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $desde, $hasta, $idDominio, $agente, $fecha_tp, $vct[3], $vct[1]);
            if( !isset($vct3['t_' .$vct[0]]) )
                $vct3['t_' .$vct[0]] = 0;
            if( $vct[1] == '99' )
                $vct99[$vct[0]] = $vct2[$vct[0] .'_' .$vct[1]];
            $vct3['t_' .$vct[0]] += $vct2[$vct[0] .'_' .$vct[1]];
            
            // no entra
            if( $vct[0] == 'sg1' && $vct[0] == '99' )
                $sg1 = $vct2[$vct[0] .'_' .$vct[1]] * $vct[1];
            if( $vct[0] == 'sg2' && $vct[0] == '99' )
                $sg2 = $vct2[$vct[0] .'_' .$vct[1]] * $vct[1];
        } // for
        
        
        if( $total_enc > 0 ){
           
            $seudoAux = '';
            
            $vct5 = self::codigo2Pregunta( $schema, $idInstrumento );
            $vct4 = self::valor2Respuesta( $schema, $idInstrumento );
            $blink = " style='background-color: #fcfcff;' vertical-align:middle;"; //' class="gris" ';
            $cl = $blink;
            foreach( $vct1 as $vct ){
                if( $vct[0] != $seudoAux ){
                    $cadAux .= '<tr><td colspan="8"><hr /></td></tr>';    
                    if( $cl == '' )
                        $cl = $blink;
                    else
                        $cl = '';
                }
                $aux01 = $vct3['t_' .$vct[0]];
                $aux02 = $vct4[$vct[0]][$vct[1]];
                $aux03 = $vct2[$vct[0] .'_' .$vct[1]];
                
                $aux04 = $vct5[$vct[0]];
                
                if( $vct[0] != $seudoAux ){
                    // TD 1, 2: pregunta ,3: respuesta
                    $cadAux .= "<tr" .$cl .">
                            <td" .$cl ." align='center'> $aux01 </td>
                            <td valig='middle'> " .strtoupper($vct[0]) ."." .$aux04 ."</td>
                            <td><table width='100%'><tbody>";
                }else{
                    $cadAux .= "<tr" .$cl .">
                            <td align='center'> &nbsp; </td>
                            <td> &nbsp;</td>
                            <td><table width='100%'><tbody>";
                }
                
                $es_escala = false;
                if( $vct[2] == '1' )
                    $es_escala = true;
                
                if( $es_escala == true ){
                    if( $aux02 == 1 ){
                        $t3b = $b2b = 0;
                        if( $vct3['t_' .$vct[0]] > 0 ){
                            $t3b = (($vct2[$vct[0] .'_10'] * 100)/$vct3['t_' .$vct[0]])
                                 + (($vct2[$vct[0] .'_9'] * 100)/$vct3['t_' .$vct[0]])
                                 + (($vct2[$vct[0] .'_8'] * 100)/$vct3['t_' .$vct[0]]);
                                
                            $b2b = (($vct2[$vct[0] .'_1'] * 100)/$vct3['t_' .$vct[0]])
                                 + (($vct2[$vct[0] .'_2'] * 100)/$vct3['t_' .$vct[0]]);
                        }
                        
                        $xx = 0;
                        $num = ($vct2[$vct[0] .'_1']*1)
                             + ($vct2[$vct[0] .'_2']*2)
                             + ($vct2[$vct[0] .'_3']*3)
                             + ($vct2[$vct[0] .'_4']*4)
                             + ($vct2[$vct[0] .'_5']*5)
                             + ($vct2[$vct[0] .'_6']*6)
                             + ($vct2[$vct[0] .'_7']*7)
                             + ($vct2[$vct[0] .'_8']*8)
                             + ($vct2[$vct[0] .'_9']*9)
                             + ($vct2[$vct[0] .'_10']*10);
                        $den = ($vct3['t_' .$vct[0]] - $vct2[$vct[0] .'_99']);
                        if( $den != 0 )
                            $xx = $num / $den;                           
                    }
                    
                    
                    
                   
                    if( (int)$aux02 == 'No Sabe / No Contesta' )
                        $cadAux .= "<tr" .$cl ."><td valign=\"middle\"> 99. $escala[$aux02] </td></tr>";
                    else
                        $cadAux .= "<tr" .$cl ."><td valign=\"middle\"> $aux02 . $escala[$aux02] </td></tr>";
                    
                    
                }else $cadAux .= "<tr" .$cl ."><td valign=\"middle\" style=\"vertical-align::middle\"> $aux02 </td></tr>";
                
                
                
                

                
                
                    
                if( $vct[0] != $seudoAux || 1 ) // TD 4: total
                    $cadAux .= '</tbody></table></td><td><table width="100%"><tbody>';
                $cadAux .= "<tr" .$cl ."><td style='text-align:right;'> $aux03 </td></tr>";
                if( $vct[0] != $seudoAux || 1 )
                    $cadAux .= "</tbody></table></td>";
                
                
                
                
                
                if( $vct[0] != $seudoAux || 1 ) // TD 5: Porcentaje
                    $cadAux .= "<td class='gri'><table width='100%'><tbody>";
                if( $aux01 != 0 )
                    $cadAux .= '<tr' .$cl .'><td style="text-align:right;">' .number_format(@(($aux03*100)/$vct3['t_' .$vct[0]]), 2, '.', ',') .'</td></tr>';
                else
                    $cadAux .= '<tr' .$cl .'><td style="text-align:right;">0</td></tr>';
                if( $vct[0] != $seudoAux || 1 )
                    $cadAux .= "</tbody></table></td>";

                
                
                
                
                if( $vct[0] != $seudoAux || 1 ){
                    // TD t3B
                    if( $es_escala == true && isset($t3b) && $aux02 == 1 )
                        $cadAux .= "<td class='gri' rowspan='11' title='T3B' style='text-align:right;'>" .number_format(@($t3b), 2, '.', ',') ."</td>";

                    
                    
                    // TD t2B
                    if( $es_escala == true && isset($b2b) &&  $aux02 == 1 )
                        $cadAux .= "<td class='gri' rowspan='11' title='T2B' style='text-align:right;'>" .number_format(@($b2b), 2, '.', ',') ."</td>";

                    
                    
                    
                    if( $es_escala == true && isset($xx) &&  $aux02 == 1 )
                        $cadAux .= "<td class='gri' rowspan='11' title='' style='text-align:right;'>" .number_format(@($xx), 2, '.', ',') ."</td>";
                    
                    
                    $cadAux .= "</tr>";
                }
                
                
                
                
                
                $seudoAux = $vct[0];    
            } // for
            $cadAux .= "</tbody>";


                


            $IST = '';
            $pT3B = '';
            $FCR = '';
            $pB2B = '';

            $top_sg1 = 0;



            $p2 = $pp2 = 0;
            $den1 = $vct3['t_sg1'] - $vct2['sg1_99'];
            $den2 = $vct3['t_sg2'] - $vct2['sg2_99'];
            if( $den1 > 0 ){
                $p2 = ($vct2['sg1_1'] * 1)
                    + ($vct2['sg1_2'] * 2)
                    + ($vct2['sg1_3'] * 3)
                    + ($vct2['sg1_4'] * 4)
                    + ($vct2['sg1_5'] * 5)
                    + ($vct2['sg1_6'] * 6)
                    + ($vct2['sg1_7'] * 7)
                    + ($vct2['sg1_8'] * 8)
                    + ($vct2['sg1_9'] * 9)
                    + ($vct2['sg1_10'] * 10);
                $p2 = $p2 / $den1;


                $pT3B = (($vct2['sg1_8'] + $vct2['sg1_9'] + $vct2['sg1_10'])/$den1) * 100;
                $pT3B = number_format($pT3B, 2, '.', ',');
                    
                $pB2B = (($vct2['sg1_1'] + $vct2['sg1_2'])/$den1) * 100;
                $pB2B = number_format($pB2B, 2, '.', ',');                    
            }
            if( $den2 > 0 ){
                $pp2 = ($vct2['sg2_1'] * 1)
                     + ($vct2['sg2_2'] * 2)
                     + ($vct2['sg2_3'] * 3)
                     + ($vct2['sg2_4'] * 4)
                     + ($vct2['sg2_5'] * 5)
                     + ($vct2['sg2_6'] * 6)
                     + ($vct2['sg2_7'] * 7)
                     + ($vct2['sg2_8'] * 8)
                     + ($vct2['sg2_9'] * 9)
                     + ($vct2['sg2_10'] * 10);
                $pp2 = $pp2 / $den2;
            }


            if( $den1 > 0 && $den2 > 0 )
                $IST = number_format(($p2 + $pp2)/2, 2, '.', ',');



                

            //$FCR = '';
            $p3 = $pp3 = 0;
            if( $vct3['t_res1'] != 0 )
                $p3 = (($vct2['res1_1']*100)/$vct3['t_res1']);   
            if( $vct3['t_res2'] != 0 )
                $pp3 = (($vct2['res2_1']*100)/$vct3['t_res2']);
            $FCR .= number_format((($p3*$pp3)/100), 2, '.', ',');
                



                
  



            $p = $pp = 0;
            if( $vct3['t_sg1'] - $vct99['sg1'] )
                $p = $sg1/($vct3['t_sg1'] - $vct99['sg1']);
            if( $vct3['t_sg2'] - $vct99['sg2'] )
                $pp = $sg2/($vct3['t_sg2'] - $vct99['sg2']);
            $IST = number_format(($p + $pp)/2, 2, '.', ',');



	
            if( $vct3['t_sg1'] != 0 )
                $top_sg1 = (($vct2['sg1_8'] + $vct2['sg1_9'] + $vct2['sg1_10'])/($vct3['t_sg1'] - $vct99['sg1']))*100;
            else
                $top_sg1 = 0;
                
            

            $p = $pp = 0;
            if( $vct3['t_res1'] != 0 )
                $p = (($vct2['res1_1']*100)/$vct3['t_res1']);   
            if( $vct3['t_res2'] != 0 )
                $pp = (($vct2['res2_1']*100)/$vct3['t_res2']);
            $FCR = number_format((($p*$pp)/100), 2, '.', ',');                  


            $cad .= $cadAux;
                
            $cad .= '<tfoot>';
            $cad .= '<tr><td></td><td> IST            </td><td style="text-align:right;">' .$IST .'</td></tr>';
            $cad .= '<tr><td></td><td> Promedio T3B   </td><td style="text-align:right;">' .$pT3B .'</td></tr>';
            $cad .= '<tr><td></td><td> Calculo de FCR </td><td style="text-align:right;">' .$FCR .'</td></tr>';
            $cad .= '<tr><td></td><td> Promedio B2B   </td><td style="text-align:right;">' .$pB2B .'</td></tr>';
            $cad .= '</tfoot>';
                      
            $cad .= '</table>';
  
        }
    






        
  

        if( $onlyExcel == false ){
            // SALIDA
            $cad .= '' .self::toHtml2( $data, $style, '0.4em' ) .'';
            $cad .= '<br /><hr />';
            $cad .= '<br /><hr /><br /><br />';
        }else
            $cad = Reporte::hojaDeCalculo( $data, true );
        
        return $cad;
    } // eof ##################################################  estadisticas







    /**
     * Reporte de efectividad
     */
    public static function efectividad( $schema, $codInstrumento, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $onlyExcel = false ){
        set_time_limit(5000); // Limite de tiempo
        $cad = '';
        $data = array();
        $data0 = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $datas = array();
        $cad = '';

        if( $schema != '' ){
            $cod_proyecto = $schema;
        }else{
            $schema = $cod_proyecto = Request::rq('x3proy');
            if( $cod_proyecto.'' == '' ) $schema = $cod_proyecto = Request::rq('cod_proyecto');
        }

        if( (int)$idInstrumento > 0 ){
            $inst = $id_instrumento = $idInstrumento;
        }else{
            $idInstrumento = $inst = $id_instrumento = (int)Request::rq('x3inst');
            if( $id_instrumento == 0 ) $inst = $id_instrumento = Request::rq('id_instrumento');
        }
        
        $ax = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."';")->one();
        $codInstrumento = $ax->codigo;

        
        // DEL FILTRO
        if( $onlyExcel == false )
            $cad .= self::filtro1( 'Reporte de Efectividad', $schema, $codInstrumento, $idInstrumento, 'efectividad', 'efectividad' );
        
        $desde      = Request::rq('x3desde');
        $hasta      = Request::rq('x3hasta');
        $tlo        = Request::rq('x3tlo');
        $reporte    = Request::rq('x3rpt');
        $data_tp    = Request::rq('x3data_tp');
        $fecha_tp   = Request::rq('x3fecha_tp');
        $idDominio = Request::rq('x3dom');
        $agente     = Request::rq('x3agente');
        $hoy = date('Y-m-d');
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'efectividad';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($idDominio) ) $idDominio = null;
        if( is_null($agente) ) $agente = null;    
        
        
        
        // ################### CABECERA
        $vct[] = ''; $style[] = 'padding:0.3em; width: 30.0em;';
        $vct[] = 'Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Llamadas Realizadas
        $vct[] = 'Contactados'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = '% Contactabilidad'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas        
        $vct[] = 'Enc. Planificadas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = 'Efectivas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = 'Efectividad de Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = 'Efectividad de Contactos'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = '% de Cumplimiento'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $data[] = $vct;
        
        $t_llamadas = $t_contactados = $t_planificado = $t_efectivas = 0;
        
        if( $desde != $hasta )
            $periodo = $desde .' - ' .$hasta;
        else
            $periodo = $desde;
        
        $sql1 = "select id,de from " .$schema .".instrumento where st='1';";
        $objs1 = Aux::findBySql($sql1)->all();
        foreach( $objs1 as $reg1 ){ // ######################## CADA INSTRUMENTO
            $vct1[0] = $reg1->de;    
            $sql2 = "select 
sum(llamadas) as llamadas, 
sum(contactados) as contactados, 
sum(planificado) as planificado, 
sum(efectivas) as efectivas 
from ( 
select count(l.*) as llamadas, 0 as contactados, 0 as planificado, 0 as efectivas 
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on l.id_prospecto = p.id 
where l.reg <= '" .$hasta ." 23:59:59'::timestamp and l.reg >= '" .$desde ." 01:01:01'::timestamp and l.st!= '0' and l.st!= '6'  and p.id_instrumento ='" .$reg1->id ."' 

union 
select 0 as llamadas, count(l.*) as contactados, 0 as planificado, 0 as efectivas
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on l.id_prospecto = p.id 
where l.reg <= '" .$hasta ." 23:59:59'::timestamp and l.reg >= '" .$desde ." 01:01:01'::timestamp and l.id_tipificacion in ('1','15') and p.id_instrumento ='" .$reg1->id ."' 

union 
select 0 as llamadas, 0 as contactados, sum(c.cuota) as planificado, 0 as efectivo 
from " .$schema .".cuota c 
where c.reg = c.reg and c.id_instrumento ='" .$reg1->id ."' and c.st in (1,2)  
and c.reg <= '" .$hasta ." 23:59:59'::timestamp and c.reg >= '" .$desde ." 01:01:01'::timestamp

union
select 0 as llamadas, 0 as contactados, 0 as planificado, sum(LEAST(c.cuota,c.conteo)) as efectivo 
from " .$schema .".cuota c 
where  c.id_instrumento ='" .$reg1->id ."' and c.st in (1,2) 
and c.reg <= '" .$hasta ." 23:59:59'::timestamp and c.reg >= '" .$desde ." 01:01:01'::timestamp

) alia;";
            $obj2 = Aux::findBySql($sql2)->one();
            $vct1[1] = $obj2->llamadas;
            $vct1[2] = $obj2->contactados;
            if( $obj2->llamadas > 0 )
                $vct1[3] = round($obj2->contactados / $obj2->llamadas,2) .' %';
            else
                $vct1[3] = '0 %';
            $vct1[4] = $obj2->planificado;
            $vct1[5] = $obj2->efectivas;
            if( $obj2->llamadas > 0 )
                $vct1[6] = round($obj2->efectivas / $obj2->llamadas,2) .' %';
            else
                $vct1[6] = '0 %';
            if( $obj2->contactados > 0 )
                $vct1[7] = round($obj2->efectivas / $obj2->contactados,2) .' %';
            else
                $vct1[7] = '0 %';
            if( $obj2->planificado > 0 )
                $vct1[8] = round($obj2->efectivas / $obj2->planificado,2) .' %';
            else
                $vct1[8] = '0 %';
            $data[] = $vct1;            
            $t_llamadas += $obj2->llamadas;
            $t_contactados += $obj2->contactados;
            $t_planificado += $obj2->planificado;
            $t_efectivas += $obj2->efectivas;
            unset($vct1);
            $data0[] = array($reg1->de,$obj2->llamadas);
            $data1[] = array($reg1->de,$obj2->contactados); 
            $data2[] = array($reg1->de,$obj2->planificado);
            $data3[] = array($reg1->de,$obj2->efectivas);
        } // for cada instrumento
        
        $datas[] = $data0;
        $datas[] = $data1;
        $datas[] = $data2;
        $datas[] = $data3;
        
        $vct1[0] = 'TOTAL';
        $vct1[1] = $t_llamadas;
        $vct1[2] = $t_contactados;
        if( $t_llamadas > 0 )
            $vct1[3] = round($t_contactados / $t_llamadas,2) .' %';
        else
            $vct1[3] = '0 %';
        $vct1[4] = $t_planificado;
        $vct1[5] = $t_efectivas;
        if( $t_llamadas > 0 )
            $vct1[6] = round($t_efectivas / $t_llamadas,2) .' %';
        else
            $vct1[6] = '0 %';
        if( $t_contactados > 0 )
            $vct1[7] = round($t_efectivas / $t_contactados,2) .' %';
        else
            $vct1[7] = '0 %';
        if( $t_planificado > 0 )
            $vct1[8] = round($t_efectivas / $t_planificado,2) .' %';
        else
            $vct1[8] = '0 %';
        $data[] = $vct1;
                
        if( $onlyExcel == false ){
            $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
            $cad .= self::toHtml( $data, $style, '0.8em' );
            $cad .= '<br /><hr />';
            $cad .= self::toChart5( $schema, $datas, $periodo );
            $cad .= '<br /><hr /><br /><br />';
        }else{
            $cad = Reporte::hojaDeCalculo( $data, true );
        }
        return $cad;
    } // eof ##################################################  efectividad
    











    /**
     * Reporte de Efectividad Detalle
     */
    public static function efectividadDetalle( $schema, $codInstrumento, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $onlyExcel = false ){
        set_time_limit(5000); // Limite de tiempo
        $cad = '';
        $data = array();
        $data0 = array();
        $data1 = array();
        $data2 = array();
        $data3 = array();
        $datas = array();
        $cad = '';

        $vctZ['dominioOagente'] = true;


        if( $schema != '' ) $cod_proyecto = $schema;
        else{
            $schema = $cod_proyecto = Request::rq('x3proy');
            if( $cod_proyecto.'' == '' ) $schema = $cod_proyecto = Request::rq('cod_proyecto');
        }
        if( $idInstrumento > 0 )
            $inst = $id_instrumento = $idInstrumento;
        else{
            $idInstrumento = $inst = $id_instrumento = (int)Request::rq('x3inst');
            if( $id_instrumento == 0 ) $inst = $id_instrumento = Request::rq('id_instrumento');
        }
        

        $ax = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."';")->one();
        $codInstrumento = $ax->codigo;

        
        // DEL FILTRO
        if( $onlyExcel == false )
            $cad .= self::filtro1( 'Reporte de Efectividad por Detalle', $schema, $codInstrumento, $idInstrumento, 'efectividad_detalle', 'efectividad_detalle', $vctZ );
        $desde      = Request::rq('x3desde');
        $hasta      = Request::rq('x3hasta');
        $tlo        = Request::rq('x3tlo');
        $reporte    = Request::rq('x3rpt');
        $data_tp    = Request::rq('x3data_tp');
        $fecha_tp   = Request::rq('x3fecha_tp');
        $idDominio = Request::rq('x3dom');
        $agente     = Request::rq('x3agente');
        $dom_agente   = Request::rq('x3dominioOagente');
        $hoy = date('Y-m-d');
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'efectividad_detalle';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($idDominio) ) $idDominio = null;
        if( is_null($dom_agente) || $dom_agente == 2 ){
            $dom_agente = '2'; // por dominio=7 c007, por agente=2 c002    
            $campoAux = 'c002';
            $campoAux2 = 'c002';
        }else{
            $dom_agente = '7';
            $campoAux = 'c007';
            $campoAux2 = 'c006';
        }
        if( is_null($desde) ) $desde = $hoy;        
        
        $cad .= '<div class="titulos"> Reporte de Efectividad por Detalle </div>';
        
        
        
        
        // ################### CABECERA
        $vct[] = ''; $style[] = 'padding:0.3em; width: 30.0em;';
        $vct[] = ''; $style[] = 'padding:0.3em; width: 30.0em;';
        $vct[] = 'Cargados'; $style[] = 'text-align:center; padding:0.3em;'; // Total Cargados
        $vct[] = 'Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Total de llamadas
        $vct[] = 'Contactados'; $style[] = 'text-align:center; padding:0.3em;'; // Total de Clientes Contactados      
        $vct[] = '% Contactabilidad'; $style[] = 'text-align:center; padding:0.3em;'; // Porcentaje de Contactabilidad
        $vct[] = 'Enc. Planificadas'; $style[] = 'text-align:center; padding:0.3em;'; // Total de Encuestas Planificadas
        $vct[] = 'Efectivas'; $style[] = 'text-align:center; padding:0.3em;'; // Total de Encuestas Efectivas
        $vct[] = 'Efectividad de Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Efectividad de llamada
        $vct[] = 'Efectividad de Contactos'; $style[] = 'text-align:center; padding:0.3em;'; // Efectividad de Contactados
        $vct[] = '% de Cumplimiento'; $style[] = 'text-align:center; padding:0.3em;'; // Porcentaje de Cumplimiento       
        $data[] = $vct;
        


         	 	 	 	 	 	 	 	
        
        
        // $data2[0][] = 'Periodo';
        
        $t_cargados = $t_llamadas = $t_contactados = $t_planificado = $t_efectivas = array();
        $tt_cargados = $tt_llamadas = $tt_contactados = $tt_planificado = $tt_efectivas = 0; 
        

        
    
        if( $desde != $hasta )
            $periodo = $desde .' - ' .$hasta;
        else
            $periodo = $desde;
        
        
        $sql1 = "select id, de, codigo from " .$schema .".instrumento where st='1';";
        $objs1 = Aux::findBySql($sql1)->all();
        foreach( $objs1 as $reg1 ){ // ######################## CADA INSTRUMENTO
            if( !isset($t_cargados[$reg1->id]) ){
                $t_cargados[$reg1->id] = $t_llamadas[$reg1->id] = $t_contactados[$reg1->id] = $t_planificado[$reg1->id] = $t_efectivas[$reg1->id] = 0;
            }

       

            
            $sql2 = "select " .$campoAux2 .", 
 sum(cargados) as cargados,
sum(llamadas) as llamadas, 
sum(contactados) as contactados, 
sum(planificado) as planificado, 
sum(efectivas) as efectivas 
from ( 





select " .$campoAux2 .", cantidad as cargados, 0 as llamadas, 0 as contactados, 0 as planificado, 0 as efectivas
from (
select p." .$campoAux2 .", count(p.id) as cantidad
from
 " .$schema .".prospecto p 
inner join " .$schema .".cuota_prospecto cp on cp.id_prospecto = p.id
inner join " .$schema .".cuota c on cp.id_cuota = c.id
where 
c.id_instrumento ='" .$reg1->id ."'  
and c.st in (1,2,5) 
and c.fecha_encuesta <= '" .$hasta ."'  
and c.fecha_encuesta >= '" .$desde ."' 
group by " .$campoAux2 ."
 order by " .$campoAux2 .") a1



union 
select " .$campoAux2 .", 0 as cargados, count(l.*) as llamadas, 0 as contactados, 0 as planificado, 0 as efectivas
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on p.id=l.id_prospecto
where l.reg <= '" .$hasta ." 23:59:59'::timestamp and l.reg >= '" .$desde ." 01:01:01'::timestamp and l.st!= '0' and l.st!= '6'  and p.id_instrumento ='" .$reg1->id ."' 
 group by " .$campoAux2 ."

union 
select " .$campoAux2 .", 0 as cargados, 0 as llamadas, count(l.*) as contactados, 0 as planificado, 0 as efectivas
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on p.id=l.id_prospecto
where l.reg <= '" .$hasta ." 23:59:59'::timestamp and l.reg >= '" .$desde ." 01:01:01'::timestamp and l.id_tipificacion in ('1','15') and p.id_instrumento ='" .$reg1->id ."' 
 group by " .$campoAux2 ."

union 
select " .$campoAux2 .", 0 as cargados, 0 as llamadas, 0 as contactados, sum(c.cuota) as planificado, 0 as efectivo
from " .$schema .".cuota c 
inner join " .$schema .".cuota_prospecto cp on cp.id_cuota = c.id
inner join " .$schema .".prospecto p on cp.id_prospecto = p.id
where c.reg = c.reg and c.id_instrumento ='" .$reg1->id ."' and c.st in (1,2)  
and c.reg <= '" .$hasta ." 23:59:59'::timestamp and c.reg >= '" .$desde ." 01:01:01'::timestamp 
 group by " .$campoAux2 ."



union
select 
" .$campoAux2 .", 0 as cargados, 0 as llamadas, 0 as contactados, 0 as planificado, count(p.id) as efectivo 
from
 " .$schema .".prospecto p 
inner join " .$schema .".cuota_prospecto cp on cp.id_prospecto = p.id
inner join " .$schema .".cuota c on cp.id_cuota = c.id
where 
c.id_instrumento ='" .$reg1->id ."' 
and c.st in (1,2,5) 
and p.st='5'
and p.id_tipificacion in ('1','15') 
and c.fecha_encuesta <= '" .$hasta ."'  
and c.fecha_encuesta >= '" .$desde ."' 
group by " .$campoAux2 ."


) alia group by " .$campoAux2 .";";
     


            $obj2s = Aux::findBySql($sql2)->all();
            foreach( $obj2s as $obj2 ){
                $vct1[0] = $reg1->de;
                $vct1[1] = $obj2->$campoAux2;
                $vct1[2] = $obj2->cargados;
                $vct1[3] = $obj2->llamadas;
                $vct1[4] = $obj2->contactados;
                if( $obj2->llamadas > 0 )
                    $vct1[5] = round($obj2->contactados / $obj2->llamadas,2) .' %';
                else
                    $vct1[5] = '0 %';

                if( $dom_agente == '2' ) // por agente
                    $vct1[6] = '';
                else
                    $vct1[6] = $obj2->planificado;
                $vct1[7] = $obj2->efectivas;
                if( $obj2->llamadas > 0 )
                    $vct1[8] = round($obj2->efectivas / $obj2->llamadas,2) .' %';
                else
                    $vct1[8] = '0 %';
                if( $obj2->contactados > 0 )
                    $vct1[9] = round($obj2->efectivas / $obj2->contactados,2) .' %';
                else
                    $vct1[9] = '0 %';

                if( $dom_agente == '2' ) $vct1[10] = '';
                else{
                    if( $obj2->planificado > 0 )
                        $vct1[10] = round($obj2->efectivas / $obj2->planificado,2) .' %';
                    else
                        $vct1[10] = '0 %';
                }

                if( $vct1[3] > 0  || true ){
                    $data[] = $vct1;
                    $t_cargados[$reg1->id] += $obj2->cargados;
                    $t_llamadas[$reg1->id] += $obj2->llamadas;
                    $t_contactados[$reg1->id] += $obj2->contactados;
                    if( $dom_agente == '2' ) // por agente
                        $t_planificado[$reg1->id] = '';
                    else
                        $t_planificado[$reg1->id] += $obj2->planificado;
                
                    $t_efectivas[$reg1->id] += $obj2->efectivas;
                    unset($vct1);

                
                    $data0[] = array($reg1->codigo .'. ' .$obj2->$campoAux2, $obj2->llamadas);
                    $data1[] = array($reg1->codigo .'. ' .$obj2->$campoAux2, $obj2->contactados);
                    if( $dom_agente == '2' )
                        $data2[] = null;
                    else
                        $data2[] = array($reg1->codigo .'. ' .$obj2->$campoAux2, $obj2->planificado);
                    $data3[] = array($reg1->codigo .'. ' .$obj2->$campoAux2, $obj2->efectivas);

                
                }
            } // for cada instrumento
            
            
            

            $vct1[0] = '';
            $vct1[1] = 'SUBTOTAL';
            $vct1[2] = $t_cargados[$reg1->id];
            $vct1[3] = $t_llamadas[$reg1->id];
            $vct1[4] = $t_contactados[$reg1->id];
            if( $t_llamadas[$reg1->id] > 0 )
                $vct1[5] = round($t_contactados[$reg1->id] / $t_llamadas[$reg1->id],2) .' %';
            else
                $vct1[5] = '0 %';
            $vct1[6] = $t_planificado[$reg1->id];
            $vct1[7] = $t_efectivas[$reg1->id];
            if( $t_llamadas[$reg1->id] > 0 )
                $vct1[8] = round($t_efectivas[$reg1->id] / $t_llamadas[$reg1->id],2) .' %';
            else
                $vct1[8] = '0 %';
            if( $t_contactados[$reg1->id] > 0 )
                $vct1[9] = round($t_efectivas[$reg1->id] / $t_contactados[$reg1->id],2) .' %';
            else
                $vct1[9] = '0 %';
            if( $t_planificado[$reg1->id] > 0 )
                $vct1[10] = round($t_efectivas[$reg1->id] / $t_planificado[$reg1->id],2) .' %';
            else
                $vct1[10] = '0 %';
            $data[] = $vct1;








            $tt_cargados += $t_cargados[$reg1->id];
            $tt_llamadas += $t_llamadas[$reg1->id];
            $tt_contactados += $t_contactados[$reg1->id];
            $tt_planificado += $t_planificado[$reg1->id];
            $tt_efectivas += $t_efectivas[$reg1->id];
       






        
        } // for 
            
        $datas[] = $data0;
        $datas[] = $data1;
        $datas[] = $data2;
        $datas[] = $data3;


        $vct1[0] = 'TOTAL';
        $vct1[1] = '';
        $vct1[2] = $tt_cargados;
        $vct1[3] = $tt_llamadas;
        $vct1[4] = $tt_contactados;
        if( $tt_llamadas > 0 )
            $vct1[5] = round($tt_contactados / $tt_llamadas,2) .' %';
        else
            $vct1[5] = '0 %';
        $vct1[6] = $tt_planificado;
        $vct1[7] = $tt_efectivas;
        if( $tt_llamadas > 0 )
            $vct1[8] = round($tt_efectivas / $tt_llamadas,2) .' %';
        else
            $vct1[8] = '0 %';
        if( $tt_contactados > 0 )
            $vct1[9] = round($tt_efectivas / $tt_contactados,2) .' %';
        else
            $vct1[9] = '0 %';
        if( $tt_planificado > 0 )
            $vct1[10] = round($tt_efectivas / $tt_planificado,2) .' %';
        else
            $vct1[10] = '0 %';
        $data[] = $vct1;
        
        if( $onlyExcel == false ){
            $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
            $cad .= self::toHtml( $data, $style, '0.8em' );
            $cad .= '<br /><hr />';
            $cad .= Grafico::toChart4( $schema, $datas, $periodo );
            $cad .= '<br /><hr /><br /><br />';
        }else
            $cad = Reporte::hojaDeCalculo( $data, true );
        return $cad;
    } // eof ##################################################  efectividad_detalle







    


    /**
     * Reporte de Supervisor
     */    
    public static function supervisor( $schema, $codInstrumento, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $onlyExcel = false ){
        set_time_limit(5000); // Limite de tiempo
        $cad = '';
        $data = array();
        $data2 = array();
        $cad = '';

        $vctZ['tlo'] = true;

        if( $schema != '' ) $cod_proyecto = $schema;
        else{
            $schema = $cod_proyecto = Request::rq('x3proy');
            if( $cod_proyecto.'' == '' ) $schema = $cod_proyecto = Request::rq('cod_proyecto');
        }
        if( $idInstrumento > 0 )
            $inst = $id_instrumento = $idInstrumento;
        else{
            $idInstrumento = $inst = $id_instrumento = (int)Request::rq('x3inst');
            if( $id_instrumento == 0 ) $inst = $id_instrumento = Request::rq('id_instrumento');
        }
        $ax = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."';")->one();
        $codInstrumento = $ax->codigo;
        
        // DEL FILTRO
        if( $onlyExcel == false )
            $cad .= self::filtro1( 'Encuestas por Teleoperador', $schema, $codInstrumento, $idInstrumento, 'supervisor', 'supervisor', $vctZ );
        $desde      = Request::rq('x3desde');
        $hasta      = Request::rq('x3hasta');
        $tlo        = Request::rq('x3tlo');
        $reporte    = Request::rq('x3rpt');
        $data_tp    = Request::rq('x3data_tp');
        $fecha_tp   = Request::rq('x3fecha_tp');
        $idDominio = Request::rq('x3dom');
        $agente     = Request::rq('x3agente');
        $hoy = date('Y-m-d');
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'supervisor';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($idDominio) ) $idDominio = null;
        if( is_null($agente) ) $agente = null;    



        $vct0[] = '';
        $vct0[] = '';
        $vct0[] = 'Total';




        $vct[] = '#'; $style[] = 'text-align: center; padding:0.3em; width: 3.0em;';
        $vct[] = 'Nombre'; $style[] = 'padding:0.3em; width: 30.0em;';
        $vct[] = 'Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Llamadas Realizadas
        $vct[] = 'Tiempo'; $style[] = 'text-align:center; padding:0.3em;'; // Tiempo Invertido
        $vct[] = 'Efectivas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = 'No Efectivas'; $style[] = 'text-align:center; padding:0.3em;';  // Enc. No Efectivas


        $sql1 = "select id,de from " .$schema .".instrumento where st='1' order by id asc;";
        $objs1 = Aux::findBySql($sql1)->all();
        $j = 6;
        foreach( $objs1 as $regX){
            $pos[$regX->id] = $j;
            $vct0[] = $regX->id.'. '.$regX->de;
            $vct[] = 'Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Llamadas Realizadas
            $vct[] = 'Efectivas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
            $vct[] = 'No Efectivas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. No Efectivas
            $t_llamadas[$regX->id] = 0;
            $t_efectivas[$regX->id] = 0;
            $t_no_efectivas[$regX->id] = 0;
            $j += 3;
        }
        
        $data[] = $vct0;
        unset($vct0);


        $data[] = $vct;

        $sql0 = "select id,nombres,apellidos from a.usuario order by nombres asc, apellidos asc;";
        $objs0 = Aux::findBySql($sql0)->all();


        $t_llamadas[0] = $t_tiempo[0] = $t_efectivas[0] = $t_no_efectivas[0] = 0;
        $t2_efectivas[0] = $t2_no_efectivas[0] = 0;
        
        $k = 0;

        $ingresado = array();

        $f1 = " and l.reg <= '" .$hasta ." 23:59:59'::timestamp and l.reg >= '" .$desde ." 01:01:01'::timestamp ";
        $f2 = " and p.reg <= '" .$hasta ." 23:59:59'::timestamp and p.reg >= '" .$desde ." 01:01:01'::timestamp ";        
        foreach( $objs0 as $reg0 ){ // ######################## CADA TLO
            $k++;
            $vct1[0] = $reg0->id;
            $vct1[1] = $reg0->nombres .' ' .$reg0->apellidos;
            $vct1[5] = $vct1[4] = $vct1[3] = $vct1[2] = 0;
            $activo = false;
               
            foreach( $objs1 as $reg1 ){ // ######################## CADA INSTRUMENTO                  
                $sql2 = "select sum(llamadas) as llamadas, sum(efectivas) as efectivas, sum(tiempo) as tiempo_invertido
from ( 
select count(l.*) as llamadas, 0 as efectivas, '0:0:0'::interval as tiempo 
from " .$schema .".llamada l 
inner join " .$schema .".prospecto p on p.id=l.id_prospecto  
where id_usuario = '" .$reg0->id ."'  and l.st!= '0' and l.st!= '6' and p.id_instrumento='" .$reg1->id ."' $f1 
 union select 0 as llamadas2, count(p.*) as efectivas2, '0:0:0'::interval as tiempo2 
from " .$schema .".prospecto p where p.id_tipificacion in ('1','15') and tlo = '" .$reg0->id ."'   and p.id_instrumento='" .$reg1->id ."' $f2
  union select 0 as llamadas2, 0 as efectivas2, sum(fin-inicio) as tiempo2 
from " .$schema .".prospecto p where p.id_tipificacion in ('1','15') and tlo = '" .$reg0->id ."'   and p.id_instrumento='" .$reg1->id ."' $f2
) alia;";
                $objs2 = Aux::findBySql($sql2)->all();
                foreach( $objs2 as $obj2 ){
                   
                    $vct1[2] += $obj2->llamadas;
                    if( $obj2->llamadas > 0 ) $activo = true; 

                  
                    $vct1[3] = substr($obj2->tiempo_invertido,0,-7);

          
                    $vct1[4] += $obj2->efectivas;
                   
                    $vct1[$pos[$reg1->id]] = $obj2->llamadas;
                    $vct1[$pos[$reg1->id]+1] = $obj2->efectivas;
                    $vct1[$pos[$reg1->id]+2] = $obj2->llamadas - $obj2->efectivas;
                    $t_llamadas[$reg1->id] += $obj2->llamadas;
                    $t_efectivas[$reg1->id] += $obj2->efectivas;
                    $t_no_efectivas[$reg1->id] += $obj2->llamadas - $obj2->efectivas;

                    if( isset($t2_efectivas[$reg0->id]) ) $t2_efectivas[$reg0->id] += $obj2->efectivas;
                    else $t2_efectivas[$reg0->id] = $obj2->efectivas;
                    if( isset($t2_no_efectivas[$reg0->id]) ) $t2_no_efectivas[$reg0->id] += $t_no_efectivas[$reg1->id];
                    else  $t2_no_efectivas[$reg0->id] = $t_no_efectivas[$reg1->id];                    
                } // for cada instrumento
            }



            if( $activo == true ){
                $vct1[5] = $vct1[2] - $vct1[4];
                $t_llamadas[0] += $vct1[2];
                $t_tiempo[0] = self::addPeriodo($t_tiempo[0],$vct1[3]);
                $t_efectivas[0] += $vct1[4];
                $t_no_efectivas[0] += $vct1[5];
                $data[] = $vct1;
            }
                



            if( $t2_efectivas[$reg0->id] > 0 ){
                $data2[$k][] = $reg0->nombres .' ' .$reg0->apellidos;
                $data2[$k][] = $t2_efectivas[$reg0->id];
                $data2[$k][] = $t2_no_efectivas[$reg0->id];
            }
                
            unset($vct1);
                
        } // for cada tlo
            



        $k++;
        $vct1[0] = '';
        $vct1[1] = 'TODOS';
        $vct1[2] = $t_llamadas[0];
        $vct1[3] = $t_tiempo[0];
        $vct1[4] = $t_efectivas[0];
        $vct1[5] = $t_no_efectivas[0];
            
        $cantidad = count($t_llamadas);
        foreach( $t_llamadas as $k => $v){

            if( $k == 0 ) continue;
            $vct1[] = $t_llamadas[$k];
            $vct1[] = $t_efectivas[$k];
            $vct1[] = $t_no_efectivas[$k];
        }
            
        $data[] = $vct1;
            
              
        if( $desde != $hasta )
            $periodo = $desde .' - ' .$hasta;
        else
            $periodo = $desde;
            
            
     
        if( $onlyExcel == false ){
            $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
            $cad .= self::toHtml3( $data, $style );
            $cad .= '<br /><hr />';
            $cad .= Grafico::toChart2( $schema, $idInstrumento, $data2, $periodo );
            $cad .= '<br /><hr /><br /><br />';
        }else
            $cad = Reporte::hojaDeCalculo( $data, true );
        return $cad;
    } // eof ##################################################  supervisor











    /**
     * Reporte de Supervisor Detalle
     */
    public static function supervisorDetalle( $schema, $codInstrumento, $idInstrumento, $columna_fecha_ref, $columna_cod, $columna_ente, $onlyExcel = false ){
        set_time_limit(5000); // Limite de tiempo
        $cad = '';
        $data = array();
        $data2 = array();
        $cad = '';
                    
        $vctZ['tlo'] = true;
                    


        if( $schema != '' ) $cod_proyecto = $schema;
        else{
            $schema = $cod_proyecto = Request::rq('x3proy');
            if( $cod_proyecto.'' == '' ) $schema = $cod_proyecto = Request::rq('cod_proyecto');
        }
        if( $idInstrumento > 0 )
            $inst = $id_instrumento = $idInstrumento;
        else{
            $idInstrumento = $inst = $id_instrumento = (int)Request::rq('x3inst');
            if( $id_instrumento == 0 ) $inst = $id_instrumento = Request::rq('id_instrumento');
        }
        
        $ax = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id='" .$id_instrumento ."';")->one();
        $codInstrumento = $ax->codigo;
                    
                    
        // DEL FILTRO
        if( $onlyExcel == false )
            $cad .= self::filtro1( 'Encuestas por Teleoperador - Detallado', $schema, $codInstrumento, $idInstrumento, 'supervisor_detalle', 'supervisor_detalle', $vctZ );
        $idTlo      = (int)Request::rq('x3tlo');
        if( $idTlo == 0 ) return $cad;
        $desde      = Request::rq('x3desde');
        $hasta      = Request::rq('x3hasta');
        $tlo        = Request::rq('x3tlo');
        $reporte    = Request::rq('x3rpt');
        $data_tp    = Request::rq('x3data_tp');
        $fecha_tp   = Request::rq('x3fecha_tp');
        $idDominio = Request::rq('x3dom');
        $agente     = Request::rq('x3agente');
        $hoy = date('Y-m-d');
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'supervisor_detalle';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($idDominio) ) $idDominio = null;
        if( is_null($agente) ) $agente = null;    



        $sql0 = "select id,nombres,apellidos from a.usuario where id='" .$idTlo ."' order by nombres asc, apellidos asc;";
        $reg0 = Aux::findBySql($sql0)->one();
        
        
        $cad .= '<div class="subtitulos"> Encuestas por Teleoperador - Detallado ';
        $cad .= ' de ' .$reg0->nombres .' ' .$reg0->apellidos .' <sup title="Número ID">'.$reg0->id.'</sup> </div><br />'; 

    
  
        
       

        $vct[] = 'Instrumento'; $style[] = 'text-align: center; padding:0.3em; width: 30.0em;';
        $vct[] = ''; $style[] = 'padding:0.3em;';
        $vct[] = 'Llamadas'; $style[] = 'text-align:center; padding:0.3em;'; // Llamadas Realizadas
        $vct[] = 'Tiempo Efec'; $style[] = 'text-align:center; padding:0.3em;'; // Tiempo Invertido
        $vct[] = 'Efectivas'; $style[] = 'text-align:center; padding:0.3em;'; // Enc. Efectivas
        $vct[] = 'Tiempo No Efec'; $style[] = 'text-align:center; padding:0.3em;'; // Tiempo Invertido        
        $vct[] = 'No Efectivas'; $style[] = 'text-align:center; padding:0.3em;';  // Enc. No Efectivas
        $data[] = $vct;

 


        $t_llamadas = $t_tiempo = $t_tiempo_efectivo = $t_efectivas = $t_no_efectivas = 0;


        $fecha = " and p.reg <= '" .$hasta ." 23:59:59'::timestamp and p.reg >= '" .$desde ." 01:01:01'::timestamp ";

        
        $sql1 = "select i.codigo, i.id as id_instrumento, i.de as instrumento, d.id as id_dominio, d.de as dominio from " .$schema .".instrumento i inner join " .$schema .".dominio d on d.id_instrumento=i.id where i.st='1' and d.st='1' order by i.id asc, d.cod asc;";
        $objs1 = Aux::findBySql($sql1)->all();
        foreach( $objs1 as $reg1 ){ // ######################## POR CADA INSTRUMENTO - DOMINIO

            $dominio = " inner join  " .$schema .".dominio d on d.cod = p." .$columna_cod ." and d.id = $reg1->id_dominio ";
            
            
            $sql2 = "select 
sum(llamadas) as llamadas, sum(efectivas) as efectivas, sum(tiempo) as tiempo_invertido, sum(tefectivo) as tiempo_efectivo
from ( 
select count(l.*) as llamadas, 0 as efectivas, '0:0:0'::interval as tiempo, '0:0:0'::interval as tefectivo 
from " .$schema .".llamada l inner join " .$schema .".prospecto p on l.id_prospecto=p.id $dominio where id_usuario='" .$reg0->id ."' and l.reg <= '" .$hasta ." 23:59:59'::timestamp and l.reg >= '" .$desde ." 01:01:01'::timestamp and l.st!= '0' and l.st!= '6'  and p.id_instrumento='" .$reg1->id_instrumento ."' 
 
union select 0 as llamadas, count(p.*) as efectivas, '0:0:0'::interval as tiempo, '0:0:0'::interval as tefectivo 
from " .$schema .".prospecto p $dominio where p.id_tipificacion in ('1','15') and tlo='" .$reg0->id ."' " .$fecha ."  and p.id_instrumento='" .$reg1->id_instrumento ."' 

union select 0 as llamadas, 0 as efectivas, sum(fin-inicio) as tiempo, '0:0:0'::interval as tefectivo 
from " .$schema .".prospecto p $dominio where p.st='5' and tlo='" .$reg0->id ."' " .$fecha ."  and p.id_instrumento='" .$reg1->id_instrumento ."' 
  
union select 0 as llamadas, 0 as efectivas, '0:0:0'::interval as tiempo, sum(fin-inicio) as tefectivo 
from " .$schema .".prospecto p $dominio where p.id_tipificacion in ('1','15') and tlo='" .$reg0->id ."' " .$fecha ." and p.id_instrumento='" .$reg1->id_instrumento ."' 

) alia;";         
            $objs2 = Aux::findBySql($sql2)->all();
            foreach( $objs2 as $obj2 ){
                if( $obj2->llamadas > 0 ){
                    $vct1[0] = $reg1->instrumento;
                    $vct1[1] = $reg1->dominio;
                
                    $vct1[2] = $obj2->llamadas;
                    $vct1[3] = substr($obj2->tiempo_efectivo,0,-7);
                    $vct1[4] = $obj2->efectivas;

                    $vct1[5] = self::supPeriodo(substr($obj2->tiempo_invertido,0,-7), substr($obj2->tiempo_efectivo,0,-7));
  
                    $vct1[6] = $obj2->llamadas - $obj2->efectivas;
                

                    $t_llamadas += $vct1[2];
                    $t_tiempo_efectivo = self::addPeriodo($t_tiempo_efectivo,$vct1[3]);
                    $t_efectivas += $vct1[4];
                    $t_tiempo = self::addPeriodo($t_tiempo,$vct1[5]);
                    $t_no_efectivas += $vct1[6];
                    $data[] = $vct1;
                    if( isset($reg1->dominio) )
                        $data2[] = array( $reg1->dominio.'. '.strtoupper($reg1->codigo), $t_efectivas, $t_no_efectivas);
                    else
                        $data2[] = array($reg1->instrumento, $t_efectivas, $t_no_efectivas);    
                }    
                unset($vct1);
            }


             
        } // for cada instrumento
        
        
        if( $desde != $hasta )
            $periodo = $desde .' - ' .$hasta;
        else
            $periodo = $desde;
        
        

                
 
        
        // fin tlo
        $vct1[0] = 'TODOS';
        $vct1[1] = '';
        $vct1[2] = $t_llamadas;
        $vct1[3] = $t_tiempo_efectivo;
        $vct1[4] = $t_efectivas;
        $vct1[5] = $t_tiempo;
        $vct1[6] = $t_no_efectivas;       
        $data[] = $vct1;
  

        
        if( $onlyExcel == false ){
            $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
            $cad .= self::toHtml( $data, $style, '0.8em' );
            $cad .= '<br /><hr />';
            $cad .= Grafico::toChart2( $schema, $idInstrumento, $data2, $periodo );
            $cad .= '<br /><hr /><br /><br />';
        }else
            $cad = Reporte::hojaDeCalculo( $data, true );
        return $cad;
    } // eof ##################################################  supervisor detalle

    




    
    

    /**
     * Genera un Periodo 
     */
    public static function addPeriodo( $original, $nuevo ){
        $hor1 = substr($original,0,2);
        $hor2 = substr($nuevo,0,2);
        $min1 = substr($original,3,2);
        $min2 = substr($nuevo,3,2);
        $seg1 = substr($original,6,2);
        $seg2 = substr($nuevo,6,2);
        $seg = $seg1 + $seg2;
        if( $seg > 59 )
            $seg0 = $seg % 60;
        else
            $seg0 = $seg;
        if( $seg0 < 10 )
            $seg0 = Cadena::set0($seg0,1);
        $min = $min1 + $min2 + ($seg -($seg % 60))/60;
        if( $min > 59 )
            $min0 = $min % 60;
        else
            $min0 = $min;
        if( $min0 < 10 )
            $min0 = Cadena::set0($min0,1);
        $hor = $hor1 + $hor2 + ($min -($min % 60))/60; 
        return $hor .':' .$min0 .':' .$seg0;
    } // eof ##################################################



    /**
     * Genera un Periodo SUP
     */
    public static function supPeriodo( $original, $nuevo ){
        $hor1 = substr($original,0,2);
        $hor2 = substr($nuevo,0,2);
        $min1 = substr($original,3,2);
        $min2 = substr($nuevo,3,2);
        $seg1 = substr($original,6,2);
        $seg2 = substr($nuevo,6,2);
        $seg = $seg1 - $seg2;
        $acu = 0;
        if( $seg < 0 ){
            $seg0 = abs($seg) % 60;
            $acu = ceil($seg0/60);
        }else
            $seg0 = $seg;
        if( $seg0 < 10 )
            $seg0 = Cadena::set0($seg0,1);
        $min = $min1 - $min2 - $acu;
        $acu = 0;
        if( $min < 0 ){
            $min0 = abs($min) % 60;
            $acu = ceil($min0/60);
        }else
            $min0 = $min;
        if( $min0 < 10 )
            $min0 = Cadena::set0($min0,1);
        $hor = $hor1 - $hor2 - $acu; 
        return $hor .':' .$min0 .':' .$seg0;
    } // eof ##################################################



    
    /**
     * Reporte de Data Cruda
     */
    public static function dc( $schema, $codInstrumento, $idInstrumento, $onlyExcel = false ){
        $cad = '';
        $vctZ['data_tp'] = true;
        $vctZ['fecha_tp'] = true;
        $vctZ['id_dominio'] = true;
        $vctZ['agente'] = true;

        if( $onlyExcel == false ) 
            $cad .= self::filtro1( 'Data Cruda', $schema, $codInstrumento, $idInstrumento, 'dc', 'dc', $vctZ );

        $desde      = Request::rq('x3desde');
        $hasta      = Request::rq('x3hasta');
        $tlo        = Request::rq('x3tlo');
        $reporte    = Request::rq('x3rpt');
        $data_tp    = Request::rq('x3data_tp');
        $fecha_tp   = Request::rq('x3fecha_tp');
        $id_dominio = Request::rq('x3dom');
        $agente     = Request::rq('x3agente');
        

        $hoy = date('Y-m-d');
        if( is_null($desde) ) $desde = $hoy;
        if( is_null($hasta) ) $hasta = $hoy;
        if( is_null($reporte) ) $reporte = 'dc';
        if( is_null($data_tp) ) $data_tp = '1';
        if( is_null($fecha_tp) ) $fecha_tp = '1';
        if( is_null($id_dominio) ) $id_dominio = null;
        if( is_null($agente) ) $agente = null;    

        
        
        $vct = array();
        $select = '';
        $filtro = '';




        $vct[] = 'ID ENCUESTA ';
        $select .= 'encuesta,';
        $style[] = 'text-align:center; padding:0.3em;';


        
        $sql0 = "select c.columna, c.de, e.de as pregunta from " .$schema .".cabecera c left join " .$schema .".entrada e on e.codigo=c.de and e.id_instrumento='" .$idInstrumento ."' where 1=1 and c.id_instrumento ='" .$idInstrumento ."' and c.st='1' and c.columna != 'tlo' order by c.orden ASC;";
        $objs0 = Aux::findBySql($sql0)->all();
        $select = '';
        foreach( $objs0 as $obj0 ){
            if( is_null($obj0->pregunta) )
                $vct[] = strtoupper($obj0->de);
            else
                $vct[] = strtoupper($obj0->de) .'. ' .$obj0->pregunta;
            $select .= $obj0->columna .',';
            $style[] = 'text-align:center; padding:0.3em;';
        }
  
        $vct[] = 'Teleoperador';
        $select .= 'tlo';
        $style[] = 'text-align:center; padding:0.3em;'; // para TLO

        $style[1] = 'padding:0.3em;'; // agente
        $style[3] = 'padding:0.3em;'; // nombre    

        
        $sql1 = "select encuesta," .$select .", u.nombres, u.apellidos,p.inicio,p.fin from ";
        $sql1 .= $schema .".prospecto p ";
        $sql1 .= " inner join a.usuario u on u.id=p.tlo ";
        $sql1 .= " where ";
        $sql1 .= " id_instrumento ='" .$idInstrumento ."' ";
        $sql1 .= " and id_tipificacion > 0 " .$filtro ." ";

        if( $desde != null )
            $sql1 .= " and p.up >= '" .$desde ." 01:01:01'::timestamp ";

  
        if( $hasta != null )
            $sql1 .= " and p.up <= '" .$hasta ." 23:59:59'::timestamp ";

        
        if( $data_tp == 1 )
            $sql1 .= " and p.id_tipificacion in ('1','15') ";
        else if( $data_tp == 0 )
            $sql1 .= " and p.id_tipificacion != '15' and p.id_tipificacion != '1' and p.id_tipificacion > 0 ";
            
            
        $sql1 .= " order by encuesta ASC;";
      
        
        $objs1 = Aux::findBySql($sql1)->all();

        $data = array();
        $data[] = $vct;

        foreach( $objs1 as $obj1 ){ // recorro la tabla prospecto
            $y[] = $obj1->encuesta;
            foreach( $objs0 as $obj0 ){ // recorro las cabeceras
                $x = $obj0->columna;
                if( $x == 'c008' ) // fecha
                    $y[] = substr($obj1->inicio,0,10);  // 2017-03-08 17:53:25
                else if( $x == 'c009' ) // hora_inicio
                    $y[] = substr($obj1->inicio,11,8);
                else if( $x == 'c010' ) // hora fin
                    $y[] = substr($obj1->fin,11,8);
                else 
                    $y[] = $obj1->$x;
            }
            $y[] = $obj1->nombres .' ' .$obj1->apellidos;
            $data[] = $y;
            unset($y);
        }


        if( $onlyExcel == false ){
        
        $cad .= Reporte::hojaDeCalculo( $data ) .'<br /><br />';
        $cad .= self::toHtml2( $data, $style );
        

        $cad .= '<br /><hr /><br /><br />';

        } else $cad = Reporte::hojaDeCalculo( $data, true );
    
        
        
        
        return $cad;
    } // eof ##################################################   
    
        
} // class
