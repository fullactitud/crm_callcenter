<?php
$this->pageTitle=Yii::app()->name . ' - Contact Us';
$this->breadcrumbs=array(
	'Contact',
);
?>

<h1><?=Yii::t("CtgModule.lang", 'Contact Us');?></h1>

<?php if(Yii::app()->user->hasFlash('contact')): ?>

<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('contact'); ?>
</div>

<?php else: ?>

<p>
<?=Yii::t("CtgModule.lang", 'If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.');?>
</p>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm'); ?>

	<p class="note"><?=Yii::t("CtgModule.lang", 'Fields with <span class="required">*</span> are required.');?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,Yii::t("CtgModule.lang", 'name')); ?>
		<?php echo $form->textField($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,Yii::t("CtgModule.lang", 'email')); ?>
		<?php echo $form->textField($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,Yii::t("CtgModule.lang", 'Phone')); ?>
		<?php echo $form->textField($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,Yii::t("CtgModule.lang", 'subject')); ?>
		<?php echo $form->textField($model,'subject',array('size'=>60,'maxlength'=>128)); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,Yii::t("CtgModule.lang", 'body')); ?>
		<?php echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<?php if(CCaptcha::checkRequirements()): ?>
	<div class="row">
		<?php echo $form->labelEx($model,Yii::t("CtgModule.lang", 'verifyCode')); ?>
		<div>
		<?php $this->widget('CCaptcha'); ?>
		<?php echo $form->textField($model,'verifyCode'); ?>
		</div>
		<div class="hint"><?=Yii::t("CtgModule.lang", 'Please enter the letters as they are shown in the image above.');?>
		<br/><?=Yii::t("CtgModule.lang", 'Letters are not case-sensitive.');?></div>
	</div>
	<?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t("CtgModule.lang", 'Submit')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php endif; ?>