<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "test.cabecera".
 *
 * @property integer $id
 * @property integer $id_archivo
 * @property string $nombre
 * @property string $columna
 * @property integer $orden
 * @property integer $st
 */
class Cabecera extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.cabecera';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_archivo', 'nombre', 'columna'], 'required'],
            [['id_archivo', 'orden', 'st'], 'integer'],
            [['nombre'], 'string', 'max' => 128],
            [['columna'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_archivo' => 'Id Archivo',
            'nombre' => 'Nombre',
            'columna' => 'Columna',
            'orden' => 'Orden',
            'st' => 'St',
        ];
    }
}
