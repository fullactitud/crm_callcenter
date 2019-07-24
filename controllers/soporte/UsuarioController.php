<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;

use app\components\crm\Mensaje;
use app\components\crm\Request;
use app\components\crm\Listado;
use app\components\crm\JSon;
use app\components\crm\Formulario;
use app\components\crm\Ayuda;
use app\components\crm\Perfil;

use app\models\Aux;

use app\models\UploadForm;
use yii\web\UploadedFile;

use yii\widgets\ActiveForm;

use app\vendor\PHPExcel\PHPExcel\IOFactory;

use yii\swiftmailer\Mailer;


/**
 * Controler para manejar Usuarios
 */
class UsuarioController extends Controller{
    
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [                
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    } // eof #######################################################

    
    
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
     * Procesos que se ejecutan antes que el controller carge
     */    
    function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();


        
        if( isset($_REQUEST['_lang']) ){
            Yii::$app->language = $_REQUEST['_lang'];
            Yii::$app->session['_lang'] = Yii::$app->language;
        }else if( isset(Yii::$app->session['_lang']) ){
            Yii::$app->language = Yii::$app->session['_lang'];
        }else{
            Yii::$app->language = 'es_ve';
            Yii::$app->session['_lang'] = Yii::$app->language;
        }
    } // eof #######################################################
            

    /**
     * Genera un código valido
     */
    public function codigo( $codigo ){        
        $codigo = str_replace(' ', '', $codigo);
        $codigo = str_replace("\t", '', $codigo);
        $codigo = str_replace("\n", '', $codigo);
        $codigo = str_replace('á', '', $codigo);
        $codigo = str_replace('ó', '', $codigo);
        $codigo = str_replace('í', '', $codigo);
        $codigo = str_replace('é', '', $codigo);
        $codigo = str_replace('ú', '', $codigo);
        $codigo = str_replace('ñ', '', $codigo);
        $codigo = str_replace('Á', '', $codigo);
        $codigo = str_replace('Ó', '', $codigo);
        $codigo = str_replace('Í', '', $codigo);
        $codigo = str_replace('É', '', $codigo);
        $codigo = str_replace('Ú', '', $codigo);
        $codigo = str_replace('Ñ', '', $codigo);
        $codigo = str_replace('-', '_', $codigo);
        $codigo = str_replace('&', '_', $codigo);
        $codigo = str_replace('#', '_', $codigo);
        $codigo = str_replace('%', '_', $codigo);
        $codigo = str_replace('$', '_', $codigo);
        $codigo = str_replace('/', '_', $codigo);
        $codigo = str_replace('(', '_', $codigo);
        $codigo = str_replace(')', '_', $codigo);
        $codigo = str_replace('=', '_', $codigo);
        $codigo = str_replace("'", '', $codigo);
        $codigo = str_replace('.', '_', $codigo);
        $codigo = str_replace(',', '_', $codigo);
        $codigo = str_replace(':', '_', $codigo);
        $codigo = str_replace(';', '_', $codigo);
        return $codigo;
    }


    /**
     * Agregar Usuario
     */
    public function actionAdd(){
        $username = trim(Request::rq('username'));
        if( $username == '' ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/df&', 302 );
        }else{ 
            $x3id = (int)Request::rq('x3id');
            $nombres = Request::rq('nombres').'';
            $apellidos = Request::rq('apellidos').'';
            $email = Request::rq('email').'';
            $st = Request::rq('st').'';
            $password = Request::rq('password').'';
            $foto = '';
            $rol = Request::rq('rol').'';
            if( $rol == '' ) $rol = '';
                 
            
            $sql = "WITH upsert AS 
(UPDATE a.usuario SET nombres='" .$nombres ."', apellidos='" .$apellidos ."', password='" .$password ."', email='" .$email ."', st='" .$st ."' WHERE username='" .$username ."' RETURNING *)
    INSERT INTO a.usuario ( username, nombres, apellidos, password, email, foto, st ) 
SELECT '" .$username ."', '" .$nombres ."', '" .$apellidos ."', '" .$password ."', '" .$email ."', '" .$foto ."', '" .$st ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
            Aux::findBySql($sql)->one();
            $sql = "select id from a.usuario where username='" .$username ."' order by id desc;";
            $obj = Aux::findBySql($sql)->one();
            
            // SUBIR ARCHIVO DE FOTO 
            $dir_subida = Yii::$app->basePath .'/web/img/foto/';
            $posicion = (int)strrpos($_FILES['foto']['name'], '.');
            if( $posicion > 0 ){
                $nombre = $obj->id .'' .substr($_FILES['foto']['name'], $posicion);
                $fichero_subido = $dir_subida .$nombre;
                if( move_uploaded_file($_FILES['foto']['tmp_name'], $fichero_subido) ){
                    Aux::findBySql("update a.usuario set foto='" .$nombre ."' where id='" .$obj->id ."';")->one();
                }
            }            

            
            
            $sql = "WITH upsert AS (UPDATE public.auth_assignment SET item_name='" .$rol ."' WHERE user_id='" .$obj->id ."' RETURNING *) INSERT INTO public.auth_assignment ( item_name, user_id ) SELECT '" .$rol ."', '" .$obj->id ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
            Aux::findBySql($sql)->one();
            
         



            if( !is_null($obj->id) ){
                Aux::findBySql("delete from a.usuario_proyecto where id_usuario='" .$obj->id ."';")->one();
                $objs = Aux::findBySql("select distinct codigo from a.proyecto p where p.st = '1' ;")->all();
                foreach( $objs as $p ){
                    if( !is_null(Request::rq('proyecto_' .$p->codigo)) )
                        Aux::findBySql("insert into a.usuario_proyecto (id_usuario, cod_proyecto) values ('" .$obj->id ."','" .$p->codigo ."');")->one();
                }
            }            
                

            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/df&x3id=' .$x3id, 302);
        }
    } // eof #######################################################
    
    
    /**
     * Acción por defecto de Usuario
     */    
    public function actionDf(){
        $x3id = (int)Request::rq('x3id');
        
        if( $x3id != 0 ){
            $obj = Aux::findBySql("select distinct * from a.usuario where st!='0' and id='" .$x3id ."'")->one(); 
            $aux_id = '<input type="hidden" id="x3id" name="x3id" value="' .$x3id .'">';
            $aux_id2 = $x3id;
            $aux_nombres = $obj->nombres;
            $aux_username = $obj->username;
            $aux_password = $obj->password;
            $aux_apellidos = $obj->apellidos;
            $aux_foto = $obj->foto;
            $aux_st = $obj->st;
            $aux_email = $obj->email;
            $aux2_codigo = ' onlyread="onlyread" ';
        }else{
            $aux_id = '<input type="hidden" id="x3id" name="x3id" value="">';
            $aux_id2 = '';
            $aux_nombres = '';
            $aux_username = '';
            $aux_password = '';
            $aux_apellidos = '';
            $aux_foto = '_.png';
            $aux_st = '1';
            $aux_email = '';
            $aux2_codigo = '';
        }
        
        $form = '';
        

        if( $aux_foto == '' ) $aux_foto = '_.png';
       
        
        
        
        $form .= '<div class="titulos"> Usuarios </div>';
        $form .= Mensaje::mostrar();
        $form .= Ayuda::toHtml('usuarios');
        $form .= '<hr/>';


        $form .= '<div class="col-sm-9">';
        

        $form .= '<form class="form-horizontal" id="datosdelusuario" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/add" method="POST" enctype="multipart/form-data">
' .$aux_id .'


<div class="form-group"> <br/>
  <div class="col-sm-2"><img src="' .Yii::$app->params['baseUrl'] .'img/foto/' .$aux_foto .'" style="width:100%;" alt="' .$aux_id2 .'" title="' .$aux_id2 .'" /></div>
  <div class="col-sm-10">
    <div class="form-group">
      <div class="control-label col-sm-2">Usuario</div>
      <div class="col-sm-3">
        <input id="username" class="form-control" name="username" value="' .$aux_username .'" placeholder="Nombre de usuario" style="z-index:1; width: 100%;" type="text" />
      </div>
      <div class="col-sm-1">&nbsp;</div>
      <div class="control-label col-sm-2">Contraseña</div>
      <div class="col-sm-3">
        <input id="password" class="form-control" name="password" value="' .$aux_password .'" ' .$aux2_codigo .' placeholder="Contraseña" style="z-index:1; width: 100%;" type="text" />
      </div>
    </div>

    <div class="form-group">
      <div class="control-label col-sm-2" >Nombres</div>
      <div class="col-sm-3">
        <input id="nombres" class="form-control" name="nombres" value="' .$aux_nombres .'" placeholder="Nombres" style="z-index:1; width: 100%;" type="text" />
      </div>
      <div class="col-sm-1">&nbsp;</div>
      <div class="control-label col-sm-2" >Apellidos</div>
      <div class="col-sm-3">
        <input id="apellidos" class="form-control" name="apellidos" value="' .$aux_apellidos .'" placeholder="Apellidos" style="z-index:1; width: 100%;" type="text" />
      </div>
    </div>


    <div class="form-group">
      <div class="control-label col-sm-2" >Email</div>
      <div class="col-sm-3">
        <input id="email" class="form-control" name="email" value="' .$aux_email .'" placeholder="Correo electrónico" style="z-index:1; width: 100%;" type="text" />
      </div>
      <div class="col-sm-1">&nbsp;</div>
      <div class="control-label col-sm-2" >Estatus</div>
      <div class="col-sm-3" style="font-size:0.8em;">';
        if( $aux_st == 1 ){
            $form .= '<input type="radio" id="st_1" class="for4m-control" name="st" value="1" checked="checked" /> Activo &nbsp; &nbsp; 
        <input type="radio" id="st_3" class="fo4rm-control" name="st" value="3" /> En espera';
        }else if( $aux_st == 3 ){
            $form .= '<input type="radio" id="st_1" class="for4m-control" name="st" value="1" /> Activo &nbsp; &nbsp; 
        <input type="radio" id="st_3" class="fo4rm-control" name="st" value="3" checked="checked" /> En espera';           
        }else{
            $form .= '<input type="radio" id="st_1" class="for4m-control" name="st" value="1" /> Activo &nbsp; &nbsp; 
        <input type="radio" id="st_3" class="fo4rm-control" name="st" value="3" /> En espera';
        }
        
        $form .= '
      </div>
    </div>


    <div class="form-group">
      <div class="control-label col-sm-2" >Foto</div>
      <div class="col-sm-3">
        <input id="foto" class="form-control" name="foto" placeholder="Foto" style="z-index:1; width: 100%;" type="file" />
      </div>

      <div class="col-sm-1" >&nbsp;</div>
      
      <div class="control-label col-sm-2" >Rol</div>
      <div class="col-sm-3">
        <select id="rol" class="form-control" name="rol" style="z-index:1; width: 100%;" />' .Perfil::optionSelect( (int)$aux_id2 ) .'</select>
      </div>
      
    </div>


    <div class="form-group"> <br/>
      <div class="col-sm-2">&nbsp;</div>
      <div class="col-sm-3">
        <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/df">
        <div class="ui-button ui-corner-all ui-widget" id="btnnuevo" style="width:100%;">   Recetear   </div>
        </a>
      </div>
      <div class="col-sm-3">&nbsp;</div>
      <div class="col-sm-3">
        <button type="submit" class="ui-button ui-corner-all ui-widget" id="btnsalvar" style="width:100%;">   Salvar   </button>
      </div>
    </div>
  </div>
  </div>
';


        //               $ayuda2 = '<img src="img/icons/help.png" title="Mostrar Ayuda" style="height:1.5em; cursor:hand;cursor:pointer;"  onclick="if($(\'#x3ayuda2\').css(\'display\') == \'block\'){$(\'#x3ayuda2\').hide();}else{$(\'#x3ayuda2\').show();}" />';
            
            
            $form .= '<hr/>';
        $form .= '<div class="subtitulos"><u>Listado</u> </div>';


        $stt[0] = 'Eliminado';
        $stt[1] = 'Activo';
        $stt[2] = 'Pendiente';
        $stt[3] = 'En espera';
        

        $data[] = 'ID';
        $data[] = 'Foto';
        $data[] = 'Nombre';
        $data[] = 'Nick';
        $data[] = 'Email';
        $data[] = 'Estatus';
        $data[] = '';
        $data[] = '';
        
        $linea[] = $data;
        unset($data);
        $registros = Aux::findBySql("select id,foto,nombres,apellidos,username,email,st from a.usuario where st != '0' order by nombres asc, apellidos asc")->all();
        foreach( $registros as $reg ){
            $data[] = $reg->id;
            if( !is_null($reg->foto) && $reg->foto!='' )
                $data[] = '<img style="height: 3.0em;" src="' .Yii::$app->params['baseUrl'] .'img/foto/' .$reg->foto .'" />';
            else
                $data[] = '';
            $data[] = $reg->nombres.' '.$reg->apellidos;
            $data[] = $reg->username;
            $data[] = $reg->email;
            $data[] = $stt[$reg->st];
            $data[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/df&x3id=' .$reg->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reg->nombres.' '.$reg->apellidos .'"/></a>';
            $data[] = '<a href="javascript:if( confirm(\'Eliminar el usuario: ' .$reg->nombres.' '.$reg->apellidos .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/eliminar1&x3id=' .$reg->id .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reg->nombres.' '.$reg->apellidos .'"/></a>';
            $linea[] = $data;
            unset($data);
        }

        
        $form .= '<div style="width:100%;">' .Listado::listado( $linea, 'Listado de Usuarios' ) .'</div>';
        $form .= '</div>';
        $form .= '<div class="col-sm-3">';
        $form .= $this->proyectos( $aux_id2 );
        $form .= '</div>';
        $form .= '</form>';
        

        return $this->render('@views/df/txt',array( 'txt'=>$form ));
    } // eof #######################################################


    /**
     * Proyectos disponibles
     */       
    public static function proyectos( $id_usuario = '' ){
        $cad = '';
        $sql2 = '';
        if( $id_usuario != '' ){
            $sql = "select distinct p.codigo, p.de, up.id as chk
            from a.proyecto p
            left join a.usuario_proyecto up on up.cod_proyecto = p.codigo and up.id_usuario='" .$id_usuario ."' and up.st = '1'
            where p.st = '1' order by p.de asc;";
        }else{
            $sql = "select distinct p.codigo, p.de
            from a.proyecto p
            where p.st = '1' order by p.de asc;";
        }
        
        $objs = Aux::findBySql($sql)->all();
        $cad .= '<h3> Acceso a Proyectos </h3>';

        foreach( $objs as $obj )
            if( !is_null($obj->chk) )
                $cad .= '<div> <input type="checkbox" name="proyecto_' .$obj->codigo .'" checked="checked" value="1"/> &nbsp;' .$obj->de .'</div>';
            else
                $cad .= '<div> <input type="checkbox" name="proyecto_' .$obj->codigo .'" value="1"/> &nbsp;' .$obj->de .'</div>';
        return $cad;
    } // eof 


    
    /**
     * Elimina un usuario
     */
    public function actionEliminar1(){
        $x3id = Request::rq('x3id');
        $sql = "delete from a.usuario where id= '" .$x3id ."';";
        Aux::findBySql($sql)->one();
        $txt = 'Usuario eliminado.';
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/df&x3txt=' .$txt, 302);
    }

    
} // class
