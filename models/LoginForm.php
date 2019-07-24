<?php
namespace app\models;
use Yii;
use yii\base\Model;

use app\models\xUsuario;

/**
 * LoginForm es el modelo detras del formulario login.
 *
 * @property User|null $user Esta propiedad es de solo lectura.
 *
 */
class LoginForm extends Model{
    public $username;
    public $password;
    public $rememberMe = false;
    private $_user = false;


    /**
     * @return array Las reglas de validación
     */
    public function rules(){
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword( $attribute, $params ){
        if( !$this->hasErrors() ){
            $user = $this->getUser();
            if( !$user || !$user->validatePassword($this->password) ){
                $this->addError($attribute, 'Nombre de usuario o contraseña incorrecta.');
            }            
        }
    } // eof 
    
    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login(){
        if( $this->validate() ) return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        return false;
    } // eof 

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser(){
        if( $this->_user === false ) $this->_user = xUsuario::findByUsername($this->username);
        return $this->_user;
    } // eof 
    
} // class 
