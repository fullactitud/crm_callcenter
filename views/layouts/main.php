<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\xUsuario;

use app\components\crm\Request;
use app\components\crm\Usuario;
use app\models\Aux;

\Yii::$app->language = 'es-VE';
AppAsset::register($this);
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
    <title><?=Yii::t('app', 'CRM');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="<?= Yii::$app->language ?>" />
    <meta charset="<?= Yii::$app->charset ?>">
    <link rel="shortcut icon" href="img/cyc1.png" type="image/png" />

<?php    
    // TEMA DEFAULT
    $ruta_font = '';
$font = 'Times new Roman';
$bootstrap = 'bootstrap.min.css';    
$jqueryui = 13;


// CARGA TEMA DEL INSTRUMENTO
$cod_proyecto = Request::rq('cod_proyecto');
if( is_null($cod_proyecto) ) $cod_proyecto = Request::rq('x3proy');
$id_instrumento = (int)Request::rq('id_instrumento');
if( $id_instrumento == 0 ) $id_instrumento = (int)Request::rq('x3inst');
if( !is_null($cod_proyecto) && $id_instrumento > 0 ){ // tema del instrumento
    $sql = "select f.de as font, f.tipo as tp, b.url as bootstrap, j.id as jqueryui from " .$cod_proyecto .".instrumento i inner join a.font f on f.id = i.id_font inner join a.jqueryui j on j.id= i.id_jqueryui inner join a.bootstrap b on b.id = i.id_bootstrap where i.id='" .$id_instrumento ."';";
    $tema = Aux::findBySql($sql)->all();
    if( count($tema) > 0 ){
        if( $tema[0]->tp != 'css' ) $ruta_font = $tema[0]->font.'.'.$tema[0]->tp;
        else $ruta_font = '';
        
        if( $tema[0]->font !='' ) $font = $tema[0]->font;
        
        if( $tema[0]->bootstrap != '' ) $bootstrap = $tema[0]->bootstrap;
        
        if( $tema[0]->jqueryui > 0 ) $jqueryui = (int)$tema[0]->jqueryui;
    }
} // if


echo '<script>var x3theme=\'' .$jqueryui .'\';</script>';
if( $ruta_font != '' ){
    echo '<style>
 @font-face { font-family: ' .$font .'; src: url("font/' .$ruta_font .'"); font-weight: bold; }
 .titulos{ font-family: ' .$font .'; font-size:1.85em; padding-bottom: 1.0em; padding-top: 1.0em; }
 .subtitulos{ font-family: ' .$font .'; font-size:1.5em; }
</style>';
}else{
    echo '<style>
 @font-face { font-family: ' .$font .'; font-weight: bold; }
 .titulos{ font-family: ' .$font .'; font-size:1.85em; padding-bottom: 1.0em; padding-top: 1.0em; }
 .subtitulos{ font-family: ' .$font .'; font-size:1.5em; }
</style>';
}

?>


<link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui<?=$jqueryui;?>/jquery-ui.min.css" />
                <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui<?=$jqueryui;?>/jquery-ui.structure.min.css" />
                <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui<?=$jqueryui;?>/jquery-ui.theme.min.css" />
                <link rel="stylesheet" type="text/css" href="css/bootstrap/<?=$bootstrap;?>">
                <link rel="stylesheet" type="text/css" href="css/jqgrid/searchFilter.css" />
                <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid.css" />
                <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid-bootstrap.css" media="screen" />
                <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid-bootstrap-ui.css" />
                <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.multiselect.css" />
                <link rel="stylesheet" type="text/css" href="css/cyc.css" />

                                <script type="text/javascript" src="js/jquery/jquery-3.1.1.min.js"></script> 
                <script type="text/javascript" src="js/jquery/jquery-ui<?=$jqueryui;?>/package.json"></script>
                <script type="text/javascript" src="js/jquery/jquery-ui<?=$jqueryui;?>/jquery-ui.min.js"></script>
                                

                <script type="text/javascript" src="js/jqgrid/i18n/grid.locale-es.js"></script>
                <script type="text/javascript" src="js/jqgrid/jquery.jqGrid.min.js"></script>
                <script type="text/javascript" src="js/bootstrap.min.js"></script>
                <script type="text/javascript" src="js/loader.js"></script>
                <script type="text/javascript" src="js/cyc.js"></script>
                <script type="text/javascript" src="js/instrumento1.js"></script>
                
                <script language="Javascript" type="text/javascript">
                var baseUrl = "<?=Yii::$app->params['baseUrl'];?>";
