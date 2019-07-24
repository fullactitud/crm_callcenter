<?php
use yii\widgets\ActiveForm;

$form = ActiveForm::begin(['action' =>[$action],'options' => ['enctype' => 'multipart/form-data']]);
echo '<input type="hidden" id="x3proy" name="x3proy" value="' .$proyecto .'"/>';
echo '<input type="hidden" id="x3inst" name="x3inst" value="' .$instrumento .'"/>';

echo $txt;
if( false )
    echo $form->field($model, 'xls')->fileInput(['multiple' => true, 'accept' => 'image/*']);
else
    echo $form->field($model, 'xls')->fileInput(['multiple' => false, 'accept' => '*']);
echo '<center><button> Subir Archivo </button></center>';
ActiveForm::end();

echo '<br /><br />' .$list;

?>