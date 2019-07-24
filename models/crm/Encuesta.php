<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "test.encuesta".
 *
 * @property integer $id
 * @property integer $id_encuesta_tp
 * @property string $de
 * @property integer $barrida
 * @property integer $st
 *
 * @property TestAgenda[] $testAgendas
 * @property TestArchivoEncuesta[] $testArchivoEncuestas
 * @property TestCliente[] $testClientes
 * @property TestTipoEncuesta $idEncuestaTp
 * @property TestEncuestaBarrida[] $testEncuestaBarridas
 * @property TestEntrada[] $testEntradas
 * @property TestPreguntaPadre[] $testPreguntaPadres
 * @property TestTipificacionPregunta[] $testTipificacionPreguntas
 * @property TestUsuarioEncuesta[] $testUsuarioEncuestas
 */
class Encuesta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.encuesta';
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
            'id_encuesta_tp' => 'Tipo',
            'de' => 'Descripción',
            'barrida' => 'Barrida',
            'st' => 'Estatus',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestAgendas()
    {
        return $this->hasMany(TestAgenda::className(), ['encuesta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestArchivoEncuestas()
    {
        return $this->hasMany(TestArchivoEncuesta::className(), ['encuesta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestClientes()
    {
        return $this->hasMany(TestCliente::className(), ['encuesta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEncuestaTp()
    {
        return $this->hasOne(TestTipoEncuesta::className(), ['id' => 'id_encuesta_tp']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestEncuestaBarridas()
    {
        return $this->hasMany(TestEncuestaBarrida::className(), ['encuesta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestEntradas()
    {
        return $this->hasMany(TestEntrada::className(), ['id_encuesta' => 'id']);
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestTipificacionPreguntas()
    {
        return $this->hasMany(TestTipificacionPregunta::className(), ['encuesta_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestUsuarioEncuestas()
    {
        return $this->hasMany(TestUsuarioEncuesta::className(), ['encuesta_id' => 'id']);
    }
}
