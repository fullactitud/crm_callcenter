<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "movistar.dominio".
 *
 * @property integer $id
 * @property integer $id_parent
 * @property string $de
 * @property string $reg
 * @property integer $st
 */
class Dominio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movistar.dominio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_parent', 'st'], 'integer'],
            [['de'], 'required'],
            [['reg'], 'safe'],
            [['de'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_parent' => 'Id Parent',
            'de' => 'De',
            'reg' => 'Reg',
            'st' => 'St',
        ];
    }
}
