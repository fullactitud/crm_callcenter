<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "test.entrada".
 *
 * @property integer $id
 * @property integer $id_encuesta
 * @property integer $id_pregunta_tp
 * @property string $de
 * @property string $codigo
 * @property integer $st
 * @property integer $ir_a
 *
 * @property TestClienteOpcion[] $testClienteOpcions
 * @property TestEncuesta $idEncuesta
 * @property TestTipoPregunta $idPreguntaTp
 * @property TestOpcion[] $testOpcions
 * @property TestPreguntaHijo[] $testPreguntaHijos
 * @property TestPreguntaPadre[] $testPreguntaPadres
 * @property TestTipificacionPregunta[] $testTipificacionPreguntas
 */
class Entrada extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.entrada';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_encuesta' => 'Id Encuesta',
            'id_pregunta_tp' => 'Id Pregunta Tp',
            'de' => 'De',
            'codigo' => 'Codigo',
            'st' => 'St',
            'ir_a' => 'Ir A',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestClienteOpcions()
    {
        return $this->hasMany(TestClienteOpcion::className(), ['pregunta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEncuesta()
    {
        return $this->hasOne(TestEncuesta::className(), ['id' => 'id_encuesta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPreguntaTp()
    {
        return $this->hasOne(TestTipoPregunta::className(), ['id' => 'id_pregunta_tp']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestOpcions()
    {
        return $this->hasMany(TestOpcion::className(), ['id_entrada' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestPreguntaHijos()
    {
        return $this->hasMany(TestPreguntaHijo::className(), ['pregunta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestPreguntaPadres()
    {
        return $this->hasMany(TestPreguntaPadre::className(), ['pregunta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestTipificacionPreguntas()
    {
        return $this->hasMany(TestTipificacionPregunta::className(), ['pregunta_id' => 'id']);
    }
}
