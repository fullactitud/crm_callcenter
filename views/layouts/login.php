<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\xUsuario;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>

<html lang="<?= Yii::$app->language ?>">
    <head>
    <title><?=Yii::t('app', 'Customer Relationship Management');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="es" />
    <meta charset="<?= Yii::$app->charset ?>">
    <link rel="shortcut icon" href="img/cyc1.png" type="image/png" />

 <script>var x3theme='4';</script>
 <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui4/jquery-ui.min.css" />
 <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui4/jquery-ui.structure.min.css" />
 <link rel="stylesheet" type="text/css" href="css/jquery/jquery-ui4/jquery-ui.theme.min.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/searchFilter.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid-bootstrap.css" media="screen" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.jqgrid-bootstrap-ui.css" />
 <link rel="stylesheet" type="text/css" href="css/jqgrid/ui.multiselect.css" />
 
 <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
 <link rel="stylesheet" type="text/css" href="css/cyc.css" />


<script language="Javascript">
  var baseUrl = "<?=Yii::$app->params['baseUrl'];?>";
  var webservice = baseUrl;
</script>
    
 <script type="text/javascript" src="js/jquery/jquery-3.1.1.min.js"></script>
 <script type="text/javascript" src="js/jquery/jquery-ui4/package.json"></script>
 <script type="text/javascript" src="js/jquery/jquery-ui4/jquery-ui.min.js"></script>
 <script type="text/javascript" src="js/jqgrid/i18n/grid.locale-es.js"></script>
 <script type="text/javascript" src="js/jqgrid/jquery.jqGrid.min.js"></script>
                 <script type="text/javascript">         
                 $.jgrid.no_legacy_api = true;
$.jgrid.useJSON = true;
$.jgrid.defaults.width = "700";
</script>                 
 <script type="text/javascript" src="js/bootstrap.min.js"></script>
 <script type="text/javascript" src="js/cyc.js"></script>

                 <?=Html::csrfMetaTags();?>

<?=$this->head();?>

</head>
<body>
<?php $this->beginBody() ?>


<center>
  <div class="panel panel-default" style="clear:both; width:400px; position: relative;"/>
    <div class="panel-heading text-center">
      <img src="img/callycall1.png" style="position:relative;height:40px;top:0px;">
    </div>
    <div style="padding:1.0em;">
      <div class="container" style="text-align:left;">
        <div class="row"  style="clear:both;">
          <?= $content ?>
        </div>
      </div>
    </div>
  </div>
</center>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>