var webservice = baseUrl;
$.jgrid.no_legacy_api = true;
$.jgrid.useJSON = true;
$.jgrid.defaults.width = "700";
</script>
<?=Html::csrfMetaTags(); ?>
<?=$this->head();  ?>

</head>


<body>
<?php
$this->beginBody(); 
$u = xUsuario::findIdentity(Usuario::id());
if( isset($u) ) $nom = $u->getNombre();
else $nom = '';
?>







<nav class="navbar navbar-default  navbar-fixed-top" role="navigation" style="background-color:#000000;">
  <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
        <span class="sr-only">Desplegar navegación</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?=Yii::$app->params['baseUrl'];?>index.php"><img src="img/callycalltop.png" style="position:relative;height:50px;" title=" Inicio de Call & Call "></a>
  </div>
          
    <!-- se ocultan al minimizar la barra -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?=Yii::$app->params['baseUrl'];?>index.php" style="color:#ffffff; font-weight:700;" title=" Menú Panel "> Panel </a></li>

        <li><a href="#" onClick="javascript:if($('#divAyuda0').css('display') == 'block'){$('#divAyuda0').hide();}else{$('#divAyuda0').show();}" style="color:#ffffff; font-weight:700;" title=" Mostrar Ayuda "> Ayuda </a></li>

          <li><a href="<?=Yii::$app->params['baseUrl'];?>index.php?r=soporte/mensaje/solicitud" style="color:#ffffff; font-weight:700;" title=" Solicitud de Soporte "> Soporte </a></li>
          
        <li><a href="<?=Yii::$app->params['baseUrl'];?>index.php?r=df/logout" style="color:#ffffff; font-weight:700;" title=" Salir del Sistema "> Salir &nbsp; &nbsp; </a></li>
      </ul>
    </div>

    <div class="ui-jqgrid-titlebar ui-widget-header ui-helper-clearfix" style="clear:both; border-bottom:none; height: 1.4em; overflow:hidden; width:100%;">
      <div style="float:left; width:48%; overflow:hidden; text-align:left; padding-left: 3%; height: auto;" title="<?=\Yii::t('app/crm', 'Usuario');?>: <?=$nom;?>"><?=$nom;?></div>
      <div style="float:left; width:4%; overflow:hidden; height: auto;">&nbsp;</div>
      <div id="x3divProyectoTop" style="float:right; width:48%; overflow:hidden; text-align:right; padding-right: 3%; height: auto;" >&nbsp;</div>
    </div>

</nav>


                          

<div class="container">
  <div class="row">
          <?=$content;?>
  </div>
</div>


<footer class="footer"><div class="container"><p class="pull-left">&copy; <?=Yii::t('app/crm', 'Call & Call');?> <?= date('Y') ?></p></div></footer>

<style type="text/css">
#x3ConfirmBox {
	width: 10em;
	height: 5em;
	position: absolute;
	z-index: 1;
	visibility: hidden;
	background: blue;
	color: white;
	border: 6px double white;
	text-align: center;
}
</style>
<div id="x3ConfirmBox"><div id="x3ConfirmBox_text"></div><div id="x3ConfirmBox_op1"></div><div id="x3ConfirmBox_op2"></div></div>

</body>
</html>
<?php $this->endPage(); ?>
