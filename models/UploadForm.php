<?php
namespace app\models;




use yii\base\Model;
use yii\web\UploadedFile;




class UploadForm extends Model{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    public $foto;
    public $xls;
    public $nombre;







    
    public function rules(){
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['xls'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xls, xlsx, txt, csv'],
        ];
    } // eof #######################################################







    
    public function upload(){
        if( $this->validate() ){
            $this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        }else{
            return false;
        }
    } // eof #######################################################







    
    public function uploadFoto(){
        if( $this->validate() ){
            $this->imageFile->saveAs('uploads/foto/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        }else{
            return false;
        }
    } // eof #######################################################




    public function uploadLogo_deprecate(){
        if( $this->validate() ){
            $this->nombre = date('Ymd_his') .'_'; 
            $this->imageFile->saveAs('uploads/logo/' .$this->nombre);
            return true;
        }else{
            return false;
        }
    } // eof #######################################################


    
    public function uploadXLS(){
        if( $this->validate() ){
            $aux = str_replace('.', '', strtolower(trim($this->xls->baseName)));

            $aux = str_replace('Á','a',$aux);
            $aux = str_replace('É','e',$aux);
            $aux = str_replace('Í','i',$aux);
            $aux = str_replace('Ó','o',$aux);
            $aux = str_replace('Ú','u',$aux);
            $aux = str_replace('á','a',$aux);
            $aux = str_replace('é','e',$aux);
            $aux = str_replace('í','i',$aux);
            $aux = str_replace('ó','o',$aux);
            $aux = str_replace('ú','u',$aux);
            $aux = str_replace('Ñ','gn',$aux);
            $aux = str_replace('ñ','gn',$aux);

            $aux = str_replace('Ü','u',$aux);
            $aux = str_replace('ü','u',$aux);
            
            $aux = str_replace(' ','',$aux);
            $aux = str_replace('y','_',$aux);
            $aux = str_replace('#','',$aux);
            $aux = str_replace('&','',$aux);
            $aux = str_replace(';','',$aux);
            $aux = str_replace('-','_',$aux);
            $aux = str_replace('__','_',$aux);
            $aux = str_replace('__','_',$aux);


            
            
            
            $this->nombre = date('Ymd_his') .'_' .$aux .'.' .$this->xls->extension;
            $this->xls->saveAs('uploads/data/' .$this->nombre);
            return true;
        }else{
            return false;
        }
    } // eof #######################################################

    
}
