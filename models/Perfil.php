<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "perfil".
 *
 * @property integer $id
 * @property string $de
 * @property integer $padre
 * @property integer $st
 */
class Perfil extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'a.perfil';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['padre', 'st'], 'integer'],
            [['de'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'de' => Yii::t('app', 'De'),
            'padre' => Yii::t('app', 'Padre'),
            'st' => Yii::t('app', 'St'),
        ];
    }
}
