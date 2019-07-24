<?php
namespace app\components\crm;
use Yii;

use app\components\crm\Request;
use app\components\crm\Mensaje;
use app\components\crm\Ayuda;
use app\components\crm\Listado;
use app\components\crm\Usuario;
use app\models\Aux;

/**
 * Clase helper para llamadas
 */
class Llamada{
    
    /**
     * Listado de llamadas
     */
    public static function listado(){
        $sqlU = '';
        $i = 1;
        $dato[] = ' &nbsp; ';
        $dato[] = 'Proyectos';
        $dato[] = 'Instrumento';
        $dato[] = 'ID Llamada';
        $dato[] = 'Fecha';
        $dato[] = 'Duración';
        $dato[] = 'Emisor';
        $dato[] = 'Receptor';
        $dato[] = 'Colgar';
        $data[] = $dato; unset($dato);
        
        $id_usuario = Usuario::id();
        if( $id_usuario != 1 )
            $sqlU = " and l.id_usuario = '" .$id_usuario ."' ";
        
        $proyectos = Aux::findBySql("select * from a.proyecto where st != '0'")->all();
        foreach( $proyectos as $proyecto ){
            $instrumentos = Aux::findBySql("select * from " .$proyecto->codigo .".instrumento where st !='0' order by id asc;")->all();
            foreach( $instrumentos as $instrumento ){
                $aux0 = Aux::findBySql("select columna from " .$proyecto->codigo .".cabecera c where c.id_instrumento='" .$instrumento->id ."' and c.st='1' order by c.id asc limit 1;")->one();
                
                $sql = "select l.id as id_llamada, u.nombres || ' ' || u.apellidos as emisor, p.c004 as receptor, s.de as estatus, l.reg as inicio, justify_interval(( CURRENT_TIMESTAMP - l.reg)) as duracion
from " .$proyecto->codigo .".llamada l
inner join a.usuario u on u.id = l.id_usuario 
inner join a.estatus s on s.id = l.st
inner join " .$proyecto->codigo .".prospecto p on p.id = l.id_prospecto
where l.st not in ('0','5','6') and p.id_instrumento='" .$instrumento->id ."' ;";
                $llamadas = Aux::findBySql($sql)->all();
                foreach( $llamadas as $llamada ){
                    $dato[] = $i++;
                    $dato[] = $proyecto->de;
                    $dato[] = $instrumento->de;
                    $dato[] = $llamada->id_llamada;
                    $dato[] = $llamada->inicio;
                    $dato[] = $llamada->duracion;
                    $dato[] = $llamada->emisor;
                    $dato[] = $llamada->receptor;
                    $dato[] = '<a href="javascript:if( confirm(\'Colgar a: ' .$llamada->receptor .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/llamada/colgar&cod_proyecto=' .$proyecto->codigo .'&id_instrumento=' .$instrumento->id .'&id=' .$llamada->id_llamada .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/colgar.png" style="width:1.5em;"  title="Colgar a ' .$llamada->receptor .'"/></a>';
                    $data[] = $dato; unset($dato);
                } // foreach de llamadas
                unset($llamadas);
            } // foreach de intrumentos
            unset($instrumentos);
        } // foreach de proyectos
        unset($proyectos);
        return Listado::listado( $data );
    } // eof 
    


    /**
     * Proceso que finaliza una llamada
     */
    public static function colgar(){
        
        $cod_proyecto = Request::rq('cod_proyecto');
        $id_instrumento = (int)Request::rq('id_instrumento');
        $id = (int)Request::rq('id');
        
        $llamada = Aux::findBySql("select * from " .$cod_proyecto .".llamada where id='" .$id ."';")->one();
        Aux::findBySql("update " .$cod_proyecto .".llamada set st='5', id_tipificacion='3' where id='" .$llamada->id ."';")->one();
        Aux::findBySql("delete from " .$cod_proyecto .".usuario_prospecto where id_prospecto='" .$llamada->id_prospecto ."' and id_usuario='" .$llamada->id_usuario ."';")->one();
        Aux::findBySql("update " .$cod_proyecto .".prospecto set st='2', id_tipificacion='3', up=now() where st='4' and tlo='" .$llamada->id_usuario ."' and id='" .$llamada->id_prospecto ."';")->one();
        
        return 'Llamada finalizada';
    } // eof     
    

    
} // class

