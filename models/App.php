<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app".
 *
 * @property integer $id
 * @property string $fecha
 * @property string $aplicacion
 * @property string $proyecto
 * @property string $programador
 * @property string $contactos
 * @property integer $imp_fact
 * @property integer $imp_client
 * @property string $funcion
 * @property string $creacion
 * @property string $up
 * @property string $actualizada
 * @property string $framework
 * @property string $tecnologia
 * @property string $app_ip
 * @property string $app_path
 * @property string $app_user
 * @property string $app_users
 * @property string $app_passwd
 * @property string $app_passwds
 * @property string $app_bck_ip
 * @property string $app_bck_path
 * @property string $app_bck_user
 * @property string $app_bck_passwd
 * @property string $app_bck_name
 * @property string $app_mb
 * @property string $app_users_cant
 * @property string $app_reg
 * @property string $db_name
 * @property string $db_smdb
 * @property string $db_access
 * @property string $db_ip
 * @property string $db_path
 * @property string $db_user
 * @property string $db_passwd
 * @property string $db_passwds
 * @property string $db_bck_ip
 * @property string $db_bck_path
 * @property string $db_bck_name
 * @property string $lenguaje
 * @property string $riesgos
 * @property string $pendientes
 * @property string $ultima_error
 * @property string $w_cambios
 * @property string $path_app_db_config
 */
class App extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'a.app';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fecha'], 'safe'],
            [['imp_fact', 'imp_client'], 'integer'],
            [['aplicacion', 'programador', 'app_path', 'app_bck_path', 'db_path', 'db_bck_path'], 'string', 'max' => 128],
            [['proyecto', 'actualizada', 'app_users', 'app_passwds', 'lenguaje', 'riesgos'], 'string', 'max' => 512],
            [['contactos', 'funcion', 'tecnologia', 'pendientes', 'ultima_error', 'w_cambios'], 'string', 'max' => 1024],
            [['creacion', 'up', 'framework', 'app_bck_name'], 'string', 'max' => 64],
            [['app_ip', 'app_bck_ip', 'db_ip', 'db_bck_ip'], 'string', 'max' => 15],
            [['app_user', 'app_passwd', 'app_bck_user', 'app_bck_passwd', 'db_smdb', 'db_user', 'db_passwd'], 'string', 'max' => 16],
            [['app_mb', 'app_users_cant', 'app_reg', 'db_name', 'db_access', 'db_passwds', 'db_bck_name', 'path_app_db_config'], 'string', 'max' => 32],
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
            'aplicacion' => Yii::t('app', 'Aplicacion'),
            'proyecto' => Yii::t('app', 'Proyecto'),
            'programador' => Yii::t('app', 'Programador'),
            'contactos' => Yii::t('app', 'Contactos'),
            'imp_fact' => Yii::t('app', 'Imp Fact'),
            'imp_client' => Yii::t('app', 'Imp Client'),
            'funcion' => Yii::t('app', 'Funcion'),
            'creacion' => Yii::t('app', 'Creacion'),
            'up' => Yii::t('app', 'Up'),
            'actualizada' => Yii::t('app', 'Actualizada'),
            'framework' => Yii::t('app', 'Framework'),
            'tecnologia' => Yii::t('app', 'Tecnologia'),
            'app_ip' => Yii::t('app', 'App Ip'),
            'app_path' => Yii::t('app', 'App Path'),
            'app_user' => Yii::t('app', 'App User'),
            'app_users' => Yii::t('app', 'App Users'),
            'app_passwd' => Yii::t('app', 'App Passwd'),
            'app_passwds' => Yii::t('app', 'App Passwds'),
            'app_bck_ip' => Yii::t('app', 'App Bck Ip'),
            'app_bck_path' => Yii::t('app', 'App Bck Path'),
            'app_bck_user' => Yii::t('app', 'App Bck User'),
            'app_bck_passwd' => Yii::t('app', 'App Bck Passwd'),
            'app_bck_name' => Yii::t('app', 'App Bck Name'),
            'app_mb' => Yii::t('app', 'App Mb'),
            'app_users_cant' => Yii::t('app', 'App Users Cant'),
            'app_reg' => Yii::t('app', 'App Reg'),
            'db_name' => Yii::t('app', 'Db Name'),
            'db_smdb' => Yii::t('app', 'Db Smdb'),
            'db_access' => Yii::t('app', 'Db Access'),
            'db_ip' => Yii::t('app', 'Db Ip'),
            'db_path' => Yii::t('app', 'Db Path'),
            'db_user' => Yii::t('app', 'Db User'),
            'db_passwd' => Yii::t('app', 'Db Passwd'),
            'db_passwds' => Yii::t('app', 'Db Passwds'),
            'db_bck_ip' => Yii::t('app', 'Db Bck Ip'),
            'db_bck_path' => Yii::t('app', 'Db Bck Path'),
            'db_bck_name' => Yii::t('app', 'Db Bck Name'),
            'lenguaje' => Yii::t('app', 'Lenguaje'),
            'riesgos' => Yii::t('app', 'Riesgos'),
            'pendientes' => Yii::t('app', 'Pendientes'),
            'ultima_error' => Yii::t('app', 'Ultima Error'),
            'w_cambios' => Yii::t('app', 'W Cambios'),
            'path_app_db_config' => Yii::t('app', 'Path App Db Config'),
        ];
    }
}
