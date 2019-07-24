<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servidor".
 *
 * @property integer $id
 * @property string $de
 * @property string $ip1
 * @property string $ip2
 * @property string $ip3
 * @property string $ip4
 * @property string $procesador
 * @property double $mem
 * @property double $swap
 * @property string $linux
 * @property string $mysql
 * @property string $postgresql
 * @property string $golang
 * @property string $php
 * @property integer $emacs
 * @property string $apache
 */
class Servidor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'servidor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mem', 'swap'], 'number'],
            [['emacs'], 'integer'],
            [['de'], 'string', 'max' => 512],
            [['ip1', 'ip2', 'ip3', 'ip4'], 'string', 'max' => 15],
            [['procesador', 'linux'], 'string', 'max' => 128],
            [['mysql', 'postgresql', 'golang', 'php', 'apache'], 'string', 'max' => 32],
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
            'ip1' => Yii::t('app', 'Ip1'),
            'ip2' => Yii::t('app', 'Ip2'),
            'ip3' => Yii::t('app', 'Ip3'),
            'ip4' => Yii::t('app', 'Ip4'),
            'procesador' => Yii::t('app', 'Procesador'),
            'mem' => Yii::t('app', 'Mem'),
            'swap' => Yii::t('app', 'Swap'),
            'linux' => Yii::t('app', 'Linux'),
            'mysql' => Yii::t('app', 'Mysql'),
            'postgresql' => Yii::t('app', 'Postgresql'),
            'golang' => Yii::t('app', 'Golang'),
            'php' => Yii::t('app', 'Php'),
            'emacs' => Yii::t('app', 'Emacs'),
            'apache' => Yii::t('app', 'Apache'),
        ];
    }
}
