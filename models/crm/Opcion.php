<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "test.opcion".
 *
 * @property integer $id
 * @property integer $id_entrada
 * @property string $de
 * @property string $valor
 * @property integer $st
 * @property integer $ir_a
 *
 * @property TestClienteOpcion[] $testClienteOpcions
 * @property TestEntrada $idEntrada
 * @property TestPreguntaOpcion[] $testPreguntaOpcions
 * @property TestTipificacionPreguntaOpcion[] $testTipificacionPreguntaOpcions
 */
class Opcion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.opcion';
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
            'id_entrada' => 'Id Entrada',
            'de' => 'De',
            'valor' => 'Valor',
            'st' => 'St',
            'ir_a' => 'Ir A',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestClienteOpcions()
    {
        return $this->hasMany(TestClienteOpcion::className(), ['opcion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdEntrada()
    {
        return $this->hasOne(TestEntrada::className(), ['id' => 'id_entrada']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestPreguntaOpcions()
    {
        return $this->hasMany(TestPreguntaOpcion::className(), ['opcion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestTipificacionPreguntaOpcions()
    {
        return $this->hasMany(TestTipificacionPreguntaOpcion::className(), ['opcion_id' => 'id']);
    }
}
