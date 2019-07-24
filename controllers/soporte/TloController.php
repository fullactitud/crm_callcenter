<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\controllers\crm\CrmController;

use app\models\UploadForm;
use yii\web\UploadedFile;
use app\models\crm\Prospecto;
use app\models\movistar\ProspectoMovistar;

use app\components\crm\Usuario;
use app\components\crm\Mensaje;
use app\components\crm\Menu;
use app\components\crm\Request;
use app\components\crm\X3html;
use app\components\crm\Email;
use app\components\crm\Ayuda;
use app\components\crm\Listado;
use app\models\Aux;


/**
* Controler para amnejar TLOs
*/
class TloController extends CrmController{
 
    /**
     * Proecsos que se ejecutan antes que el controller carge
     */
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } 

    /**
     * Acción por defecto
     */
    public function actionIndex(){
        $id_usuario = Usuario::id(); // \Yii::$app->user->identity->id;
        $rol = null;
        $vector[] = 'admin';
        $vector[] = 'soporte';
        $vector[] = 'supervisor';
        $vector[] = 'tlo';
        $vector[] = 'activador';    
        $vector[] = 'cliente';
        $i = 0;
        $perfiles = Aux::findBySql("select item_name from public.auth_assignment where user_id ='" .(int)$id_usuario ."';")->all();
        while( is_null($rol) && $i < count($vector) ){
            foreach( $perfiles as $perfil ){
                if( $perfil->item_name ==  $vector[$i] ){
                    $rol = 'admin';
                    break;
                }
            }
            $i++;
        }

        switch( $rol ){
        case 'admin':
        case 'soporte':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/tlo&', 302);
            break;
        case 'supervisor':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/supervisor&', 302);
            break;
        case 'activador':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/tlo&', 302);
            break;
        case 'cliente':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/cliente&', 302);
            break;
        case 'tlo':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/tlo&', 302);
        default:
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=df/logout&', 302);
            break;
        }
            
    } // eof 


    
    /**
     * Despliega las opciones para supervisor
     */
    public function actionSupervisor(){
        $cadB = '';
        $height = '2.0em';
        $height2 = '3.0em';
        
        $cadB .= Ayuda::toHtml('panel');
        
        $cadB .= '<div class="row"><hr /><div class="subtitulos col-sm-12"> &nbsp; Herramientas </div><br /><br /></div>';  
        $cadB .= '<div class="btn-group btn-group-justified">';
        
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/df" class="btn btn-default" title="Agregar Instrumento"><strong>+</strong> Instrumentos</a>';
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df" class="btn btn-default" title="Envios"><strong>+</strong> Envios</a>';
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df" class="btn btn-default" title="Dominios"><strong>+</strong> Dominios</a>';
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df" class="btn btn-default" title="Tipificaciones"><strong>+</strong> Tipificaciones</a>';
        
        $cadB .= '</div>';
        
        $cadC = '';
        $cadC .= '<div class="btn-group btn-group-justified">';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/add" class="btn btn-default" title="Agregar Usuario"><strong>+</strong> Usuarios</a>';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df" class="btn btn-default" title="Agregar Reporte"><strong>+</strong> Reportes</a>'; 
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/gerencia/df" class="btn btn-default" title="Ver Reporte General">Reporte General</a>';       
        $cadC .= '</div>';
        
        $c1 = '#eeffee';
        $c2 = '#f6f6ff';
        $c3 = '#ffffee';
        $c = $c1;
        $vctSt['1'] = '<span style="color:#000099;">Activo</span>';
        $vctSt['2'] = '<span style="color:#999900;">Pendiente</span>';
        
        $proyectos = $this->proyectos();
        
        $cad = '<div class="row"><hr /><div class="subtitulos col-sm-12"> &nbsp; Instrumentos </div><br /><br /></div>';
        
        foreach( $proyectos as $proyecto ){
            $cad .= '<div class="row" style="background-color:#ccc;"><div class="col-sm-12"><img src="'.Yii::$app->params['baseUrl'] .'img/bandera/' .$proyecto->pais .'.png" style="height:1.5em;" title="' .$proyecto->pais .'"/> &nbsp; &nbsp; ' .$proyecto->de .'</div></div>';
            
            $sql1 = "select schema_name from information_schema.schemata where schema_name = '" .$proyecto->codigo ."';";
            
            $obj = Aux::findBySql($sql1)->one();
            if( isset($obj->schema_name) && $obj->schema_name != '' ){
                $sql2 = "select id, de, barrida, codigo, st from " .$obj->schema_name .".instrumento where st != '0' order by id DESC;";
                
                $vctInstrumento = Aux::findBySql($sql2)->all();
                
                foreach( $vctInstrumento as $instrumento ){
                    if( $c == $c1 ) $c = $c2;
                    else if( $c == $c2 ) $c = $c3;
                    else $c = $c1;
                    
                    if( true ){
                        
                        if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($instrumento->codigo) .'Controller') )
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$instrumento->codigo;
                        else if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($obj->schema_name) .'Controller') )
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$obj->schema_name;
                        else
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta';
                        
                        $estilo = ' style="float:left; padding: 0.5em; padding-left: 1.5em; text-align:center;" ';
                        
                        $cad .= '<div class="row" style="background-color:' .$c .';"><div class="col-sm-5" style="line-height:' .$height2 .';"> '.$proyecto->id.'.' .$instrumento->id .'. ' .$instrumento->de .'</div>';
                        $cad .= '<div class="col-sm-7" style="">';



                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/barrida&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Barrida"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/barrida2.png" style="height:' .$height .';"/>
</a></div>';

                        
                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/ver&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Ver Instrumento"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/ver.png" style="height:' .$height .';"/>
</a></div>';
                        
                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xlsup&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Cargar data"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/data.png" style="height:' .$height .';"/>
</a></div>';
                        $cad .= '<div ' .$estilo .'> <a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/cuotaform&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Cargar cuota">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/cuota.png" style="height:' .$height .';"/>
</a></div>';
                        
                        $cad .= '<div ' .$estilo .'><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/viz&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Ruta de las preguntas">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/ruta.png" style="height:' .$height .';"/>
</a></div>';
                        $cad .= '<div ' .$estilo .'><a href="' .$pathController .'/contactar1&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Acceder al instrumento">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/teleoperar.png" style="height:' .$height .';"/>
</a></div>';
                        $cad .= '<div ' .$estilo .'><a href="' .$pathController .'/reportes&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Acceder al Listado de Reportes">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/reportes.png" style="height:' .$height .';"/>
</a></div>';
                        
                        $cad .= '<div ' .$estilo .'><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/df&x3proyecto=' .$obj->schema_name .'&x3id=' .$instrumento->id .'" style="color: #0000aa;" title="Editar ' .$instrumento->de .'">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="height:' .$height .';"/>
</a></div>';
                        
                        $cad .= '<div ' .$estilo .'><a href="javascript:if( confirm(\'Eliminar el instrumento: ' .$instrumento->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/eliminar2&x3proyecto=' .$obj->schema_name .'&x3id=' .$instrumento->id .'\';" style="color: #0000aa;" title="Eliminar ' .$instrumento->de .'">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="height:' .$height .';"/>
</a></div>';
           
                        $cad .= '</div></div>';
                        
                    }
                    
                }
                
                $cad .= '<div class="row"><div class="col-sm-12">&nbsp;</div></div>';
                
            }
        }     
        return $this->render('@views/soporte/index',array(
            'menu' => '',
            'titulo' => 'Panel - Menú ',
            'txt' =>  Mensaje::mostrar() .$cadB .$cadC .'<br/>' .$cad ,
            'ayuda' => '',
        ));    
    } // eof #######################################################
    


    /**
     * Despliega las opciones para los clientes
     */
    public function actionCliente(){
        $cadB = '';
        $height = '2.0em';
        $height2 = '3.0em';
        
        $cadB .= Ayuda::toHtml('panel_clientes');
                
        $cad = '';
        
        $c1 = '#eeffee';
        $c2 = '#f6f6ff';
        $c3 = '#ffffee';
        $c = $c1;
        $vctSt['1'] = '<span style="color:#000099;">Activo</span>';
        $vctSt['2'] = '<span style="color:#999900;">Pendiente</span>';
        
        $proyectos = $this->proyectos();
        

        
        foreach( $proyectos as $proyecto ){
            $cad .= '<div class="row" style="background-color:#ccc;"><div class="col-sm-12"> &nbsp; &nbsp; ' .$proyecto->de .'</div></div>';
            
            $sql1 = "select schema_name from information_schema.schemata where schema_name = '" .$proyecto->codigo ."';";
            $obj = Aux::findBySql($sql1)->one();
            if( isset($obj->schema_name) && $obj->schema_name != '' ){
                $sql2 = "select id, de, barrida, codigo, st from " .$obj->schema_name .".instrumento where st != '0' order by id DESC;";
                
                $vctInstrumento = Aux::findBySql($sql2)->all();
                
                foreach( $vctInstrumento as $instrumento ){
                    if( $c == $c1 ) $c = $c2;
                    else if( $c == $c2 ) $c = $c3;
                    else $c = $c1;
                    
                    if( true ){
                        
                        if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($instrumento->codigo) .'Controller') )
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$instrumento->codigo;
                        else if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($obj->schema_name) .'Controller') )
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$obj->schema_name;
                        else
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta';
                        
                        $estilo = ' style="float:left; padding: 0.5em; padding-left: 1.5em; text-align:center;" ';
                        
                        $cad .= '<div class="row" style="background-color:' .$c .';"><div class="col-sm-9" style="line-height:' .$height2 .';"> '.$instrumento->de .'</div>';
                        $cad .= '<div class="col-sm-3" style="">';


                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/ver&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Ver Instrumento"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/ver.png" style="height:' .$height .';"/>
</a></div>';
                        
                        if( false ) 
                            $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xlsup&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Cargar data"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/data.png" style="height:' .$height .';"/>
</a></div>';
                        if( false )
                            $cad .= '<div ' .$estilo .'> <a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/cuotaform&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Cargar cuota">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/cuota.png" style="height:' .$height .';"/>
</a></div>';
            
                        $cad .= '<div ' .$estilo .'><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/viz&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Ruta de las preguntas">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/ruta.png" style="height:' .$height .';"/>
</a></div>';
            
                        $cad .= '<div ' .$estilo .'><a href="' .$pathController .'/reportes&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Acceder al Listado de Reportes">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/reportes.png" style="height:' .$height .';"/>
</a></div>';
                        

           
                        $cad .= '</div></div>';
                        
                    }
                    
                }
                
                $cad .= '<div class="row"><div class="col-sm-12">&nbsp;</div></div>';
                
            }
        }     
        return $this->render('@views/soporte/index',array(
            'menu' => '',
            'titulo' => ' Panel de Instrumentos',
            'txt' =>  '<div style="font-size: 1.2em;">' .Mensaje::mostrar() .$cadB .'<br/>' .$cad .'</div>',
            'ayuda' => '',
        ));    
    } // eof 
    

    
    /**
     * Proyectos que son accesibles por el usuario
     */
    public function proyectos(){
        if( Usuario::id() == 1 ){
            $proyectos = Aux::findBySql("select * from a.proyecto where st!='0';")->all();     
        }else{
            $proyectos = Aux::findBySql("select p.* from a.proyecto p 
inner join a.usuario_proyecto up on up.cod_proyecto = p.codigo and up.st = '1'
where p.st != '0';")->all();
        }
        return $proyectos;
    }


    /**
     * Despliega las opciones para tlo
     */
    public function actionTlo(){
        $cad = Ayuda::toHtml('instrumentos');
        $proyectos = $this->proyectos();
        
        foreach( $proyectos as $proyecto ){
            $data = array();    
            $cad .= '<div class="row"><div class="subtitulos col-sm-12"> &nbsp; ' .$proyecto->de .' </div><br /><br /></div>';
            
            $sql1 = "select schema_name from information_schema.schemata where schema_name = '" .$proyecto->codigo ."';";
            $obj = Aux::findBySql($sql1)->one();
            if( isset($obj->schema_name) && $obj->schema_name != '' ){
                $sql2 = "select id, de, barrida, codigo, st from " .$obj->schema_name .".instrumento where st != '0' order by id DESC;";
                $vctInstrumento = Aux::findBySql($sql2)->all();
                foreach( $vctInstrumento as $instrumento ){               
                    if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($instrumento->codigo) .'Controller') )
                        $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$instrumento->codigo;
                    else if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($obj->schema_name) .'Controller') )
                        $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$obj->schema_name;
                    else
                        $pathController = Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta';
                    $dato[] = $instrumento->de;
                    $dato[] = $pathController .'/contactar1&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id;
                    $data[] = $dato; unset($dato);
                }
                $cad .= Listado::listado6( $data ) .'<hr />'; unset($data);
            }
        }

        
        $txt = Request::rq('x3txt') .'';
        if( $txt != '' ) $txt = '<div class="alert alert-info"><ul>' .$txt .'</ul></div>';
        else $txt = '';
        
        return $this->render('@views/crm/txt',array( 'txt' => $txt .$cad  ));    
    } // eof #######################################################
    
    

    /**
     * Cuotas por instrumentos
     */    
    public function cuotasInstrumento( $id_proyecto, $id_instrumento, $headFecha, $diasAntes, $vct ){
        $cad = '';
        $cant = count($vct);
        for( $s=1; $s<=$cant ;$s++  )
            $this->tColumna[$s] = 0;
        $sql1 = "select i.*, d.id as id_dominio, d.de as dominio from " .$id_proyecto .".instrumento i inner join " .$id_proyecto .".dominio d on d.id_instrumento=i.id where i.id='" .$id_instrumento ."' and d.st in (1,2,5) order by i.de ASC, d.de ASC;";
        //echo $sql1; exit();
        $obj1 = new Aux();
        $regs1 = $obj1->findBySql($sql1)->all();
        
        if( count($regs1) > 0 ){
            $cad .= '<h4>INSTRUMENTO ' .$id_instrumento .'. ' .$regs1[0]->de .'</h4>';
            $cad .= '<table border="1" style="width:144.0em; text-align: center; padding: 0.5em;">';
            $cad .= '<thead style="font-size: 0.8em;"><tr>';
            $cad .= '<th style="text-align:center; width:6.0em;"> ID</th>';
            $cad .= '<th style="text-align:center; width:36.0em;"> DOMINIO</th>';
            $cad .= '<th style="text-align:center; "> CUOTA</th>';
            $cad .= '<th style="text-align:center; "> TOTAL</th>';
            $cad .= $headFecha;
            $cad .= '</tr></thead>';
            $cad .= '<tbody>';
            
            foreach( $regs1 as $reg ){
                $this->tFila[$reg->id_dominio .'_encuesta'] = 0;
                $this->tFila[$reg->id_dominio .'_ref'] = 0;            
                foreach( $vct as $vctAux  ){
                    $e = 0;
                    $sqlSelect1 = '';
                    $sqlSelect2 = '';
                    $sqlSum = '';
                    $sql2 = '';
                    $sql3 = '';
                    for( $s=1; $s<=$cant ;$s++  ){
                        
                        $sqlSum .= $this->f01($s);
                        $sqlSelect1 .= $this->f02( $id_proyecto, $id_instrumento, $reg->id_dominio, 'fecha_encuesta', $vct[$e], $cant, $s );
                        $sqlSelect2 .= $this->f02( $id_proyecto, $id_instrumento, $reg->id_dominio, 'fecha_ref', $vct[$e++], $cant, $s );
                    } // for
                    $sql2 .= 'select ' .substr($sqlSum,0,-2) .' from (' .$sqlSelect1 .") w;";
                    $sql3 .= 'select ' .substr($sqlSum,0,-2) .' from (' .$sqlSelect2 .") w;";
                } // for
                $obj2 = new Aux();
                $regs2 = $obj2->findBySql($sql2)->all();
                $obj3 = new Aux();
                $regs3 = $obj3->findBySql($sql3)->all();
                

                $t = 0;
                foreach( $regs2 as $reg2 ){
                    $cad .= $this->cuotasDominio( $reg->id_dominio, $reg->dominio, $cant, $diasAntes, $reg2, $regs3[$t] );
                    $t++;
                } // for
            }
            
            $total = 0;
            $cad .= '<tr style="text-align:center;">';
            $cad .= '<td colspan="3"> &nbsp;</td>';
            $cadT = '';
            for( $s=1; $s<=$cant ;$s++  ){
                $total += $this->tColumna[$s];
                $cadT .= '<td>' .$this->cero($this->tColumna[$s]) .'</td>';
            } // for
            $cad .= '<td style="text-align: right;"><b>' .$total .' </b>&nbsp;</td>';
            $cad .= $cadT;
            $cad .= '</tr>';
            
            
            $cad .= '</tbody></table><br />';
        }
        return $cad;
    } // eof


    /**
     * Cuotas por dominio
     */    
    public function cuotasDominio( $id_dominio, $dominio, $cant, $diasAntes, $reg2, $reg3 ){
            $cad = '<tr style="text-align:center;">';
            $cad .= '<td rowspan="2" >' .$id_dominio  .'</td>';
            $cad .= '<td rowspan="2" style="text-align: left;"> &nbsp;' .$dominio .'</td>';
            $cad .= '<td style="text-align: left; width: 36.0em; font-size: 0.8em;"> Según día de encuesta</td>';
            $cad .= '<td style="text-align: right;"> [:' .$id_dominio .'_encuesta:] &nbsp;</td>';        
            for( $s=1; $s<=$cant ;$s++  ){
                $aux01 = 'd' .$this->set0($s,1);
                $this->vct1[$id_dominio]['encuesta'][$aux01] = $this->cero($reg2->$aux01);
                if( ($s - $diasAntes) == 0 )
                    $cad .= '<td style="background-color:' .$this->color() .';color:#006;">' .$this->cero($reg2->$aux01) .'</td>';
                else if( ($s - $diasAntes) < 0 )
                    $cad .= '<td style="background-color:' .$this->color() .';color:#999;">' .$this->cero($reg2->$aux01) .'</td>';
                else
                    $cad .= '<td style="background-color:' .$this->color() .';">' .$this->cero($reg2->$aux01) .'</td>';
                $this->tColumna[$s] += $reg2->$aux01;
                $this->tFila[$id_dominio.'_encuesta'] += $reg2->$aux01;
            } // for
            $cad .= '</tr>';

            $cad .= '<tr style="text-align:center;">';
            $cad .= '<td style="text-align: left; width: 36.0em; font-size: 0.8em;"> Según día de visita</td>';
            $cad .= '<td style="text-align: right; color:#999;"> [:' .$id_dominio .'_ref:] &nbsp;</td>';
            for( $s=1; $s<=$cant ;$s++  ){
                $aux01 = 'd' .$this->set0($s,1);
                $this->vct1[$id_dominio]['ref'][$aux01] = $this->cero($reg2->$aux01);
                if( ($s - $diasAntes) == 0 ) 
                    $cad .= '<td style="color:#006;">' .$this->cero($reg3->$aux01) .'</td>';
                else if( ($s - $diasAntes) < 0 )
                    $cad .= '<td style="color:#999;">' .$this->cero($reg3->$aux01) .'</td>';
                else
                    $cad .= '<td>' .$this->cero($reg3->$aux01) .'</td>';
                $this->tFila[$id_dominio.'_ref'] += $reg3->$aux01;
            } // for
            $cad .= '</tr>';
            foreach( $this->tFila as $k=>$v )
                $cad = str_replace('[:' .$k .':]', $v, $cad);
            return $cad;
    } // eof
    

    /**
     * Formulario
     */    
    public function format1( $cad ){
        switch( $cad ){
        case 'Sun': return 'Dom'; break;
        case 'Mon': return 'Lun'; break;
        case 'Tue': return 'Mar'; break;
        case 'Wed': return 'Mie'; break;
        case 'Thu': return 'Jue'; break;
        case 'Fri': return 'Vie'; break;
        case 'Sat': return 'Sab'; break;
        }
        return null;
    } // eof


    /**
     * Retorna Mes en español
     */    
    public function format2( $cad ){
        $part = explode('-', $cad);
        switch( $part[1] ){
        case '01': return $part[2] .', Ene<br />' .$part[0]; break;
        case '02': return $part[2] .', Feb<br />' .$part[0]; break;
        case '03': return $part[2] .', Mar<br />' .$part[0]; break;
        case '04': return $part[2] .', Abr<br />' .$part[0]; break;
        case '05': return $part[2] .', May<br />' .$part[0]; break;
        case '06': return $part[2] .', Jun<br />' .$part[0]; break;
        case '07': return $part[2] .', Jul<br />' .$part[0]; break;
        case '08': return $part[2] .', Ago<br />' .$part[0]; break;
        case '09': return $part[2] .', Sep<br />' .$part[0]; break;
        case '10': return $part[2] .', Oct<br />' .$part[0]; break;
        case '11': return $part[2] .', Nov<br />' .$part[0]; break;
        case '12': return $part[2] .', Dic<br />' .$part[0]; break;
        }
        return null;
    } // eof

    
    /**
     * Parte SQL
     */
    public function f01( $i ){
        return ' sum(d' .$this->set0($i,1) .') as d' .$this->set0($i,1) .', ';
    } // eof

    
    /**
     * Parte SQL
     */
    public function f02( $proy, $inst, $dom, $campo, $valor, $cantidad, $i ){
        if( $i == 1 )
            $cad = ' select ';
        else
            $cad = ' union select ';
        for( $j=1 ; $j<= $cantidad; $j++ )
            if( $i == $j )
                $cad .= ' c.cuota as d' .$this->set0($j,1) .', ';
            else
                $cad .= ' null::integer as d' .$this->set0($j,1) .', ';
        $cad = substr($cad,0,-2);
        $cad .= " from " .$proy .".cuota c where c.id_instrumento='" .$inst ."' and id_dominio = '" .$dom ."' and " .$campo ." = '" .$valor ."' and c.st in (1,2,5) ";
        return $cad;
    } // eof 


    /**
     * Completa los ceros a la izquierda
     */
    public function set0( $valor, $ceros = '2' ){
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
    } // eof 



    /**
     * Elimina el cero
     */    
    public function cero( $valor ){
        if( (int)$valor == 0 ) $valor = '&nbsp;';
        return $valor;
    }// eof 



    /**
     * Reporte de cargas
     */
    public function reporteCarga( $proyecto, $idInstrumento ){
        $instrumento = '';
        // CUOTAS
        $sqlXX1 = "select distinct c.fecha_ref, i.de from " .$proyecto .".cuota c inner join " .$proyecto .".instrumento i on i.id = c.id_instrumento where c.id_instrumento = '" .$idInstrumento ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by c.fecha_ref asc;";
        $regsXX1 = Aux::findBySql($sqlXX1)->all();

        $cad = '';
        $vct = array();
        $it = count($regsXX1);
        $cab2 = array();
        $ik = 0;
        $cuerpo1 = $cuerpo2 = '';
        $cabecera1 = "'Fecha', ";
        foreach( $regsXX1 as $a1 ){ // cuotas del dia
            $instrumento = $a1->de;
            $ik++;
            $cab2[] = "'" .$a1->fecha_ref ."'";
            $cabecera1 .= "'" .$a1->de ."',";
            $sqlXX = "select d.de, c.cuota from " .$proyecto .".dominio d inner join " .$proyecto .".cuota c on c.id_dominio = d.id where d.id_instrumento = '" .$idInstrumento ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by d.cod ASC;";
            $regsXX = Aux::findBySql($sqlXX)->all();
            foreach( $regsXX as $a2 ){
                if( isset($vct[$a1->de]) ) $vct[$a1->de] += $a2->cuota;
                else $vct[$a1->de] = $a2->cuota;
                $cuerpo2 .= "['" .$a2->de ."', " .$a2->cuota ."],";

            }
        } // for 
        $cabecera2 = "['Dominio', " .implode(',',$cab2) ."],";

        
        // ULTIMAS 30 CUOTAS
        $sql3 = "select i.de as instrumento, sum(c.cuota) as cuota, fecha_encuesta from " .$proyecto .".cuota c inner join " .$proyecto .".instrumento i on i.id = c.id_instrumento where c.fecha_encuesta::date >= '" .date('Y-m-d'). "'::date - 30 group by instrumento, fecha_encuesta order by c.fecha_encuesta asc;";
        $regs3 = Aux::findBySql($sql3)->all();
        foreach( $regs3 as $reg3 ){
            $cuerpo1 .= "['" .$reg3->fecha_encuesta ."'," .(int)$reg3->cuota ."],";
        }


$cad .= "
<script type=\"text/javascript\"> 
google.charts.load('current', {'packages':['corechart','bar']});
google.charts.setOnLoadCallback(x3grafico1);

function x3grafico1(){ 
var data1 = new google.visualization.arrayToDataTable([
[" .substr($cabecera1,0,-1) ."], 
 " .substr($cuerpo1,0,-1) ."
]);

        var options1 = {
          title: 'Data cargada, últimos 30 días',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
      

var chart1 = new google.visualization.LineChart(document.getElementById('x3GraficoCuota1'));
chart1.draw(data1, options1);
};

google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawStuff2);
function drawStuff2(){ 
var data2 = new google.visualization.arrayToDataTable([" .$cabecera2 ."" .$cuerpo2 ."]); 
var options2 = {chart: {title: 'Carga de cuotas solo día de hoy',subtitle: '" .$instrumento ."'},series:{0: { axis: 'cuotas' },},axes:{y: {cuotas: {label: 'Cuotas'}}}};
var chart2 = new google.charts.Bar(document.getElementById('x3GraficoCuota2'));
chart2.draw(data2, options2);
};

</script>";
$tabla = '<table width="100%"><tbody><tr><th>INSTRUMENTO</th><th>CUOTA</th></tr></tbody><tbody>';
foreach( $vct as $k => $v )
    $tabla .= '<tr><td>' .$k .'</td><td>' .$v .'</td></tr>';

$tabla .= '</tbody></table>';
$cad .= "<hr />" .$tabla ."<hr /><div id=\"x3GraficoCuota2\" style=\"width: 100%; height: 350px;\"></div><br /><div id=\"x3GraficoCuota1\" style=\"width: 100%; height: 350px;\"></div><hr /><button type=\"button\" class=\"btn btn-primary\" onClick=\"\">Enviar por correo electrónico</button><br /><br />";


        return $cad;
    } // eof #############################################################
    


    /**
     * Formulario de cuotas
     */    
    public function actionCuotaform(){
        $model = new UploadForm();
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        $fecha = Request::rq('x3fecha');
        $cad = $cad1 = '';



            $auxFecha = '';
            if( $fecha != null )
                $auxFecha = '&x3fecha=' .$fecha;


        
            
            $sql1 = 'select id,de from ' .$proy .'.instrumento where st in (1,2,5) order by de ASC;';
            $obj1 = new Aux();
            $regs1 = $obj1->findBySql($sql1)->all();
            foreach( $regs1 as $v )
                if( $v->id == $inst )
                    $cad1 .= '<option value="' .$v->id .'" selected="selected">' .$v->id .' - ' .$v->de .'</option>';
                else
                    $cad1 .= '<option value="' .$v->id .'">' .$v->id .' - ' .$v->de .'</option>';
  
            $cad = '';

            
            $cad .= '<div class="titulos" >Agregar Cuota</div>';
         
            $cad .= '<div class="row" style="text-align:left;">
                    <div class="col-sm-6" style="padding-right: 3.0em;">';

            
            $cad .= '<form id="cuotaform" method="POST" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/in&x3op=1&x3proy=' .$proy .'&x3inst=' .$inst .'&">';


            
            $cad .= '<div class="row">';
            $cad .= '<label class="control-label col-sm-4  ui-corner-top ui-state-default ui-state-active ui-state-focus" for="Instrumento">'. Yii::t('app/crm', 'Instrumento') .'</label>';
            $cad .= '<div class="col-sm-8  ui-widget-content"><select id="id_instrumento" name="id_instrumento" class=""  style="background-color:#ffffff; border: 0px; width:100%;" onChange="carga( \'x3divDominio\', \'x3proy=' .$proy .$auxFecha .'&x3inst=\' +$(\'#id_instrumento\').val() +\'&x3op=1\', \'out\', \'soporte/carga\' );carga( \'x3divData\', \'x3proy=' .$proy .$auxFecha .'&x3inst=\' +$(\'#id_instrumento\').val() +\'&x3op=1\', \'selectdata\', \'soporte/carga\' );" >' .$cad1 .'</select></div>';
            $cad .= '</div>';



            $cad .= '<div class=" row">';
            $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Data">'. Yii::t('app/crm', 'Data') .'</label>';
            $cad .= '<div id="x3divData" class="col-sm-8 ui-widget-content">';
            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divData\', \'x3proy=' .$proy .'&x3inst=' .$inst .'&x3op=1\', \'selectdata\', \'soporte/carga\' );" />';
            $cad .= '</div>';
            $cad .= '</div>';


        
        
            $cad .= "\n<script>\n
  $( function(){\n
    $( '#fecha_ref' ).datepicker({dateFormat: 'yy-mm-dd'});\n
    $( '#fecha_encuesta' ).datepicker({dateFormat: 'yy-mm-dd'});\n
  } );\n
  </script>\n";
            $cad .= '<div class="row">';
            $cad .= '<label class="control-label col-sm-4  ui-state-focus" for="Fecha de Encuesta">'. Yii::t('app/crm', 'Fecha de Encuesta') .'</label>';
            $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="fecha_encuesta" name="fecha_encuesta" value="' .date('Y-m-d') .'" style="text-align:center; width: 100%; border:0px;margin:0;  padding:0;" /></div>';
            $cad .= '</div>';


            
            $cad .= '<div class=" row">';
            $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Dominio">'. Yii::t('app/crm', 'Dominio') .'</label>';
            $cad .= '<div id="x3divDominio" class="col-sm-8 ui-widget-content">';
            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divDominio\', \'x3proy=' .$proy .'&x3inst=' .$inst .'&x3op=1\', \'out\', \'soporte/carga\' );" />';
            $cad .= '</div>';
            $cad .= '</div>';
            
  


            $cad .= '<div class="row" style=";">';
            $cad .= '<label class="control-label col-sm-4  ui-state-focus" for="Cuota">'. Yii::t('app/crm', 'Cuota') .'</label>';
            $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="cuota" name="cuota" type="number" max="9999" min="0" placeholder="0" style="text-align: center; border:0px;margin:0;  padding:0; width: 100%;" /></div>';
            $cad .= '</div>';


            $cad .= '<div class="row" style="">';
            $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Fecha de Visita">'. Yii::t('app/crm', 'Fecha de Atención') .'</label>';
            $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="fecha_ref" name="fecha_ref" value="" style="text-align:center; width:100%; border:0px; margin:0; padding:0;" /></div>';
            $cad .= '</div>';

           
            $cad .= '<div class="row">';
            $cad .= '<label class="control-label col-sm-4" ></label>';
            
            
            $cad .= '<button class="ui-button ui-corner-all ui-widget" onClick="submit();" id="button">   Agregar   </button>';
            $cad .= '</div>';
            
            $cad .= '</form>';







            $cad .= $this->reporteCarga( $proy, $inst ); 
                  
            
            $cad .= '</div>';
            $cad .= '<div id="x3divPlan" class="col-sm-6">';

            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divPlan\', \'x3proy=' .$proy .'&x3inst=' .$inst .$auxFecha .'\', \'dominiocuota\', \'soporte/carga\' );" />';
            $cad .= '</div>';
            $cad .= '</div>';



            $regresar = '<div class="regresar"><a href="'
                      . Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index'
                      .'"> &lt;- Regresar</a></div>';
            
            return $this->render('@views/soporte/txt',array(
                'regresar' => $regresar,
                'menu' => Menu::menu(),
                'txt' => $cad,
            ));
            
        
            
    } // eof #######################################################
    
    

    /**
     * Lista de dominios para select
     */    
    public function actionOut(){
        Yii::$app->layout = 'embebido';
        $op = Request::rq('x3op');
        switch( $op ){        
        case 1: // lista de dominios para select
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            $cad = '<select id="id_dominio" name="id_dominio"  style="background-color:#ffffff; border:none; width:100%;">';
            if( $inst != null ){
                //  $sql = "select id,de,cod from " .$proy .".dominio where st in (1,2,5) and (de = '' or id_instrumento='" .$inst ."'  ) order by de ASC;";
                $sql = "select id, de, cod from " .$proy .".dominio where st in (1,2,5) and id_instrumento='" .$inst ."' order by cod ASC;";
                $obj = new Aux();
                $regs = $obj->findBySql($sql)->all();
                foreach( $regs as $v )
                    $cad .= '<option value="' .$v->id .'">' .$v->cod .' - ' .$v->de .'</option>';
            }else
                $cad .= '<option value="">&nbsp;</option>';
            $cad .= '</select>';

            echo $cad;
            break;
            
        }
    } // eof #######################################################



    /**
     * lista de data para select
     */
    public function actionSelectdata(){
        Yii::$app->layout = 'embebido';
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');

        $fecha = date('Y-m-d H:i:s',strtotime('-3 day', strtotime(date('Y-m-d'))));
        $cad = '<select id="id_data" name="id_data"  style="background-color:#ffffff; border:none; width:100%;">';
        if( $inst != null ){
            $sql = "select id, de, cantidad from " .$proy .".data where st in (1,2) and id_instrumento='" .$inst ."' and cantidad > 0 and reg >= '" .$fecha ."'::timestamp order by id DESC;";
            $regs = Aux::findBySql($sql)->all();
            foreach( $regs as $v )
                    $cad .= '<option value="' .$v->id .'">' .$v->id .' - ' .$v->de .' (' .$v->cantidad .' reg.)</option>';
            if( count($regs) == 0 ) $cad .= '<option value="">&nbsp; No hay data reciente </option>';
        }else
            $cad .= '<option value="">&nbsp; Instrumento no seleccionado </option>';
        $cad .= '</select>';
        echo $cad;
    } // eof #######################################################


    /**
     * Gráfico VIZ
     */
    function viz(){
        $vct2 = array();
        $cInputText = $cInputRadio = $cTexto = $cFin = $cOption = '';
        
        $cInputText = '[style=filled, color=thistle2]';
        $cInputRadio = '[style=filled, color=cadetblue1]';
        $cTexto = '[color=lightgrey, fontcolor=white]';
        $cFin = '[color="#000000"]';
        $cOption = '[style=filled, color=lightsteelblue1]';
        
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        
        $sql1 = "select e.id, e.id_pregunta_tp, e.de, e.codigo, e.ir_a from " .$proy .".entrada e where e.st in (1,2,5) and e.id_instrumento='" .$inst ."' order by e.id ASC;";
        $obj1 = new Aux();
        $regs1 = $obj1->findBySql($sql1)->all();
        
        $cad = '<script src="' .Yii::$app->params['baseUrl'] .'js/viz.js"></script>';
        
       
        $cad .= '<script type="text/vnd.graphviz" id="x3ruta">
digraph "unix" {
graph [	fontname = "Helvetica-Oblique",	bgcolor="white", fontcolor="black", size = "32,0" ];
 node [ shape = ellipse, fontsize = 24, sides = 8, allowedToMoveX = true, allowedToMoveY = true, distortion = "0.0", orientation = "0.0", skew = "0.0"
		color = "#000066", colorborder = "#ffffdd", fontname = "Helvetica-Outline" ];';
        $aux = null;
        foreach( $regs1 as $reg ){
            $vct3[$reg->codigo] = $reg->ir_a;
            $i = $reg->id;
            if( $reg->id_pregunta_tp == 1 ){
                if( $reg->codigo == 'x3fin' ){
                    $cad .= '"FIN" ' .$cFin .';';
                    $vct['0'] = '';
                }else{
                    $aux = $reg->codigo;
                    $cad .= '"' .$reg->codigo .'" ' .$cTexto .';';
                    $vct['0'] = $reg->de;
                    if( $reg->ir_a == 'x3fin' )
                        $cad .= '"' .$reg->codigo .'" -> "FIN";';
                    else
                        $cad .= '"' .$reg->codigo .'" -> "' .$reg->ir_a .'";';
                }
            }else if( $reg->id_pregunta_tp == 2  ){
                $vct['0'] = $reg->de;
                $cad .= '"' .$reg->codigo .'" ' .$cInputText .';';
                if( $reg->ir_a == 'x3fin' )
                    $cad .= '"' .$reg->codigo .'" -> "FIN";';
                else
                    $cad .= '"' .$reg->codigo .'" -> "' .$reg->ir_a .'";';     
            }else{
                $vct['0'] = $reg->de;
                $cad .= '"' .$reg->codigo .'" ' .$cInputRadio .';';
                $sql2 = "select de, valor, orden, ir_a from " .$proy .".entrada_op where st in (1,2,5) and id_entrada='" .$reg->id ."' order by id ASC;";                //  and tp != '1'
                $obj2 = new Aux();
                $regs2 = $obj2->findBySql($sql2)->all();
                foreach( $regs2 as $rg ){
                    $vct3[$reg->codigo .'.' .$rg->valor] = $rg->ir_a;
                    $vct[(string)$rg->valor] = $rg->de;
                    if( substr($rg->valor,0,1) != '_' ){
                        
                        if( true ) $ux = $rg->valor;
                        else $ux = $rg->de;
                        $cad .= '"' .$reg->codigo .'. ' .$ux .'" ' .$cOption .';';
                        $cad .= '"' .$reg->codigo .'" -> "' .$reg->codigo .'. '.$ux .'";';
                        if( isset($rg->ir_a) ) $ir_a = $rg->ir_a;
                        else $ir_a = $reg->ir_a;
                        if( $ir_a == 'x3fin' )
                            $cad .= '"' .$reg->codigo .'. '.$ux .'" -> "FIN";';
                        else
                            $cad .= '"' .$reg->codigo .'. '.$ux .'" -> "' .$ir_a .'";';
                    }
                }
            }
            $vct2[$reg->codigo] = $vct;
            unset($vct);
        }
        
        $cad .= '
} </script>
<script>
function inspect(s){ return "<pre>" + s.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;") + "</pre>";}
function src(id){ return document.getElementById(id).innerHTML; }
function example(id, format, engine){
  var result;
  try{
    result = Viz(src(id), format, engine);
    if(format === "svg") return result;
    else return inspect(result);
  }catch(e){ return inspect(e.toString()); }
};
</script>';
        
        
        
        $cad2 = '';

        $cad2 .= '<div id="x3p1"><table style="min-width: 1200px;"><tbody>';
        $vctAux = null;
        foreach( $vct2 as $k => $v ){
            $cad2 .= '<tr><td colspan="4"><hr /></td></tr>';
            if( $vctAux != $k ){    
                $cad2 .= '<tr><td> ' .$k .'</td>';
                if( array_key_exists('0',$v) )
                    $cad2 .= '<td> ' .$v['0'] .'</td>';
                $vctAux = $k;
            }
            if( count($v) > 1){
                $cad2 .= '<td style="width: 50%;"><table><tbody>';
                foreach( $v as $k2 => $v2 )
                    if( $k2 != '0' )
                        $cad2 .= '<tr><td> ' .$k2 .'</td><td> &nbsp; &nbsp; </td><td> ' .$v2 .'</td><td> -> &nbsp; </td><td><input type="text" id="" name="" value="' .$vct3[$k .'.' .$k2] .'"  style="border:0;"/></td></tr>';
                $cad2 .= '</tbody></table></td>';
            }else
                $cad2 .= '<td><input type="text" id="" name="" value="' .$vct3[$k] .'" style="border:0;"/></td>';
            $cad2 .= '</tr>';
        }
        $cad2 .= '</tbody></table></div>';

        $cad .= '<div class="titulos"> Grafico de Entradas del Instrumento </div><br />';
        $cad .= Ayuda::toHtml('ruta');
        $cad .= '<script>document.body.innerHTML += example("x3ruta", "svg");</script>';

        $r = array();
        $r[] = $cad;
        $r[] = $cad2;
        return $r;
    } // eof #######################################################



    /**
     * Acción que invoca los gráficos VIZ
     */    
    public function actionViz(){
        $cads = $this->viz();
        return $this->render('@views/soporte/txt',array( 'txt' => $cads[0], ));
    } // eof #######################################################
        

    /**
     * Acciones invocadas por AJAX
     */    
    public function actionIn(){
        Yii::$app->layout = 'embebido';
        $op = Request::rq('x3op');
        switch( $op ){
                 
        case 1: // agregar cuota
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            $id_dominio = Request::rq('id_dominio');
            $id_data = (int)Request::rq('id_data');
            $fecha_ref = Request::rq('fecha_ref');
            $fecha_encuesta = Request::rq('fecha_encuesta');
            $cuota = Request::rq('cuota');
            if( $proy == null || $inst == null || $id_dominio == null || $fecha_ref == null || $fecha_encuesta == null || $cuota == null || (int)$cuota < 1 || $id_data == 0 )
                break;
            $id_usuario = \Yii::$app->user->identity->id;

            $sql00 = "select * from " .$proy .".cuota where id_dominio = '" .$id_dominio ."' and fecha_encuesta='" .$fecha_encuesta ."' and fecha_ref='" .$fecha_ref ."';";
            $obj = Aux::findBySql($sql00)->one();
            
            if( ! $obj instanceof Aux ){
                $obj = new xCuota();
                $obj::$schema = $proy;
                $obj->id_instrumento = $inst;
                $obj->id_dominio = $id_dominio;
                $obj->fecha_ref = $fecha_ref;
                $obj->fecha_encuesta = $fecha_encuesta;
                $obj->cuota = $cuota;
                $obj->id_data = $id_data;
                $obj->id_usuario = $id_usuario;
                $obj->save();
            }else{
                $sql00 = "update " .$proy .".cuota set cuota = '" .$cuota ."', id_data = '" .$id_data ."', id_usuario = '" .$id_usuario ."' where id_instrumento='" .$inst ."' and id_dominio = '" .$id_dominio ."' and fecha_encuesta='" .$fecha_encuesta ."' and fecha_ref = '" .$fecha_ref ."';";
                $obj = Aux::findBySql($sql00)->one();
            }
            
            $sql0 = "select cod from " .$proy .".dominio where id = '" .$id_dominio ."';";
            $objDominio = Aux::findBySql($sql0)->one();
            $dominio = $objDominio->cod;
            $sql1 = "select id from " .$proy .".cuota where id_usuario = '" .$id_usuario ."' order by id DESC;";
            $objCuota = Aux::findBySql($sql1)->one();
            $id_cuota = $objCuota->id;
            $sql3 = "select columna_dominio from " .$proy .".instrumento where id = '" .$inst ."';";
            $objColumnaDominio = Aux::findBySql($sql3)->one();
            $columna_dominio = $objColumnaDominio->columna_dominio;

            if( ! $obj instanceof Aux ){
                $sql2 = "insert into " .$proy .".cuota_prospecto select nextval('" .$proy .".cuota_prospecto_seq'::regclass), '" .$id_cuota ."',id,'" .$id_data ."' from " .$proy .".prospecto where id_instrumento='" .$inst ."' and id_data='" .$id_data ."' and " .$columna_dominio ."='" .$dominio ."';";
                Aux::findBySql($sql2)->one();
            }
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/cuotaform&x3proy=' .$proy .'&x3inst=' .$inst .'&x3fecha=' .$fecha_encuesta, 302);
            break;

            
            
        case 2: // eliminar cuota
            $id = Request::rq('x3id');
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            if( $proy == null || $inst == null || $id == null ) break;
            $obj = new xCuota();
            $obj::$schema = $proy;
            $obj->delete();
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/dominiocuota&x3proy=' .$proy .'&x3inst=' .$inst .'&x3fecha=' .$obj->fecha_encuesta, 302);            
            break;

            
        case 3: // actualizar cuota
            $id = Request::rq('x3id');
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            if( $proy == null || $inst == null || $id == null ) break;
            $obj = new xCuota();
            $obj::$schema = $proy;
            $id_dominio = Request::rq('id_dominio');
            $fecha_ref = Request::rq('fecha_ref');
            $fecha_encuesta = Request::rq('fecha_encuesta');
            $cuota = Request::rq('cuota');
            if( $id_dominio != null )
                $obj->id_dominio = $id_dominio;
            if( $fecha_ref != null )
                $obj->fecha_ref = $fecha_ref;
            if( $fecha_encuesta != null )
                $obj->fecha_encuesta = $fecha_encuesta;
            if( $cuota != null )
                $obj->cuota = $cuota;
            $obj->update();
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/dominiocuota&x3proy=' .$proy .'&x3inst=' .$inst .'&x3fecha=' .$obj->fecha_encuesta, 302);
            break;
            
        }
        

    } // eof #######################################################


    /**
     * Cuotas por dominios
     */
    public function actionDominiocuota(){
        Yii::$app->layout = 'embebido';
        $cad = '';
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        $fecha = Request::rq('x3fecha');
        $auxFecha = '';
        $txtFecha = 'Todos los días';
        if( $fecha != null && 0 ){
            $auxFecha = " and fecha_encuesta='" .$fecha ."' ";
            $txtFecha = $fecha;
        }  
        $cad .= '<div style="font-size:0.8em;">
 <div class="row" style="font-size:0.6em;">
  <div class="col-sm-1  ui-state-active" title="ID de Instrumento">INST</div>
  <div class="col-sm-1  ui-state-active" title="ID Data">DATA</div>
  <div class="col-sm-3  ui-state-active" title="Dominio">DOMINIO</div>
  <div class="col-sm-2  ui-state-active" title="Fecha de Visita">VISITA</div>
  <div class="col-sm-2  ui-state-active" title="Fecha de Encuesta">ENCUESTA</div>
  <div class="col-sm-1  ui-state-active" title="Cuota">CUOTA</div>
  <div class="col-sm-1  ui-state-active" title="Conteo">CONTEO</div>
  <div class="col-sm-1  ui-state-active" title="Prospectos Disponibles">DISPONIBLE</div>
</div>';
        $sql1 = "select d.id, d.de, c.id_instrumento, c.cuota, c.conteo, c.fecha_ref, c.fecha_encuesta, c.id_data, count(cp.*) as disponible from " .$proy .".dominio d inner join " .$proy .".cuota c on c.id_dominio = d.id left join " .$proy .".cuota_prospecto cp on cp.id_cuota=c.id where d.st = '1' and d.id_instrumento='" .$inst ."' " .$auxFecha ." and c.fecha_encuesta::date >= (now()::date - 7)  group by d.id, d.de, cuota, conteo, fecha_ref, fecha_encuesta, c.id_data,  c.id_instrumento, c.id order by c.id DESC ;";
       
        $regs1 = Aux::findBySql($sql1)->all();    
        foreach( $regs1 as $reg ){
$cad .= '
<div class="row">
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->id_instrumento .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->id_data .'</div>
      <div class="col-sm-3 ui-widget-content" style="border-top:0px;"> ' .$reg->de .'</div>
      <div class="col-sm-2 ui-widget-content" style="border-top:0px;"> ' .$reg->fecha_ref .'</div>
      <div class="col-sm-2 ui-widget-content" style="border-top:0px;"> ' .$reg->fecha_encuesta .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->cuota .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->conteo .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->disponible .'</div>
</div>
';
        }
        $cad .= '</div>';
        $regresar = '<div class="regresar"><a href="'
                  . Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index' 
                  .'"> &lt;- Regresar</a></div>';
        $regresar = '';
        return $this->render('@views/soporte/txt',array(
            'regresar' => $regresar,
            'menu' => Menu::menu(),
            'txt' => $cad,
        ));
    } // eof
    

    
    /**
     * Sube un archivo EXCEL a el servidor
     */
    public function actionXlsup(){
        $cad = '';
        $model = new UploadForm();
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        if( Yii::$app->request->isPost ){
            $model->xls = UploadedFile::getInstance($model, 'xls');
            if( isset($model->xls) && $model->uploadXLS() )
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xls2html&x3proy=' .$proy .'&x3inst=' .$inst .'&x3file=' .$model->nombre, 302);
        }

        $action = '';

        
        $sql1 = 'select * from (select d.*,i.codigo as instrumento, i.id as idInstrumento from ' .$proy .'.data d inner join ' .$proy .'.instrumento i on i.id=d.id_instrumento where d.st in (1,2,5) order by d.id desc limit 30) alia order by id asc;';
        $datas = Aux::findBySql($sql1)->all();
        $cad3 = '';
        $cad2 = "<script type=\"text/javascript\">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([";
        $bagInst = array();
        foreach( $datas as $dat ){
            if( !in_array("'" .$dat->instrumento ."'",$bagInst) && $dat->instrumento != '' )
                $bagInst[] = "'" .$dat->instrumento ."'";
            $cad3 .= "['" .substr($dat->reg,0,-16) ."',  " .$dat->cantidad ."],";
        }
        $cab1 = "['Fecha',". implode(',',$bagInst) .'],';        
        $cad2 .= $cab1 .substr($cad3,0,-1) ." ]);
        var options = {
          title: 'Últimas 30 cargas de Data',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
        var chart = new google.visualization.LineChart(document.getElementById('x3GraficoData'));
        chart.draw(data, options);
      }
    </script>
  <div id=\"x3GraficoData\" style=\"width: 900px; height: 500px\"></div>";


               $cad .= '<div class="titulos">Subir Archivo ' .Ayuda::imagen('x3ayuda0') .'</div>';
               
               $ayuda[] = 'Carga de data';
               $ayuda[] = 'Seleccione el archivo que desea enviar al servidor';
               $ayuda[] = '';
               $vAyuda[] = $ayuda;
               unset($ayuda);
               $ayuda = Ayuda::info1('x3ayuda0',$vAyuda);
               unset($vAyuda);
               
               
               $sqlCb = "select de from " .$proy .".cabecera where id_instrumento='" .$inst ."' and st = '1' and tp = '0' order by orden asc;";
               $dataCb = Aux::findBySql($sqlCb)->all();
               $orden = $ayuda .'<table border="1" style="width:100%;text-align:center;"><tbody><tr><th colspan="' .count($dataCb) .'"> &nbsp; ORDEN DE LOS DATOS</th></tr><tr>';
               for( $ib = 1; $ib<=count($dataCb) ; $ib++ )
                   $orden .= '<td style="min-width:80px;"> Columna ' .$ib .'</td>';
               $orden .= '</tr><tr>';
               foreach( $dataCb as $dt )
                   $orden .= '<td style="min-width:80px;">' .ucfirst(strtolower($dt->de)) .'</td>';
               $orden .= '</tr></tbody></table>';
               
               $cad .= $orden;



               $cad .= '<form id="w0" enctype="multipart/form-data" method="POST" action="/crm_dev/web/index.php?r=soporte%2Fcarga%2Fxlsup">';
               $cad .= '<input type="hidden" id="x3proy" name="x3proy" value="' .$proy .'"/>';
               $cad .= '<input type="hidden" id="x3inst" name="x3inst" value="' .$inst .'"/>';
               if( false )
                   $cad .= $form->field($model, 'xls')->fileInput(['multiple' => true, 'accept' => 'image/*']);
               else if( false )
                   $cad .= $form->field($model, 'xls')->fileInput(['multiple' => false, 'accept' => '*']);

               $cad .= '
<input type="hidden" name="UploadForm[xls]" value="">
           <input type="file" id="uploadform-xls" name="UploadForm[xls]" accept="*">

'; 
               
               $cad .= '<center><button> Subir Archivo </button></center>';
               $cad .= '</form>';
              
               $cad .= '<br /><br />';
               
                             
               $cad .= $cad2; 
               
               $sql1 = 'select d.*,i.codigo as instrumento, i.id as idInstrumento from ' .$proy .'.data d inner join ' .$proy .'.instrumento i on i.id=d.id_instrumento where d.st in (1,2,5) order by d.id desc limit 30;';
               $datas = Aux::findBySql($sql1)->all();
               if( true ){
                   $celda[] = 'ID';
                   $celda[] = 'FECHA';
                   $celda[] = 'USUARIO';
                   $celda[] = 'SOPORTE';
                   $celda[] = 'INSTRUMENTO';
                   $celda[] = 'REG. INICIAL';
                   $celda[] = 'REG. FINAL';
                   $celda[] = 'CANTIDAD';
                   $celda[] = 'ACUMULAR';
                   $celda[] = 'ESTADO';
                   $data[] = $celda;
                   unset($celda);
                   foreach( $datas as $dato ){
                       $celda[] = $dato->id;
                       $celda[] = substr($dato->reg,0,19);
                       $celda[] = $dato->id_usuario;
                       $celda[] = $dato->url;
                       $celda[] = $dato->id_instrumento;
                       $celda[] = $dato->reg_ini;
                       $celda[] = $dato->reg_fin;
                       $celda[] = $dato->cantidad;
                       $celda[] = $dato->acumulado;
                       $celda[] = $dato->st;
                       $data[] = $celda;
                       unset($celda);
                   }
                   $cad .= Listado::listado2( $data, strtoupper('Últimos archivos cargados en el proyecto <i>' .$proy.'</i>') );
               }else{
                   $cad .= '';
                   $cad .= '<table border="1" style="width:100%; padding: 0.2em; text-align: center;">';
                   $cad .= '<tbody style="font-size: 0.7em;"><tr><th colspan="11" style="text-align:center; padding: 0.5em;" title="últimos 100"> ' .strtoupper('Últimos archivos cargados en el proyecto <i>' .$proy .'</i>') .'</th></tr>';
                   $cad .= '<tr>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> ID </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> FECHA </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> USUARIO </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> SOPORTE </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> INSTRUMENTO </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> REG. INICIAL </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> REG. FINAL </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> CANTIDAD </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> ACUMULAR </th>';
                   $cad .= '<th style="text-align: center; background-color:#cccccc;"> ESTADO </th>';        
                   $cad .= '</tr>';
                   $cad .= '</thead><tbody style="font-size: 0.8em;">';
                   foreach( $datas as $data ){
                       $cad .= '<tr>';
                       $cad .= '<td>' .$data->id .'</td>';
                       $cad .= '<td>' .substr($data->reg,0,19) .'</td>';
                       $cad .= '<td>' .$data->id_usuario .'</td>';
                       $cad .= '<td title="' .$data->de .'">' .$data->url .'</td>';
                       $cad .= '<td title="' .$this->getDe( $proy, 'instrumento', $data->id_instrumento ) .'">' .$data->id_instrumento .'</td>';
                       $cad .= '<td>' .$data->reg_ini .'</td>';
                       $cad .= '<td>' .$data->reg_fin .'</td>';
                       $cad .= '<td>' .$data->cantidad .'</td>';
                       $cad .= '<td>' .$data->acumulado .'</td>';
                       $cad .= '<td>' .$data->st .'</td>';
                       $cad .= '</tr>';
                   }
                   $cad .= '</tbody></table>';
               } // else
              
            
        return $this->render('@views/soporte/txt',array(
            'txt' => $cad,
            'txtt' => '', 
            'model' => $model,
            'proyecto' => $proy,
            'instrumento' => $inst,
            'list' => $cad,
            'action' => $action,
        ));
    } // eof #######################################################
    

    /**
     * Retorna la descripción del Instrumento
     */
    public function getInstDe( $proyecto = 'test', $id = 0 ){
        $sql = "select de from " .$proyecto .".instrumento where id='" .$id ."' limit 1;";
        $cad = Yii::$app->getDb()->createCommand($sql)->queryScalar();
        return $cad .'';
    } // eof ##########################################################


    /**
     * Retorna la descripción de la tabla
     */
    public function getDe( $proyecto = null, $tabla = null, $id = null ){
        if( $proyecto == null || $tabla == null || $id == null ) return '';
        $sql = "select de from " .$proyecto ."." .$tabla ." where id='" .$id ."' limit 1;";
        return Yii::$app->getDb()->createCommand($sql)->queryScalar() .'';
    } // eof ##########################################################

    
    /**
     * Microtime en forma de float
     */
    function microtime_float(){
        list($useg,$seg) = explode(' ',microtime());
        return((float)$useg+(float)$seg);
    }

    
} // class
