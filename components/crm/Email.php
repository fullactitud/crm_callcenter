<?php
namespace app\components\crm;

use Yii;


use app\models\Aux;
use yii\swiftmailer\Mailer;

/**
 * Funciones genericas para envio de correo
 */
class Email{
           

    /**
     * Envio de correo simple
     */
    public static function envioSimple( $receptor, $subject, $html, $emisor = null, $text = null ){
        if( $emisor == null ) $emisor = 'apps@callycall.com.ve';
        
        \Yii::$app->mail->compose()->setFrom($emisor)->setTo($receptor)
            ->setSubject($subject)->setTextBody($text)->setHtmlBody($html)
            ->send();
    } // for ######################################################
    

    /**
     * Envio de correo compuesto
     */    
    public function Enviaremail(){
        $op = 2;
        switch( $op ){
        case '1': // envio simple
            \Yii::$app->mail->compose()->setFrom('apps@callycall.com.ve')->setTo('develop.callycall@gmail.com')
                                                                ->setSubject('Email enviado desde Yii2-Swiftmailer t3 ')
                                                                ->setTextBody('Plain text content')
                                                                ->setHtmlBody('<b>HTML content</b>')
                                                                ->send();
            break;
        case '2': // plantilla
           
            $envio = \Yii::$app->mail->compose('@app/components/templates/mail/email01.html');
            $envio->setFrom('apps@callycall.com.ve')->setTo('develop.callycall@gmail.com')
                                           ->setSubject('prueba de plantilla')
                                           ->send(); 
            
            
            break;
        case '3': // abjunto
            $envio = \Yii::$app->mail->compose();
            $envio->setFrom('apps@callycall.com.ve')->setTo('develop.callycall@gmail.com');
            $envio->setSubject('Email enviado desde Yii2-Swiftmailer t3 ');
            $envio->setTextBody('Plain text content');
            $envio->setHtmlBody('<b>HTML content</b>');
            $envio->attach(Yii::$app->basePath .'/web/reportes/pdf/nada.pdf');
            $envio->send();
            break;

        case '4': // uso de 2 plantillas. NO PROBADO
            Yii::$app->mail->compose(['html' => '@app/mail-templates/html-email-01', 'text' => '@app/mail-templates/text-email-01'], [/* Parámetros para la vista */])
                                             ->setFrom('apps@callycall.com.ve')->setTo('develop.callycall@gmail.com')
                                             ->setSubject('2 plantillas')
                                             ->send();
            break;
            
            
        }
        
        return $this->render('txt',array('txt'=>''));
    } // eof #######################################################
    


    /**
     * Envio de correo compuesto
     */    
    public static function enviarArchivo( $de, $para, $titulo, $cuerpo, $archivo ){
        try{
            $envio = @Yii::$app->mail->compose()
                   ->setFrom($de)->setTo($para)
                   ->setSubject($titulo)->setTextBody($cuerpo)->setHtmlBody($cuerpo)
                   ->attach( Yii::$app->basePath .'/web/reportes/public/' .$archivo )
                   ->send();
            return true;
        }catch( Exception $e ){
            return false;
        }
    } // eof #######################################################
    
    

} // class
