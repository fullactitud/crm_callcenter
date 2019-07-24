<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\xUsuario;



\Yii::$app->language = 'es-VE';

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
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

    $vct[] = 1;
$vct[] = 2;
$vct[] = 4; // basico
$vct[] = 5;
$vct[] = 6;
$vct[] = 7;
$vct[] = 9;
$vct[] = 10;
$vct[] = 11;
$vct[] = 12;
$vct[] = 13;
$vct[] = 14;
$vct[] = 17;
$vct[] = 24;
    $rand2 = rand(0,13);

$rand = $vct[$rand2];

$rand = 13;





$fonturl[] = 'adine_kirberg.ttf';
$fonturl[] = 'alba.ttf';
$fonturl[] = 'albam.ttf';
$fonturl[] = 'albas.ttf';

$fonturl[] = 'bleeding_cowboys.ttf';
$fonturl[] = 'brockscript.ttf';
$fonturl[] = 'brush_tip_terrence.ttf';
$fonturl[] = 'candles.ttf';
$fonturl[] = 'candles_chrome.ttf';

$fonturl[] = 'chopin_script.ttf';

$fonturl[] = 'fantastic_pete.ttf';

$fonturl[] = 'francisco_lucas_briosa.ttf';
$fonturl[] = 'francisco_lucas_llana.ttf';
$fonturl[] = 'gabrielle.ttf';

$fonturl[] = 'lastfontwastingonyou.ttf';

$fonturl[] = 'marcelle_sc.ttf';
$fonturl[] = 'mlsjn.ttf';
$fonturl[] = 'old_script.ttf';

$fonturl[] = 'pepsi_pl.ttf';
$fonturl[] = 'promocyja.ttf';
$fonturl[] = 'radagund.ttf';
$fonturl[] = 'renaissance.ttf';
$fonturl[] = 'sony_sketch_ef.ttf';
$fonturl[] = 'stamaj.ttf';
$fonturl[] = 'stampact.ttf';
$fonturl[] = 'tagettPl.ttf';
$fonturl[] = 'tagetts.ttf';
$fonturl[] = 'tequila.ttf';



$fonturl[] = 'arial_narrow_7.ttf';
$fonturl[] = 'CuxhavenTimes.ttf';
$fonturl[] = 'GOODTIME.ttf';

$fonturl[] = 'timesnewarial.ttf';
$fonturl[] = 'TIMESS__.ttf';



$font[] = 'adine_kirberg';
$font[] = 'alba';
$font[] = 'albam';
$font[] = 'albas';

$font[] = 'bleeding_cowboys';
$font[] = 'brockscript';
$font[] = 'brush_tip_terrence';
$font[] = 'candles';
$font[] = 'candles_chrome';

$font[] = 'chopin_script';

$font[] = 'fantastic_pete';

$font[] = 'francisco_lucas_briosa';
$font[] = 'francisco_lucas_llana';
$font[] = 'gabrielle';

$font[] = 'lastfontwastingonyou';

$font[] = 'marcelle_sc';
$font[] = 'mlsjn';
$font[] = 'old_script';

$font[] = 'pepsi_pl';
$font[] = 'promocyja';
$font[] = 'radagund';
$font[] = 'renaissance';
$font[] = 'sony_sketch_ef';
$font[] = 'stamaj';
$font[] = 'stampact';
$font[] = 'tagettPl';
$font[] = 'tagetts';
$font[] = 'tequila';

// nuevas
$font[] = 'arial_narrow_7';
$font[] = 'CuxhavenTimes';
$font[] = 'GOODTIME';

$font[] = 'timesnewarial';
$font[] = 'TIMESS__';

$rn = rand(0,count($font)-1);
$rn = 32;
$font = $font[$rn];
$fonturl = $fonturl[$rn];

    ?>
<script>var x3theme='<?=$rand;?>';</script>

<style>
@font-face{ font-family: <?=$font;?>; src: url("font/<?=$fonturl;?>"); font-weight: bold; }
.titulos{ font-family: <?=$font;?>; font-size:1.4em; padding-bottom: 0.8em; padding-top: 0.8em; color: #505099; }
.subtitulos{ font-family: <?=$font;?>; font-size:1.2em; }
</style>

 <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui<?=$rand;?>/jquery-ui.min.css" />
 <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui<?=$rand;?>/jquery-ui.structure.min.css" />
 <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui<?=$rand;?>/jquery-ui.theme.min.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/searchFilter.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid-bootstrap.css" media="screen" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid-bootstrap-ui.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.multiselect.css" />
 
 <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
 <link rel="stylesheet" type="text/css" href="css/cyc.css" />




 <script type="text/javascript" src="js/jquery/jquery-3.1.1.min.js"></script>
 <script type="text/javascript" src="js/jquery/jquery-ui<?=$rand;?>/package.json"></script>
 <script type="text/javascript" src="js/jquery/jquery-ui<?=$rand;?>/jquery-ui.min.js"></script>
 <script type="text/javascript" src="js/jqgrid/i18n/grid.locale-es.js"></script>
 <script type="text/javascript" src="js/jqgrid/jquery.jqGrid.min.js"></script>
 <script type="text/javascript" src="js/bootstrap.min.js"></script>
 <script type="text/javascript" src="js/cyc.js"></script>
 <script type="text/javascript" src="js/instrumento1.js"></script>
    
<script language="Javascript" type="text/javascript"> var baseUrl = "<?=Yii::$app->params['baseUrl'];?>"; var webservice = baseUrl; $.jgrid.no_legacy_api = true; $.jgrid.useJSON = true; $.jgrid.defaults.width = "700"; </script>
<?=Html::csrfMetaTags(); ?>
<?=$this->head();  ?>
</head>

<body>
<?php
$this->beginBody();
$u = xUsuario::findIdentity(Yii::$app->user->id);
if( isset($u) ) $nom = $u->getNombre();
else $nom = '';
$proyecto = 'PROYECTO_NOMBRE'; 
?>

<div class="container">
  <div class="row">
    <?= $content ?>
  </div>
</div>
          
<?php
          if( 0 ) $this->endBody();
?>
</body>
</html>
<?php $this->endPage(); ?>
