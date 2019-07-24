<?php $this->pageTitle=Yii::app()->name . ' - Register'; ?>

<center><div style="width: 25.0em;">
  <h1><?=Yii::t("CtgModule.lang", 'Register');?></h1>

  <p><?=Yii::t("CtgModule.lang", 'Please fill out the following form with your login credentials:');?></p>

  <div class="form" style="text-align: left;">
  <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'register-form',
	'enableAjaxValidation'=>true,
)); ?>

    <p class="note"><?=Yii::t("CtgModule.lang", 'Fields with <span class="required">*</span> are required.');?></p>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'email');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_email" name="webclient[email]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'password');?></div>
      <div style="float: left; width: 15.0em;"><input type="password" id="webclient_clave" name="webclient[clave]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'names');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_nombre" name="webclient[nombre]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'last names');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_apellido" name="webclient[apellido]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'enterprice');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_empresa" name="webclient[empresa]" style="width: 15.0em;" /></div>
    </div>


    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'addres');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_dir" name="webclient[dir]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'phone');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_telefono" name="webclient[telefono]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'rif');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_documento" name="webclient[documento]" style="width: 15.0em;" /></div>
    </div>

    <div class="row" style="clear: both;">
      <div style="float: left; width: 10.0em;"><?=Yii::t("CtgModule.lang", 'zona');?></div>
      <div style="float: left; width: 15.0em;"><input type="text" id="webclient_id_zona" name="webclient[id_zona]" style="width: 15.0em;" /></div>
    </div>



    <div class="row buttons" style="clear: both; text-align: center;"> <?=CHtml::submitButton(Yii::t("CtgModule.lang", 'Send'));?></div>

<?php $this->endWidget(); ?>
</div><!-- form -->
</div></center>


