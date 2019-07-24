<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $id
 * @property string $fecha
 * @property string $proyecto
 * @property string $aplicacion
 * @property string $programador
 * @property string $contacto
 * @property string $incidencia
 * @property string $creacion
 * @property string $up
 * @property string $solucion
 * @property string $cambios
 * @property double $horas
 * @property string $pendientes
 * @property string $responsabilidad
 * @property string $recomendacion
 * @property integer $st
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fecha'], 'safe'],
            [['horas'], 'number'],
            [['st'], 'integer'],
            [['proyecto'], 'string', 'max' => 512],
            [['aplicacion', 'programador'], 'string', 'max' => 128],
            [['contacto', 'incidencia', 'solucion', 'cambios', 'pendientes', 'responsabilidad', 'recomendacion'], 'string', 'max' => 1024],
            [['creacion', 'up'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fecha' => Yii::t('app', 'Fecha'),
            'proyecto' => Yii::t('app', 'Proyecto'),
            'aplicacion' => Yii::t('app', 'Aplicacion'),
            'programador' => Yii::t('app', 'Programador'),
            'contacto' => Yii::t('app', 'Contacto'),
            'incidencia' => Yii::t('app', 'Incidencia'),
            'creacion' => Yii::t('app', 'Creacion'),
            'up' => Yii::t('app', 'Up'),
            'solucion' => Yii::t('app', 'Solucion'),
            'cambios' => Yii::t('app', 'Cambios'),
            'horas' => Yii::t('app', 'Horas'),
            'pendientes' => Yii::t('app', 'Pendientes'),
            'responsabilidad' => Yii::t('app', 'Responsabilidad'),
            'recomendacion' => Yii::t('app', 'Recomendacion'),
            'st' => Yii::t('app', 'St'),
        ];
    }
}
