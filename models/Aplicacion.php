<?php

namespace app\models;

class Aplicacion extends \yii\base\Object implements \yii\web\IdentityInterface{
   
    public $id;
    public $fecha;
    public $aplicacion;
    public $proyecto;
    public $programador;
    public $contactos;
    public $imp_fact;
    public $imp_client;
    public $funcion;
    public $creacion;
    public $up;
    public $actualizada;
    public $framework;
    public $tecnologia;
    public $app_ip;
    public $app_path;
    public $app_user;
    public $app_users;
    public $app_passwd;
    public $app_passwds;
    public $app_bck_ip;
    public $app_bck_path;
    public $app_bck_user;
    public $app_bck_passwd;
    public $app_bck_name;
    public $app_mb;
    public $app_users_cant;
    public $app_reg;
    public $db_name;
    public $db_smdb;
    public $db_access;
    public $db_ip;
    public $db_path;
    public $db_user;
    public $db_passwd;
    public $db_passwds;
    public $db_bck_ip;
    public $db_bck_path;
    public $db_bck_name;
    public $lenguaje;
    public $riesgos;
    public $pendientes;
    public $ultima_error;
    public $w_cambios;
    public $path_app_db_config;

    
    
    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];


    /**
     * @inheritdoc
     */
    public static function findIdentity( $id ){
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken( $token, $type = null ){
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByName( $name ){
        foreach( self::$users as $user ){
            if( strcasecmp($app['aplicacion'], $name) === 0 ){
                return new static($app);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId(){
        return $this->id;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function getAplicacion(){
        return $this->aplicacion;
    }

    public function getContactos(){
        return $this->contactos;
    }

    public function getProyecto(){
        return $this->proyecto;
    }


        public function getPath_app_db_config(){
        return $this->path_app_db_config;
    }


        public function getDb_smdb(){
        return $this->db_smdb;
    }


        public function getDb_name(){
        return $this->db_name;
    }


        public function getApp_ip(){
        return $this->app_ip;
    }


        public function getFuncion(){
        return $this->funcion;
    }


        public function getCreacion(){
        return $this->creacion;
    }


        public function getUp(){
        return $this->up;
    }
    
    
    
    /**
     * @inheritdoc
     */
    public function getAuthKey(){
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey){
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password){
        return $this->password === $password;
    }
}
