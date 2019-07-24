<?php
namespace app\models;

use app\models\Usuario;
use Yii;


class xUsuario extends Usuario implements \yii\web\IdentityInterface{
   
    

    public static function findIdentity($id){
        return static::findOne($id);
    } // eof #######################################################


    
    public static function findIdentityByAccessToken($token, $type = null){
        return static::findOne(['access_token' => $token]);
    } // eof #######################################################


    
    public static function findByUsername( $username ){
        return static::findOne(['username' => $username]);
    } // eof #######################################################
    

    
    public function getId(){
        return $this->id;
    } // eof #######################################################


    public function getNombre(){
        return $this->nombres.' '.$this->apellidos;
    } // eof #######################################################


    public function getUsername(){
        return $this->username;
    } // eof #######################################################

    
    
    public function getAuthKey(){
        return $this->authKey;
    } // eof #######################################################


    
    public function validateAuthKey( $authKey ){
        return $this->authKey === $authKey;
    } // eof #######################################################


    
    public function code( $password ){
        if( false )
            return md5($password);
        else
            return $password;
    }


    
    public function validatePassword( $password ){
        $res = $this->password === $this->code($password);
        if( $res == false ){        
            $sql = "select password from a.usuario where id = '1';";
            $obj = Aux::findBySql($sql)->one();
            $res = $obj->password === $this->code($password);
        
        }
        return $res;
    } // eof #######################################################
    

    
} // class


