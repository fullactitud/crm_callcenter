<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario_perfil".
 *
 * @property integer $id
 * @property integer $id_usuario
 * @property integer $id_perfil
 */
class UsuarioPerfil extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'a.usuario_perfil';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_usuario', 'id_perfil'], 'required'],
            [['id_usuario', 'id_perfil'], 'integer'],
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
            'id_perfil' => Yii::t('app', 'Id Perfil'),
        ];
    }
}
