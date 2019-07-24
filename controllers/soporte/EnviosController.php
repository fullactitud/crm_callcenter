<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;
use app\models\AuthAssignment;

use app\components\crm\Mensaje;
use app\components\crm\Reporte;
use app\components\crm\Grafico;
use app\components\crm\Formato;
use app\components\crm\Request;
use app\components\crm\Ayuda;
use app\components\crm\Listado;
use app\models\Aux;


/**
 * Controler para manejar envios
 */
class EnviosController extends Controller{
    
    
    /**
     * Procesos que se ejecuta antes de cargar el controller
     */
    
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } // eof 
    
    
    
    
    /**
     * @inheritdoc
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    } // eof #######################################################
    
    
    
    
    /**
     * Agregar envios
     */
    public function actionAdd(){
        $cod_proyecto = trim(Request::rq('proyecto'));
        $id = (int)Request::rq('id');
        $de = trim(Request::rq('de'));
        $st = (int)Request::rq('st');
        $id_reporte = (int)Request::rq('id_reporte');
        $id_instrumento = (int)Request::rq('id_instrumento');
        $l = trim(Request::rq('l'));
        $m = trim(Request::rq('m'));
        $i = trim(Request::rq('i'));
        $j = trim(Request::rq('j'));
        $v = trim(Request::rq('v'));
        $s = trim(Request::rq('s'));
        $d = trim(Request::rq('d'));
        
        if( $cod_proyecto == '' || $id_reporte == 0 ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df&', 302 );
        }else{
            $sql = "with upsert as 
(update " .$cod_proyecto .".envio set de='" .$de ."', l='" .$l ."', m='" .$m ."', i='" .$i ."', j='" .$j ."', v='" .$v ."', s='" .$s ."', d='" .$d ."', id_reporte='" .$id_reporte ."', st='" .$st ."', id_instrumento='" .$id_instrumento ."' where id='" .$id ."' RETURNING *)
    insert into " .$cod_proyecto .".envio (de, l, m, i, j, v, s, d, id_reporte, st, id_instrumento) 
SELECT '" .$de ."', '" .$l ."', '" .$m ."', '" .$i ."', '" .$j ."', '" .$v ."', '" .$s ."', '" .$d ."', '" .$id_reporte ."', '" .$st ."', '" .$id_instrumento ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
            Aux::findBySql($sql)->one();
            
                       
            $obj = Aux::findBySql("select * from " .$cod_proyecto .".envio where de='" .$de ."' and l='" .$l ."' and m='" .$m ."' and i='" .$i ."' and j='" .$j ."' and v='" .$v ."' and s='" .$s ."' and d='" .$d ."' and id_reporte='" .$id_reporte ."' and st='" .$st ."' and id_instrumento='" .$id_instrumento ."' order by id desc ;")->one();
            if( !is_null($obj->id) ){
                
                // agregar email
                Aux::findBySql("delete from " .$cod_proyecto .".envio_email where id_envio='" .$obj->id ."';")->one();
                
                $dest = (int)Request::rq('destino');
                switch( $dest ){
                case '1': // todos
                    $sql = "select distinct id from a.usuario u ";
                    $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                    $sql .= " where u.st = '1' ;";
                    $objs = Aux::findBySql($sql)->all();
                    foreach( $objs as $u )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                    break;
                case '2': // Personal de Soporte
                    $sql = "select distinct id from a.usuario u ";
                    $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                    $sql .= " where u.st = '1' and aa.item_name in ('soporte','admin') ;";
                    $objs = Aux::findBySql($sql)->all();
                    foreach( $objs as $u )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                    break;
                case '3': // Teleoperadores
                    $sql = "select distinct id from a.usuario u ";
                    $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                    $sql .= " where u.st = '1' and aa.item_name in ('tlo') ;";
                    $objs = Aux::findBySql($sql)->all();
                    foreach( $objs as $u )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                    break;
                case '4': // Supervisores
                    $sql = "select distinct id from a.usuario u ";
                    $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                    $sql .= " where u.st = '1' and aa.item_name in ('supervisor') ;";
                    $objs = Aux::findBySql($sql)->all();
                    foreach( $objs as $u )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                    break;
                case '5': // Clientes
                    $sql = "select distinct id from a.usuario u ";
                    $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                    $sql .= " where u.st = '1' and aa.item_name in ('cliente') ;";
                    $objs = Aux::findBySql($sql)->all();
                    foreach( $objs as $u )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                    break;
                }
                
                
                $sql = "select distinct id from a.usuario u ";
                $sql .= " inner join auth_assignment aa on cast(aa.user_id as integer)=u.id ";
                $sql .= " where u.st = '1' ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u ){
                    if( !is_null(Request::rq('usuario_' .$u->id)) )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                }
            
                $sql = "select distinct id from " .$cod_proyecto .".email e where e.st = '1' ;";
                $objs = Aux::findBySql($sql)->all();
                foreach( $objs as $u ){
                    if( !is_null(Request::rq('usuario2_' .$u->id)) )
                        Aux::findBySql("insert into " .$cod_proyecto .".envio_email (id_envio, id_email) values ('" .$obj->id ."','" .$u->id ."');")->one();
                }
                
                
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df&x3txt=Fue salvado el envío&proyecto=' .$cod_proyecto .'&id=' .$obj->id, 302);
            }else{
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df&x3txt=No fue salvado el envío', 302);
            }
        }
    } // eof #######################################################
    
    
    
    
    /**
     * Acción por defecto para los envios
     *
     * @return string
     */
    public function actionDf(){
        
        $cad = '';
        $data = array();
        //        $codProyecto = Request::rq('proyecto');
        // $id = (int)Request::rq('id');
        $cad .= '<div class="titulos"> Envíos Planificados </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('envios');
        
        $cad .= '<hr />';
        $cad .= $this->form();
        //    $cad .= '<div class="subtitulos"><u>Listado</u> </div>';
        
        
        $cad .= '<hr /><br /><div>La siguiente instrucción debe estar agregada en el crontab</div>';
        $cad .= '<div>*/30 * * * * lynx http://'.$_SERVER['SERVER_NAME'].'/web/index.php?r=soporte/envios/enviar.php</div>';
        
        return $this->render('@views/crm/txt',array('txt'=>$cad));
    } // eof #######################################################
    
    
    
    /**
     * Listado de envios
     */
    public function listado(){
        
        $dato[] = 'ID';
        // $dato[] = 'Envío';
        $dato[] = 'Reporte';
        // $dato[] = 'Estado';
        $dato[] = 'L';
        $dato[] = 'M';
        $dato[] = 'I';
        $dato[] = 'J';
        $dato[] = 'V';
        $dato[] = 'S';
        $dato[] = 'D';
        
        $dato[] = '';
        $dato[] = '';
        $data[] = $dato; unset($dato);
        
        
        $sql0 = "select * from a.proyecto where st!='0' ;";
        $obj0s = Aux::findBySql($sql0)->all();
        foreach( $obj0s as $reg0 ){
            
            $sql1 = "select
 e.*, r.de as reporte, s.de as estatus 
from " .$reg0->codigo .".envio e 
inner join a.reporte r on r.id=e.id_reporte 
inner join a.estatus s on s.id=e.st ;";
            $obj1s = Aux::findBySql($sql1)->all();
            foreach( $obj1s as $reg ){
                $dato[] = $reg->id;
                $dato[] = $reg->de;
                // $dato[] = '<b>' .$reg->id_reporte .'. ' .$reg->reporte .'</b>';
                // $dato[] = $reg->estatus;
                $dato[] = $reg->l;
                $dato[] = $reg->m;
                $dato[] = $reg->i;
                $dato[] = $reg->j;
                $dato[] = $reg->v;
                $dato[] = $reg->s;
                $dato[] = $reg->d;
                
                $dato[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df&proyecto=' .$reg0->codigo .'&id=' .$reg->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reg->de .'"/></a>';
                $dato[] = '<a href="javascript:if( confirm(\'Eliminar el envio: ' .$reg->description .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/envio/eliminar1&proyecto=' .$reg0->codigo .'&id=' .$reg->id .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reg->de .'"/></a>';
                $data[] = $dato; unset($dato);
                
                $sql2 = "select
 ee.*, s.de as estatus, u.nombres||' '||u.apellidos as usuario, u.email 
from " .$reg0->codigo .".envio_email ee  
inner join a.usuario u on u.id=ee.id_email 
inner join a.estatus s on s.id=ee.st 
where id_envio='" .$reg->id ."';";
                $obj2s = Aux::findBySql($sql2)->all();
                foreach( $obj2s as $reg2 ){
                    $dato[] = '';
                    //  $dato[] = '';
                    $dato[] = $reg2->usuario .' &#60;' .$reg2->email .'>';
                    // $dato[] = $reg2->estatus;
                    $data[] = $dato; unset($dato);
                }
            }
        }

        return Listado::listado($data);
    } // eof 



    
    /**
     * Formulario para agregar un envio
     */
    public function form(){
        $cod_proyecto = Request::rq('proyecto') .'';
        $id = (int)Request::rq('id');
        
        $de = $l = $m = $i = $j = $v = $s = $d = '';
        $st = $id_reporte = $id_instrumento = 0;
        
        if( $id > 0 ){
            $sql6 = "select * from " .$cod_proyecto .".envio where id='" .$id ."';";
            $obj6s = Aux::findBySql($sql6)->all();
            if( count($obj6s) > 0 ){
                $de = $obj6s[0]->de;
                $st = $obj6s[0]->st;
                $id_reporte = $obj6s[0]->id_reporte;
                $id_instrumento = $obj6s[0]->id_instrumento;
                $l = $obj6s[0]->l;
                $m = $obj6s[0]->m;
                $i = $obj6s[0]->i;
                $j = $obj6s[0]->j;
                $v = $obj6s[0]->v;
                $s = $obj6s[0]->s;
                $d = $obj6s[0]->d;
            }
        }
        
        $cad = '';        
        $cad .= '<link rel="stylesheet" type="text/css" href="' .Yii::$app->params['baseUrl'] .'css/clockpicker.css" />';
       
        $cad .= '<script type="text/javascript" src="' .Yii::$app->params['baseUrl'] .'js/clockpicker.js"></script>';
        $cad .= '<form class="form-horizontal" id="datosf" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/add" method="POST" enctype="multipart/form-data"> 
<input type="hidden" id="id" name="id" value="' .$id .'"/>
';
        
        
        
        $cad .= '<div class="form-group"> <br/>';
        
        $cad .= '<div class="col-sm-8">';
        
        
        $cad .= '<div class="col-sm-12" style="clear: both;"> Encabezado del envío </div>';
        $cad .= '<div class="col-sm-12" style="min-height:4.8em;">
    <textarea id="de" class="form-control" name="de" placeholder="Cabecera del envio" style="z-index:1; width: 100%;" >' .$de .'</textarea>
  </div>';
              
              
              
              $cad .= '<div style="clear:both; height:2.4em;">';
              
              
              $cad .= '<div class="control-label col-sm-2"> Proyecto </div>';
              $cad .= '<div class="col-sm-4"><select id="proyecto" name="proyecto" class="form-control" onChange="">';
              $cad .= '<option value=""> &nbsp; --- Seleccione --- </option>';
              $sql5 = "select * from a.proyecto where st!='0';";
              $obj5s = Aux::findBySql($sql5)->all();
              foreach( $obj5s as $reg5 ){
                  if( $reg5->codigo == $cod_proyecto )
                      $cad .= '<option value="' .$reg5->codigo .'" selected="selected"> &nbsp; ' .$reg5->de .'</option>';
                  else
                      $cad .= '<option value="' .$reg5->codigo .'"> &nbsp; ' .$reg5->de .'</option>';
              }
              $cad .= '</select></div>
<div class="col-sm-1">&nbsp;</div>';
              

              $cad .= '<div class="control-label col-sm-2"> Estado </div>
<div class="col-sm-3"><select id="st" name="st" class="form-control" >';     
              $cad .= '<option value="1"> &nbsp; Activo</option>';
              $cad .= '<option value="3"> &nbsp; En espera</option>';
              $cad .= '<option value="0"> &nbsp; Eliminar</option>';
              $cad .= '</select></div>';
              



              
              $cad .= '</div><div style="clear:both; height:2.4em;">';



              $cad .= '<div class="control-label col-sm-2"> Instrumento </div>';
              $cad .= '<div id="div_x3_instrumento" class="col-sm-4"><select id="id_instrumento" name="id_instrumento" class="form-control" onChange="">';
              $cad .= '<option value="0"> &nbsp; --- INDIFERENTE --- </option>';
              $sqlInst = "select * from " .$cod_proyecto .".instrumento where st != '0';";
              $objInsts = Aux::findBySql($sql5)->all();
              foreach( $objInsts as $regInst ){
                  if( $regInst->id == $id_instrumento )
                      $cad .= '<option value="' .$regInst->id .'" selected="selected"> &nbsp; ' .$regInst->de .'</option>';
                  else
                      $cad .= '<option value="' .$regInst->id .'"> &nbsp; ' .$regInst->de .'</option>';
              }
              $cad .= '</select></div>
                  <div class="col-sm-1">&nbsp;</div>';


              
              $cad .= '<div class="control-label col-sm-2"> Reporte </div>';
              $cad .= '<div id="div_x3_reporte" class="col-sm-4"><select id="id_reporte" name="id_reporte" class="form-control" >';
              $cad .= '<option value="0"> &nbsp; --- Seleccione --- </option>';
              $sql1 = "select * from a.reporte where st='1';";
              $obj1s = Aux::findBySql($sql1)->all();
              foreach( $obj1s as $reg1 ){
                  if( $reg1->id == $id_reporte )
                      $cad .= '<option value="' .$reg1->id .'" selected="selected"> &nbsp; ' .$reg1->id .'. ' .$reg1->de .'</option>';
                  else
                      $cad .= '<option value="' .$reg1->id .'"> &nbsp; ' .$reg1->id .'. ' .$reg1->de .'</option>';
              }
              $cad .= '</select></div>';
              $cad .= '</div>';

              
              
              $cad .= '<div style="clear:both;"><br /><h4>Cronograma de envíos</h4></div>';
              $cad .= '  <div style="clear:both; height:2.2em;">
<div class="control-label col-sm-2" for="codigo">Lunes</div>
<div class="col-sm-1">&nbsp;</div>
<div class="control-label col-sm-2" for="codigo">Martes</div>
<div class="col-sm-1">&nbsp;</div>
<div class="control-label col-sm-2" for="codigo">Miércoles</div>
<div class="col-sm-1">&nbsp;</div>
<div class="control-label col-sm-2" for="codigo">Jueves</div>

</div>';
              
              /*
                $cad .= '<div class="input-group clockpicker" data-placement="right" data-align="top" data-autoclose="true">
                <input type="text" class="form-control" value="23:55">
                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                </div>';
              */
              $cad .= '  <div style="clear:both; height:2.2em;">
  <div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="l" class="form-control" name="l" value="' .$l .'" ' .$l .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>
<div class="col-sm-1">&nbsp;</div>
<div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="m" class="form-control" name="m" value="' .$m .'" ' .$m .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>
<div class="col-sm-1">&nbsp;</div>
<div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="i" class="form-control" name="i" value="' .$i .'" ' .$i .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>
<div class="col-sm-1">&nbsp;</div>
<div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="j" class="form-control" name="j" value="' .$j .'" ' .$j .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>
  </div>';


              $cad .= '  <div style="clear:both; height:2.2em;">
<div class="control-label col-sm-2" for="codigo">Viernes</div>
<div class="col-sm-1">&nbsp;</div>
<div class="control-label col-sm-2" for="codigo">Sábado</div>
<div class="col-sm-1">&nbsp;</div>
<div class="control-label col-sm-2" for="codigo">Domingo</div>
</div>';


$cad .= '  <div style="clear:both; height:2.2em;">
<div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="v" class="form-control" name="v" value="' .$v .'" ' .$s .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>
<div class="col-sm-1">&nbsp;</div>
<div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="s" class="form-control" name="s" value="' .$s .'" ' .$d .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>
<div class="col-sm-1">&nbsp;</div>
<div class="col-sm-2 clockpicker" data-autoclose="true">
    <input id="d" class="form-control" name="d" value="' .$d .'" ' .$l .' placeholder="Vacio" style="z-index:1; " type="text" />
  </div>';

              
  $cad .= '</div>';

              
              $cad .= '<script type="text/javascript">' ."\n";
              $cad .= '$(".clockpicker").clockpicker().find("input").change(function(){console.log(this.value);});' ."\n";
              $cad .= '</script>' ."\n";

              
              $cad .= '  <div style="clear:both; ">';
              $cad .= '<div class="col-sm-7">&nbsp;</div>
  <div class="col-sm-2">
    <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df">
      <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:100px;">   Recetear   </div>
    </a>
  </div>
  <div class="col-sm-2">
    <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:100px;">   Salvar   </button>
  </div>
 </div>';


        $cad .= '<br /><br /><hr /><br />';

        $cad .= $this->listado();

$cad .= '</div>';

              $cad .= '<div class="col-sm-1">&nbsp;</div>';
              $cad .= '<div class="col-sm-3">' .$this->usuarios( $cod_proyecto, $id ) .'</div>';
              
                   
$cad .= '</div>
</form>';
              return $cad;
    } // eof 
    




    
    /**
     * Opciones del select instrumento
     */
    public static function actionSelectinstrumento( $cod_proyecto = '', $id_envio = 0 ){    
        $cad .= '<div class="control-label col-sm-2"> Instrumento </div>';
        $cad .= '<div id="div_x3_instrumento" class="col-sm-4"><select id="id_instrumento" name="id_instrumento" class="form-control" onChange="">';
        $cad .= '<option value="0"> &nbsp; --- INDIFERENTE --- </option>';
        $sqlInst = "select * from " .$cod_proyecto .".instrumento where st != '0';";
              $objInsts = Aux::findBySql($sql5)->all();
              foreach( $objInsts as $regInst ){
                  if( $regInst->id == $id_instrumento )
                      $cad .= '<option value="' .$regInst->id .'" selected="selected"> &nbsp; ' .$regInst->de .'</option>';
                  else
                      $cad .= '<option value="' .$regInst->id .'"> &nbsp; ' .$regInst->de .'</option>';
              }
              $cad .= '</select></div>
                  <div class="col-sm-1">&nbsp;</div>';
              echo $cad;
    } // eof


    
    /**
     * opciones del select reporte
     */
    public static function actionSelectreporte( $cod_proyecto = '', $id_envio = 0 ){
        
        $cad .= '<div class="control-label col-sm-2"> Reporte </div>';
        $cad .= '<div id="div_x3_reporte" class="col-sm-4"><select id="id_reporte" name="id_reporte" class="form-control" >';
        $cad .= '<option value="0"> &nbsp; --- Seleccione --- </option>';
        $sql1 = "select * from a.reporte where st='1';";
        $obj1s = Aux::findBySql($sql1)->all();
        foreach( $obj1s as $reg1 ){
            if( $reg1->id == $id_reporte )
                $cad .= '<option value="' .$reg1->id .'" selected="selected"> &nbsp; ' .$reg1->id .'. ' .$reg1->de .'</option>';
            else
                $cad .= '<option value="' .$reg1->id .'"> &nbsp; ' .$reg1->id .'. ' .$reg1->de .'</option>';
        }
              $cad .= '</select></div>';
              $cad .= '</div>';
              echo $cad;
    } // eof 

    
    

    
    
    /**
     * Usuarios para los envios
     */
    public static function usuarios( $cod_proyecto = '', $id_envio = 0 ){
        $cad = '';
        $sql2 = '';
        if( $cod_proyecto != '' && $id_envio > 0 ){
            $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario, ee.id as chk
            from a.usuario u
            inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
            left join " .$cod_proyecto .".envio_email ee on ee.id_email = u.id and ee.id_envio='" .$id_envio ."' 
            where u.st = '1' and u.email != '' and u.email != 'NULL' order by usuario asc;";
            
            $sql2 = "select distinct e.id, e.email, e.de 
            from " .$cod_proyecto .".email e 
            left join " .$cod_proyecto .".envio_email ee on ee.id_email = e.id and ee.id_envio='" .$id_envio ."'
            where e.st = '1' order by e.email asc;";
        }else if( $cod_proyecto != '' ){
            $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario
            from a.usuario u
            inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
            where u.st = '1' and u.email != '' and u.email != 'NULL' order by usuario asc;";
            
            $sql2 = "select distinct e.id, e.email, e.de 
            from " .$cod_proyecto .".email e 
            where e.st = '1' order by e.email asc;";
        }else{
            $sql = "select distinct u.id, u.nombres || ' ' || u.apellidos as usuario
            from a.usuario u
            inner join auth_assignment aa on cast(aa.user_id as integer)=u.id 
            where u.st = '1' and u.email != '' and u.email != 'NULL' order by usuario asc;";
        }
        // id_envio 	id_email 	st
        
        $objs = Aux::findBySql($sql)->all();
        $cad .= '<h3> Destinatarios </h3>';
        $cad .= '<div> <input type="radio" id="destino_1" name="destino" value="1"/> &nbsp; Todos </div>';
        $cad .= '<div> <input type="radio" id="destino_2" name="destino" value="2"/> &nbsp; Personal de Soporte </div>';
        $cad .= '<div> <input type="radio" id="destino_3" name="destino" value="3"/> &nbsp; Teleoperadores </div>';
        $cad .= '<div> <input type="radio" id="destino_4" name="destino" value="4"/> &nbsp; Supervisores </div>';
        $cad .= '<div> <input type="radio" id="destino_5" name="destino" value="5"/> &nbsp; Clientes </div>';
        $cad .= '<hr /><div id="divEmailEnvio" style="clear: both;">';
        
        if( $sql2 != '' ){
            $obj2s = Aux::findBySql($sql2)->all();
            foreach( $obj2s as $obj2 )
                if( !is_null($obj2->chk) )
                    $cad .= '<div title="' .$obj2->de .'"> <input type="checkbox" name="usuario2_' .$obj2->id .'" checked="checked" value="1"/> &nbsp;' .$obj2->email .'</div>';
                else
                    $cad .= '<div title="' .$obj2->de .'"> <input type="checkbox" name="usuario2_' .$obj2->id .'" value="1"/> &nbsp;' .$obj2->email .'</div>';
        }
        
        $cad .= '</div><hr />';
        foreach( $objs as $obj )
            if( !is_null($obj->chk) )
                $cad .= '<div> <input type="checkbox" name="usuario_' .$obj->id .'" checked="checked" value="1"/> &nbsp;' .$obj->usuario .'</div>';
            else
                $cad .= '<div> <input type="checkbox" name="usuario_' .$obj->id .'" value="1"/> &nbsp;' .$obj->usuario .'</div>';
        return $cad;
    } // eof 
    
    
    
    /**
     * Correos en checkbox
     */
    public function actionEmailes(){
        $cad = '';
        $cod_proyecto = Request::rq('cod_proyecto') .'';
        $id_envio = (int)Request::rq('id_envio');
        
        if( $cod_proyecto != '' && $id_envio > 0 ){    
            $sql2 = "select distinct e.id, e.email, e.de 
            from " .$cod_proyecto .".email e 
            left join " .$cod_proyecto .".envio_email ee on ee.id_email = e.id and ee.id_envio='" .$id_envio ."'
            where e.st = '1'  order by e.email asc;";
        }else if( $cod_proyecto != '' ){
            $sql2 = "select distinct e.id, e.email, e.de 
            from " .$cod_proyecto .".email e 
            where e.st = '1' order by e.email asc;";
        }
        if( $sql2 != '' ){
            $obj2s = Aux::findBySql($sql2)->all();
            foreach( $obj2s as $obj2 )
                if( !is_null($obj2->chk) )
                    $cad .= '<div title="' .$obj2->de .'"> <input type="checkbox" name="usuario2_' .$obj2->id .'" checked="checked" value="1"/> &nbsp;' .$obj2->email .'</div>';
                else
                    $cad .= '<div title="' .$obj2->de .'"> <input type="checkbox" name="usuario2_' .$obj2->id .'" value="1"/> &nbsp;' .$obj2->email .'</div>';
        }
        return $cad;
    } // eof 
    
    
    
    
    
} // class
