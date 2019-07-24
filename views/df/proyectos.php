<?php
/* @var $this yii\web\View */
$this->title = 'CRM';


$cad = '<div id="contenedor">';

$cad .= '<div><h2>Proyectos</h2></div>';


$th = '0';
$i = 0;
$cad .= '<div class="list-group" style="font-size: 1.2em;">';


$aux = 'Agregar Proyecto';
$cad .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=crm/df/add" class="list-group-item">';
$cad .= '<div class="row">';
$cad .= '<div class="col-sm-1"><img id="pyadd" src="img/proyectos' .$th .'/add.png" alt="' .$aux .'" style="height:2.0em;"/></div>';
$cad .= '<div class="col-sm-11">' .$aux .'</div>';
$cad .= '</div>';
$cad .= '</a>';



foreach( $objs as $proyecto ){



$cad .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=' .$proyecto->codigo .'/df/instrumentos" class="list-group-item">';
$cad .= '<div class="row">';
$cad .= '<div class="col-sm-1"><img id="py' .$proyecto->codigo .'" src="img/proyectos' .$th .'/' .$proyecto->codigo .'.png" alt="' .$proyecto->de .'" style="height:2.0em;"/></div>';
$cad .= '<div class="col-sm-11">' .$proyecto->de .'</div>';
$cad .= '</div>';
$cad .= '</a>';

}


$cad .= '</div>';


$cad .= '</div>';
echo $cad;

?>


