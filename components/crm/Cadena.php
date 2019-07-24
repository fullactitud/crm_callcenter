<?php
namespace app\components\crm;

use Yii;

use app\models\Proyecto;
use app\models\Usuario;
use app\models\Perfil;

use app\models\xMenu;
use app\models\xProyecto;
use app\models\xUsuario;
use app\models\xPerfil;
use app\models\Aux;


/**
 * Clase helper para CADENAS
 */
class Cadena{



    /**
     * Completa el nombre de la columna
     */    
    public static function columna($i){
        $cad = 'c' .self::set0( $i, '2' );
        return $cad;
    } // eof #######################################################







    /**
     * Completas los ceros a la izquierda
     */
    public static function set0( $valor, $ceros = '2' ){
        switch( $ceros ){
        case '1':
            if( (int)$valor < 10 )
                $valor = '0' .$valor;
            break;
        case '3':
            if( (int)$valor < 10 )
                $valor = '000' .$valor;
            else if( (int)$valor < 100 )
                $valor = '00' .$valor;
            else if( (int)$valor < 1000 )
                $valor = '0' .$valor;
            break;            
        default:
            if( (int)$valor < 10 )
                $valor = '00' .$valor;
            else if( (int)$valor < 100 )
                $valor = '0' .$valor;
            break;
        }
        return $valor;
    } // eof #######################################################





    /**
     * Convierte una cadena normal en una cadena identidicador,
     * que es usada para identificar elementos html
     */
    public static function texto2id( $cad ){
        $cad = str_replace(' ','',strtolower(trim($cad)));
        $cad = str_replace("\t",'',$cad);
        $cad = str_replace("\n",'',$cad);
        return $cad;
    } // eof #######################################################
    



    

    /**
     * Elimina caracteres de escape
     */
    public static function validar( $cad ){
        $cad = str_replace("'", '', $cad);
        $cad = str_replace('\\', '/', $cad);
        return $cad;
    } // eof #######################################################

    

    



    /**
     * Reduce el tamaño de un texto
     */    
    public static function acortar( $cad ){
        $cant = strlen($cad);
        $c = max(((1.00 - ($cant*0.015)/150) +0.27),1.00) ;   
        $cad = '<span style="font-size: '.$c.'em;">' .$cad .'</span>';
        return $cad;
    } // eof ##################################################

        



        







    


