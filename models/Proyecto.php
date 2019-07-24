<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proyecto".
 *
 * @property integer $id
 * @property string $de
 * @property string $contacto
 * @property string $email
 * @property integer $st
 * @property string $codigo
 * @property string $pais
 *
 * @property Encuesta[] $encuestas
 * @property UsuarioProyecto[] $usuarioProyectos
 */
class Proyecto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'a.proyecto';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['de', 'contacto', 'email'], 'required'],
            [['st'], 'integer'],
            [['de'], 'string', 'max' => 64],
            [['contacto', 'email', 'codigo'], 'string', 'max' => 128],
            [['pais'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'de' => 'De',
            'contacto' => 'Contacto',
            'email' => 'Email',
            'st' => 'St',
            'codigo' => 'Codigo',
            'pais' => 'Pais',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEncuestas()
    {
        return $this->hasMany(Encuesta::className(), ['id_proyecto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioProyectos()
    {
        return $this->hasMany(UsuarioProyecto::className(), ['id_proyecto' => 'id']);
    }
}
