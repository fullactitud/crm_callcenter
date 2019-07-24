<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "movistar.cuota".
 *
 * @property integer $id
 * @property integer $id_instrumento
 * @property integer $id_dominio
 * @property string $fecha_atencion
 * @property string $fecha_ref
 * @property integer $cuota
 * @property string $reg
 * @property string $up
 * @property integer $st
 */
class Cuota extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.cuota';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_instrumento'], 'required'],
            [['id_instrumento', 'id_dominio', 'cuota', 'st'], 'integer'],
            [['fecha_atencion', 'fecha_ref', 'reg', 'up'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_instrumento' => 'Id Instrumento',
            'id_dominio' => 'Id Dominio',
            'fecha_atencion' => 'Fecha Atencion',
            'fecha_ref' => 'Fecha Ref',
            'cuota' => 'Cuota',
            'reg' => 'Reg',
            'up' => 'Up',
            'st' => 'St',
        ];
    }
}