    /**
     * Decodificación
     * Se usa para NO pasar el nombre de la tabla y campos por mensajes POST y GET
     * @param integer 
     * @return string Nombre de la tabla o campo 
     */
    public static function dcd( $id ){
        $vct = array();

        $pry = 'test';
        
        // TABLAS ////////////////
        $vct['110'] = 'a.app';
        $vct['111'] = 'a.estatus';
        $vct['112'] = 'a.menu';
        $vct['113'] = 'a.modulo';
        $vct['114'] = 'a.modulo_perfil';
        $vct['115'] = 'a.modulo_tp';
        $vct['116'] = 'a.perfil';
        $vct['117'] = 'a.proyecto';
        $vct['118'] = '';
        $vct['119'] = 'a.usuario';
        $vct['120'] = 'a.usuario_perfil';
        $vct['121'] = 'a.usuario_proyecto';
        
        
        $vct['211'] = $pry .'.agenda';
        $vct['212'] = $pry .'.agenda_cliente';
        $vct['213'] = $pry .'.archivo';
        $vct['214'] = $pry .'.archivo_encuesta';
        $vct['215'] = $pry .'.cabecera';
        $vct['216'] = $pry .'.cliente';
        $vct['217'] = $pry .'.cliente_opcion';
        $vct['218'] = $pry .'.cliente_usuario';
        $vct['219'] = $pry .'.data1';
        $vct['220'] = $pry .'.data2';	
        $vct['221'] = $pry .'.data3';
        $vct['222'] = $pry .'.data4';
        $vct['223'] = $pry .'.data5';
        $vct['224'] = $pry .'.data6';
        $vct['225'] = $pry .'.data7';
        $vct['226'] = $pry .'.data8';
        $vct['227'] = $pry .'.encuesta';
        $vct['228'] = $pry .'.encuesta_barrida';
        $vct['229'] = $pry .'.estatus';
        $vct['230'] = $pry .'.llamada';	
        $vct['231'] = $pry .'.llamada_luego';		
        $vct['234'] = $pry .'.opcion';
        $vct['236'] = $pry .'.plan';
        $vct['237'] = $pry .'.pregunta';
        $vct['239'] = $pry .'.pregunta_opcion';	
        $vct['243'] = $pry .'.tipificacion';
        $vct['244'] = $pry .'.tipificacion_pregunta';
        $vct['245'] = $pry .'.tipificacion_pregunta_opcion';
        $vct['246'] = $pry .'.tipo_encuesta';
        $vct['247'] = $pry .'.tipo_modulo';
        $vct['248'] = $pry .'.tipo_opcion';
        $vct['249'] = $pry .'.tipo_pregunta';
        $vct['250'] = $pry .'.usuario_encuesta';
        
        // CAMPOS ////////////////        
        $vct['501'] = 'id';
        $vct['502'] = 'de';
        $vct['503'] = 'st';
        $vct['504'] = 'reg';
        $vct['505'] = 'id_usuario';
        $vct['506'] = 'id_proyecto';
        $vct['507'] = 'id_encuesta';
        $vct['508'] = 'id_archivo';
        $vct['509'] = '';
        $vct['510'] = '';
        $vct['511'] = '';
        $vct['512'] = '';
        $vct['513'] = '';
        $vct['514'] = '';
        $vct['515'] = '';
        $vct['516'] = '';
        $vct['517'] = '';
        $vct['518'] = '';
        
        
        $vct['601'] = 'c01'; $vct['602'] = 'c02'; $vct['603'] = 'c03'; $vct['604'] = 'c04'; $vct['605'] = 'c05';
        $vct['606'] = 'c06'; $vct['607'] = 'c07'; $vct['608'] = 'c08'; $vct['609'] = 'c09'; $vct['610'] = 'c10';
        $vct['611'] = 'c11'; $vct['612'] = 'c12'; $vct['613'] = 'c13'; $vct['614'] = 'c14'; $vct['615'] = 'c15';
        $vct['616'] = 'c16'; $vct['617'] = 'c17'; $vct['618'] = 'c18'; $vct['619'] = 'c19'; $vct['620'] = 'c20';
        $vct['621'] = 'c21'; $vct['622'] = 'c22'; $vct['623'] = 'c23'; $vct['624'] = 'c24'; $vct['625'] = 'c25';
        $vct['626'] = 'c26'; $vct['627'] = 'c27'; $vct['628'] = 'c28'; $vct['629'] = 'c29'; $vct['630'] = 'c30';
        $vct['631'] = 'c31'; $vct['632'] = 'c32'; $vct['633'] = 'c33'; $vct['634'] = 'c34'; $vct['635'] = 'c35';
        $vct['636'] = 'c36'; $vct['637'] = 'c37'; $vct['638'] = 'c38'; $vct['639'] = 'c39'; $vct['640'] = 'c40';
        $vct['641'] = 'c41'; $vct['642'] = 'c42'; $vct['643'] = 'c43'; $vct['644'] = 'c44'; $vct['645'] = 'c45';
        $vct['646'] = 'c46'; $vct['647'] = 'c47'; $vct['648'] = 'c48'; $vct['649'] = 'c49'; $vct['650'] = 'c50';
        $vct['651'] = 'c51'; $vct['652'] = 'c52'; $vct['653'] = 'c53'; $vct['654'] = 'c54'; $vct['655'] = 'c55';
        $vct['656'] = 'c56'; $vct['657'] = 'c57'; $vct['658'] = 'c58'; $vct['659'] = 'c59'; $vct['660'] = 'c60';
        $vct['661'] = 'c61'; $vct['662'] = 'c62'; $vct['663'] = 'c63'; $vct['664'] = 'c64'; $vct['665'] = 'c65';
        $vct['666'] = 'c66'; $vct['667'] = 'c67'; $vct['668'] = 'c68'; $vct['669'] = 'c69'; $vct['670'] = 'c70';
        $vct['671'] = 'c71'; $vct['672'] = 'c72'; $vct['673'] = 'c73'; $vct['674'] = 'c74'; $vct['675'] = 'c75';
        $vct['676'] = 'c76'; $vct['677'] = 'c77'; $vct['678'] = 'c78'; $vct['679'] = 'c79'; $vct['680'] = 'c80';
        $vct['681'] = 'c81'; $vct['682'] = 'c82'; $vct['683'] = 'c83'; $vct['684'] = 'c84'; $vct['685'] = 'c85';
        $vct['686'] = 'c86'; $vct['687'] = 'c87'; $vct['688'] = 'c88'; $vct['689'] = 'c89'; $vct['690'] = 'c90';
        $vct['691'] = 'c91'; $vct['692'] = 'c92'; $vct['693'] = 'c93'; $vct['694'] = 'c94'; $vct['695'] = 'c95';
        $vct['696'] = 'c96'; $vct['697'] = 'c97'; $vct['698'] = 'c98'; $vct['699'] = 'c99';
        
        
        if( array_key_exists($id,$vct) )
            return $vct[$id];
        else
            return $id;
    } // eof #######################################################
    
} // class
