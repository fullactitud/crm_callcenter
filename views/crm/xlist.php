<?php
/* @var $this yii\web\View */
?>
<?=$menu;?>
<div style="clear: both;">
  <?=$boton;?>
  <div id="divdetail" class="col-sm-6 ui-jqgrid ui-corner-all" style="position:relative;"></div>
  <div class="col-sm-6" style="width:auto;">
    <div class="titulos"><?=$titulo;?></div>  
    <table id="cyccrmlist1" style=""></table>
    <div id="cyccrmpager1"></div>       
    <script><?=$grilla;?></script>
  </div>
</div>