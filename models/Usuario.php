<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "a.usuario".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $nombres
 * @property string $apellidos
 * @property string $email
 * @property integer $st
 * @property string $repassword
 * @property string $lastlogintime
 * @property string $created
 * @property string $up
 * @property string $foto
 * @property string $documento
 * @property string $ente
 *
 * @property AUsuarioPerfil[] $aUsuarioPerfils
 * @property AUsuarioProyecto[] $aUsuarioProyectos
 */
class Usuario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'a.usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'nombres'], 'required'],
            [['st'], 'integer'],
            [['repassword', 'lastlogintime'], 'string'],
            [['created', 'up'], 'safe'],
            [['username', 'password'], 'string', 'max' => 64],
            [['nombres', 'apellidos', 'email'], 'string', 'max' => 128],
            [['foto'], 'string', 'max' => 256],
            [['documento'], 'string', 'max' => 32],
            [['ente'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'nombres' => 'Nombres',
            'apellidos' => 'Apellidos',
            'email' => 'Email',
            'st' => 'St',
            'repassword' => 'Repassword',
            'lastlogintime' => 'Lastlogintime',
            'created' => 'Created',
            'up' => 'Up',
            'foto' => 'Foto',
            'documento' => 'Documento',
            'ente' => 'Ente',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAUsuarioPerfils()
    {
        return $this->hasMany(AUsuarioPerfil::className(), ['id_usuario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAUsuarioProyectos()
    {
        return $this->hasMany(AUsuarioProyecto::className(), ['id_usuario' => 'id']);
    }
}
