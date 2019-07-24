<?php

namespace app\models\crm;

use Yii;

/**
 * This is the model class for table "movistar.instrumento".
 *
 * @property integer $id
 * @property integer $id_instrumento_tp
 * @property string $de
 * @property integer $barrida
 * @property integer $st
 */
class Instrumento extends \yii\db\ActiveRecord{
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return 'test.instrumento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_instrumento_tp', 'de'], 'required'],
            [['id_instrumento_tp', 'barrida', 'st'], 'integer'],
            [['de'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_instrumento_tp' => 'Id Instrumento Tp',
            'de' => 'De',
            'barrida' => 'Barrida',
            'st' => 'St',
        ];
    }
}
