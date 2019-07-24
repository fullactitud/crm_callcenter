<?php
/* @var $this yii\web\View */
$this->title = 'CRM';

$cad = '<div id="contenedor">';
$cad .= '<div><h2>Instrumentos</h2></div>';


$th = '0';
$cad .= '<div class="list-group" style="font-size: 1.2em;">';
$aux = 'Agregar Instrumento';
$cad .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=' .$schema .'/df/add" class="list-group-item">';
$cad .= $aux;
$cad .= '</a>';
foreach( $objs as $obj ){
    $cad .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=' .$schema .'/df/instrumento&id=' .$obj->id .'" class="list-group-item">';
    $cad .= $obj->de;
    $cad .= '</a>';
}
$cad .= '</div>';


$cad .= '</div>';
echo $cad;
?>


