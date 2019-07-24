<?php
namespace app\components\crm;
use Yii;

use app\components\crm\Request;
use app\components\crm\Formato;
use app\components\crm\Cadena;
use app\models\Aux;

use app\models\test\cProyecto;
use app\models\crm\Instrumento;
use app\models\crm\xInstrumento;
use app\models\AuthAssignment;

/**
 * Clase helper para graficos
 */
class Grafico{
    
    
    /**
     * Grafico tipo 1
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
     * Grafico tipo 2
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
     * Grafico tipo 5
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
     * Grafico tipo 4
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
    
        
} // class
