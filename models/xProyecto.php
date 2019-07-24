<?php

namespace app\models;
use Yii;
use app\models\Proyecto;
class xProyecto extends Proyecto{


    public function getId(){
        return $this->id;
    }


    public function getDe(){
        return $this->de;
    }

    public function getContacto(){
        return $this->contacto;
    }

    public function getEmail(){
        return $this->email;
    }
    
    public function getSt(){
        return $this->st;
    }

    public function getCodigo(){
        return $this->codigo;
    }



    
} // class

