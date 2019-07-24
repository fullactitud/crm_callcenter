<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tarea".
 *
 * @property integer $id
 * @property integer $id_usuario
 * @property string $proyecto
 * @property string $actividad1
 * @property resource $descripcion1
 * @property string $actividad2
 * @property resource $descripcion2
 * @property resource $obs
 * @property integer $st
 */
class Tarea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tarea';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'st'], 'integer'],
            [['descripcion1', 'descripcion2', 'obs'], 'string'],
            [['proyecto', 'actividad1', 'actividad2'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_usuario' => Yii::t('app', 'Id Usuario'),
            'proyecto' => Yii::t('app', 'Proyecto'),
            'actividad1' => Yii::t('app', 'Actividad1'),
            'descripcion1' => Yii::t('app', 'Descripcion1'),
            'actividad2' => Yii::t('app', 'Actividad2'),
            'descripcion2' => Yii::t('app', 'Descripcion2'),
            'obs' => Yii::t('app', 'Obs'),
            'st' => Yii::t('app', 'St'),
        ];
    }
}
