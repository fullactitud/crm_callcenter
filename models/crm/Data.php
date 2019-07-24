<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "movistar.data".
 *
 * @property integer $id
 * @property integer $id_usuario
 * @property string $de
 * @property string $url
 * @property integer $acumulado
 * @property string $reg
 * @property integer $st
 */
class Data extends \yii\db\ActiveRecord{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'acumulado', 'st'], 'integer'],
            [['de'], 'required'],
            [['reg'], 'safe'],
            [['de', 'url'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_usuario' => 'Id Usuario',
            'de' => 'De',
            'url' => 'Url',
            'acumulado' => 'Acumulado',
            'reg' => 'Reg',
            'st' => 'St',
        ];
    }
}
