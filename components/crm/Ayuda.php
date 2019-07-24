<?php
namespace app\components\crm;
use Yii;
use app\models\Aux;

/**
 * Maneja los textos de las ayudas
 */
class Ayuda{

    /**
     * Despliega la ayuda de forma oculta
     */
    public static function info1( $vct, $codigo ){
        $cad = '<div id="divAyuda0" style="clear:both; display: none;"><div class="alert alert-info">';
        $cad .= '<div class="subtitulos"> &nbsp; &nbsp; <u> ' .self::vector($codigo) .'</u> </div><hr />';
        foreach( $vct as $vector ){
           
            $cad .= '<ul style="padding-left:2.0em;">';
            foreach( $vector as $k => $v ){
                if( $k == 0 )
                    $cad .= '<strong>' .$v .'</strong>';
                else if( $k > 0 )
                    $cad .= '<li>' .$v .'</li>';
            }
            $cad .= '</ul><br />';
        }                   
        $cad .= '<hr /></div></div>';
        return $cad;
    } // eof 


    /**
     * Despliega la ayuda de forma visible
     */
    public static function info2( $vct, $cadena ){
        $cad = '<hr /><div style="clear:both; display: block;"><div class="content content-info">';
        $cad .= '<div class="subtitulos"> &nbsp; &nbsp; <u>' .$cadena .'</u> </div><br />';
        foreach( $vct as $vector ){
           
            $cad .= '<ul style="padding-left:2.0em;">';
            foreach( $vector as $k => $v ){
                if( $k == 0 )
                    $cad .= '<strong>' .$v .'</strong>';
                else if( $k > 0 )
                    $cad .= '<li>' .$v .'</li>';
            }
            $cad .= '</ul><br />';
        }                   
        $cad .= '</div></div>';
        return $cad;
    } // eof 
    

    /**
     * Rellena el vector de ayudas posibles
     */    
    public static function vector( $p = null ){
        $v['cuota'] = 'Página para manejar las cuotas';
        $v['data'] = 'Página para cargar la data';
        $v['dominios'] = 'Página para manejar los dominios';
        $v['envios'] = 'Página para manejar los envios';
        $v['instrumento'] = 'Página para crear instrumentos';
        $v['panel'] = 'Página Panel o Menú';
        $v['perfiles'] = 'Página para manejar perfiles';
        $v['proyectos'] = 'Página para manejar Proyectos';
        $v['teleoperar'] = 'Página para teleoperar';
        $v['reporte'] = 'Página para manejar los reportes';
        $v['reportes'] = 'Reporte';
        $v['reportes_panel'] = 'Página para acceder a los reportes validos según el instrumento seleccionado';
        $v['reportegeneral'] = 'Página del Reporte General';
        $v['ruta'] = 'Página para mostrar la ruta de las preguntas';
        $v['usuarios'] = 'Página para manejar usuarios';
        $v['tipificacion'] = 'Página para manejar las tipificaciones por proyecto';
        $v['mensaje'] = 'Página para manejar los mensajes entre usuarios';
        $v['mensaje_a_soporte'] = 'Página que permite enviar un mensaje a soporte';
        $v['instrumentos'] = 'Página que muestra los instrumentos disponibles';
        $v['llamada'] = 'Página que muestra las llamadas activas';
        $v['llamar_luego'] = 'Página para agendar una llamada ';
        $v['panel_tlo'] = 'Página Panel de Teleoperadores';
        $v['panel_clientes'] = 'Página Panel de clientes';
        $v['barrida'] = 'Página para actualizar la barrida';
        if( is_null($p) )
            return $v;
        else
            return $v[$p];
    } // eof 

    
    /**
     * Agrega los textos de la ayuda en un vector
     */
    public static function toHtml( $codigo, $cadena = '' ){
        $height0 = '1.5em';
       switch( $codigo ){


       case 'mensaje': // ################################################################
          
           $ayuda[] = 'Formulario de Mensajes';
           $ayuda[] = 'Permite agregar un mensaje que se le mostrará a uno, ó a varios usuarios';
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Listado de Mensajes';
           $ayuda[] = 'Todos los mensajes registrados';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;

       case 'mensaje_a_soporte': // ################################################################
           
           $ayuda[] = 'Formulario de Mensajes';
           $ayuda[] = 'Permite agregar un mensaje que se le mostrará al personal de soporte';
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Listado de Mensajes';
           $ayuda[] = 'Todos los mensajes registrados del usuario';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;

           
       case 'cuota': // ################################################################
           
           $ayuda[] = 'Formulario de Cuotas';
           $ayuda[] = 'Permite agregar una cuota para un instrumento';
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Listado de Cuotas';
           $ayuda[] = 'Todos los cuotas registradas para un instrumento';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;


       case 'data': // ################################################################
          
           $ayuda[] = 'Formulario para cargar la Data';
           $ayuda[] = 'Permite agregar prospectos para ser teleoperado por un instrumento.';
           $ayuda[] = 'Debe seleccionar de su computador el archivo que desea enviar al servidor.';
           
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Listado de carga de data';
           $ayuda[] = 'Historico de la carga de data registradas en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;
           
           
       case 'dominios': // ################################################################
          
           $ayuda[] = 'Formulario de Dominios';
           $ayuda[] = 'Permite agregar o editar los datos de un dominio en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Listado de Dominios';
           $ayuda[] = 'Todos los dominios registrados en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;
           
       case 'envios': // ################################################################
          
           $ayuda[] = 'Formulario de Envíos';
           $ayuda[] = 'Permite agregar o editar los datos de un envío en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Listado de Envios';
           $ayuda[] = 'Todos los envios registrados en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;
           
           
       case 'instrumento': // ################################################################
           
           $fontTb = '';
           $cad = '<style>';
           $fonts = Aux::findBySql("select * from a.font where st='1' order by de asc")->all();
           $i = 0;
           $on = 0;
           foreach( $fonts as $font ){
               if( $font->tipo == 'ttf' || $font->tipo == 'css' ){
                   $i++;
                   if( $font->tipo == 'ttf' )
                       $cad .= '@font-face{ font-family: ' .$font->de .'; src: url("font/' .$font->de .'.ttf"); }';
                   if( $on == 0 ){
                       $fontTb .= '<tr>';
                       $on = 1;
                   }
                   $fontTb .= '<td style="font-family: \'' .$font->de .'\';">' .$font->de .'</td>';
                   if( $i > 2 ){
                       $i = 0;
                       $fontTb .= '</tr>';
                       $on = 0;
                   }
               }
           }
           if( $on == 1 )
               $fontTb .= '</tr>';
           $cad .= '</style>';


           
           $BootstrapTb = '';           
           $Bootstraps = Aux::findBySql("select de,imagen from a.bootstrap where st='1' order by de asc")->all();
           $i = 0;
           $on2 = 0;
           foreach( $Bootstraps as $Bootstrap ){
               $i++;
               if( $on2 == 0 ){
                   $BootstrapTb .= '<tr>';
                   $on2 = 1;
               }
               $BootstrapTb .= '<td>' .$Bootstrap->de .'<br /><img src="img/bootstrap/' .$Bootstrap->imagen .'" title="' .$Bootstrap->de .'" style="width:150px;"/><br /><br /></td>';
               if( $i > 3 ){
                   $i = 0;
                   $BootstrapTb .= '</tr>';
                   $on2 = 0;
               }
           } // for 
           if( $on2 == 1 )
               $BootstrapTb .= '</tr>';


           
           $JQueryTb = '';
           $JQuerys = Aux::findBySql("select de,imagen from a.jqueryui where st='1' order by de asc")->all();
           $i = 0;
           $on3 = 0;
           foreach( $JQuerys as $JQuery ){
               $i++;
               if( $on3 == 0 ){
                   $JQueryTb .= '<tr>';
                   $on3 = 1;
               }
               $JQueryTb .= '<td>' .$JQuery->de .'<br /><img src="img/jqueryui/' .$JQuery->imagen .'" title="' .$JQuery->de .'" style="width:150px;"/><br /><br /></td>';
               if( $i > 3 ){
                   $i = 0;
                   $JQueryTb .= '</tr>';
                   $on3 = 0;
               }
           }
           if( $on3 == 1 )
               $JQueryTb .= '</tr>';



           

           

           
           $ayuda[] = 'Instrumento' .$cad;
           
           $ayuda[] = 'Representa un cuestionario de un estudio descriptivo, junto con su comportamiento.';
           $vAyuda[] = $ayuda; unset($ayuda);

           $ayuda[] = 'Configuración';
           
           
           $ayuda[] = '<i>Proyecto</i>.<br /> Debe seleccionar el Proyecto-Cliente al cual pertenece el instrumento';
           $ayuda[] = '<i>Administrador</i>.<br />El usuario administrador del instrumento, solo seleccione si es diferente de admin o de soporte';
           $ayuda[] = '<i>Estatus</i>.<br />Estado del instrumento, coloquele “En espera” si no desee que este activo';
           $ayuda[] = '<span class="rojo" title="Obligatorio"><i>Descripción del Instrumento</i></span>.<br />Título del instrumento, <span>Es obligatorio</span>';
           $ayuda[] = '<i>Código</i>.<br />Código representativo del instrumento, no debe tener espacios, acentos, eñes, signos de puntuación. Si desea que el sistema lo asigne, dejalo vacio';
           $ayuda[] = '<i>Tipo de Instrumento</i>.<br />Seleccione el que más se adecue a sus necesidades';
                      
           
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Presentación';
           $ayuda[] = '<i>Tema JQuery</i>. Estos temas controlan como se ven ls barras y elementos JQuery.<br /><table style="width:90%; position:relative; left:3.0em; font-size:1.0em; line-height: 1.0em; text-align:center;"><tbody>' .$JQueryTb .'</tbody></table><br />';

           
           $ayuda[] = '<i>Tema Bootstrap</i>. Estos temas controlan como se ven los botones.<br /><table style="width:90%; position:relative; left:3.0em; font-size:1.0em; line-height: 1.0em; text-align:center;"><tbody>' .$BootstrapTb .'</tbody></table><br />';


           $ayuda[] = '<i>Tipo de Letra</i>. Para ser usada en los titulos y subtitulos.<br /><table style="width:90%; position:relative; left:3.0em; font-size:1.5em; line-height: 1.5em;"><tbody>' .$fontTb .'</tbody></table><br />';
        
        
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Comportamiento.<br />';
           $ayuda[] = 'Desplegar.<br />Establece como se desplegaran las preguntas del instrumento.';
           $ayuda[] = 'Siguiente.<br />Establece cómo será seleccionado el prospecto siguiente. Por defecto es aleatorio.';
           $ayuda[] = 'Retroceder.<br />Establece si el teleoperador puede retroceder en el instrumento.';
           $ayuda[] = 'LLamar Luego.<br />Habilita la posibilidad de que los prospectos sean almacenados para ser llamados luego.';
           $ayuda[] = 'Uso de dominios.<br />Habilita el uso de dominios (zonas, regiones, o cualquier otra agrupación de prospectos).';
           $ayuda[] = 'Uso de cuotas.<br />Habilita el uso de cuotas por día.';
           $ayuda[] = 'Entrada Inicial.<br />Si la entrada inicial (pregunta o texto) no es la primera registrada, debe especificarse. Debe usar el código de la pregunta y no el número.';
           
           
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Carga de los datos';
           $ayuda[] = 'Datos que se cargan desde archivo (.xls, .xlsx)';
           
           

        


           $ayuda[] = 'Campos.<br /><ul style="position:relative; left: 3.0em;">
        
        <li>Data.<br /> Nombre del campo o cabecera</li>
        <li>Columna.<br /> Columna o posición en el archivo (hoja de cálculo)</li>
        <li>Mostrar.<br /> Permite mostrar el dato al teleoperador durante la realización de la encuesta</li>
        <li>Editar.<br /> Permite que el teleoperador edite el dato, durante la realización de la encuesta</li>
        <li>Borrar.<br /> Si selecciona esta casilla, este dato no será tomado en cuenta al salvar el instrumento</li>
</ul>';
           
           $ayuda[] = 'Campos precargados<br />
 
 <ol style="position: relative; left: 3.0em;">
<li>Nombre del Prospecto.<br /></li>
<li>Telefono.<br /></li>
<li>Fecha de REF.<br /></li>
<li>Agente.<br />Es una forma de agrupar los prospectos que representa una oficina.</li>
<li>Dominio.<br />Es la zona, región o cualquier otra forma adicional de agrupar los prospectos.</li> 
';
       
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Reportes permitidos';
           $ayuda[] = 'Seleccionar los reportes que desea permitir para el instrumento.';
           $ayuda[] = 'Debe seleccionar reporte genéricos, ó debe verificar que el reporte (no generico) seleccionado es válido para la configuración del instrumento.';
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Entradas';
           $ayuda[] = 'Representa un pregunta o un solo un texto que se desea mostrar';
           $ayuda[] = 'Campos <ul style="position: relative; left: 3.0em;">




<li><i>Tipo de campo</i><ol style="position:relative; left: 2.0em;">
<li>Texto sin pregunta<br /> Título de sección ó texto para aclarar algo</li>
<li>Pregunta para obtener un texto como respuesta<br /> Introducir texto, genera un campo textarea para que el usuario introduzca el texto de la respuesta</li>
<li>Lista desplegable, genera una lista de selección<br /> </li>
<li>Lisŧa para hacer chequear un sola una opción<br /> </li>
<li>Lista para hacer chequear una o varias opciones<br /> </li>
</ol></li>
<li><i>Identificador</i><br /> Es el código que representa la pregunta. Si su instrumento no lo requiere, déjelo en blanco y se creará uno para uso interno del sistema</li>
<li><i>Pregunta ó texto</i><br /> Texto o pregunta que verá el teleoperador</li>
<li><i>Hace efectiva la encuesta</i><br /> </li>
<li><i>Entrada siguiente por defecto</i><br /> </li>
<li><i>Requiere</i>
   Entrada 01</li>
<li>Opciones  
<br />Agregar Opción

    		
<ul style="position:relative; left: 2.0em;">
<li><i>Text de la opción</i><br /> Texto que se muestra al teleoperador / prospecto</li>
<li><i>Valor de la opción</i><br /> Texto que se guarda en Base de Datos</li>
<li><i>Al seleccionar ir a</i><br /> Entrada que será la siguiente, si se selecciona esta opción. Por defecto toma la <u>entrada siguiente</u> declarada para la entrada en curso</li>
      	<li><i>Eliminar opción</i><br /> No tomar esta opción en cuenta al salvar el instrumento</li>
</ul></li>

<li>Eliminar
Marcar esta entrada para ser eliminada al salvar.</li>
</ul>
';
        
           $vAyuda[] = $ayuda; unset($ayuda);
           
           break;
           


           
       case 'panel': // ################################################################
           
           $ayuda[] = 'Panel-Menú';
           $ayuda[] = 'Permite acceder a las diferentes posibilidades que posee el sistema para el tipo de usuario logeado.';
           $ayuda[] = 'Para regresar al <i>panel - menú</i>, existe un enlace en el lado izquierdo de la barra superior.';
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Herramientas';
           $ayuda[] = '<b><i>Proyectos</i></b>.<br /> Permite la creación de proyectos ( Clientes )';
           $ayuda[] = '<b><i>Instrumentos</i></b>.<br /> Permite la creación y edición de un cuestionario para una investigación descriptiva.';
           $ayuda[] = '<b><i>Reportes</i></b>.<br /> Permite la creación y edición de reportes genéricos sencillos. Los reportes complejos requieren del desarrollo de un programador.';
           $ayuda[] = '<b><i>Usuarios</i></b>.<br /> Permite la creación y edición de usuarios.';
           $ayuda[] = '<b><i>Perfiles</i></b>.<br /> Permite la creación y edición de Perfiles - Roles de usuarios.';
           $ayuda[] = '<b><i>Reporte General</i></b>.<br /> Muestra en estado de todos los instrumentos activos.';
           $ayuda[] = '<b><i>Envios</i></b>.<br /> Creación de envío de reportes'; 
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Instrumentos';
           $ayuda[] = 'Visualiza los instrumentos que están activos, permitiendo las siguientes acciones:';
           $auxAY = '<ol style="position: relative; left: 2.0em;">
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/data.png" style="height:' .$height0 .';"/>
 Carga de data</li>
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/cuota.png" style="height:' .$height0 .';"/>
 Carga de cuota</li>
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/ruta.png" style="height:' .$height0 .';"/>
 Ver las rutas de las preguntas</li>
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/teleoperar.png" style="height:' .$height0 .';"/>
 Teleoperar</li>
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/reportes.png" style="height:' .$height0 .';"/>
 Ver listado de reportes activos para el instrumento</li>
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="height:' .$height0 .';"/>
 Editar el instrumento</li>
<li><img src="'.Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="height:' .$height0 .';"/>
 liminar el instrumento</li>
        </ol>';
           $ayuda[] = $auxAY;
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = '<a href="index.php?r=crm/ayuda/df"> Click aquí para acceder a la documentación completa. </a>';
           $vAyuda[] = $ayuda; unset($ayuda);

           break;
           
       case 'perfiles': // ################################################################
          
           $ayuda[] = 'Formulario de Pefiles';
           $ayuda[] = 'Permite agregar o editar los datos de un perfil en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           $ayuda[] = 'Listado de Perfiles';
           $ayuda[] = 'Todos los perfiles registrados en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;




           
       case 'proyectos': // ################################################################
          
           $ayuda[] = 'Proyecto';
           $ayuda[] = 'Agrupa los instrumentos de un cliente, bajo un único schema de Base de Datos.';
           $ayuda[] = 'Los instrumentos agrupados en un schema pueden compartir prospectos.';
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Campos para agregar un proyecto';
           $ayuda[] = 'Descripción. El único campo obligatorio es la descripción (nombre) del proyecto (cliente).';
           $ayuda[] = 'Logo. El logo del proyecto es opcional, pero mejora la presentación.';
           $ayuda[] = 'Código. si no es asignado el sistema asignará uno por defecto. Para este identificador, solo se permiten letras y números, NO debe tener espacios intermedios.';
           $ayuda[] = 'Usuario administrador. Es quien tendrá permisos para modificar la configuración del proyecto (cliente).';
           $ayuda[] = 'País. Denota el país en donde se encuentra radicado el cliente.';
           $ayuda[] = 'Activo. Si activa un proyecto, este será visible para los TLO.';
           $vAyuda[] = $ayuda; unset($ayuda);   
           
           $ayuda[] = 'Lista de los proyectos registrados';
           $ayuda[] = 'Al hacer clic sobre uno de ellos, los datos del proyecto se cargarán en el formulario ubicado en la parte superior, para poder actualizar los datos del proyecto.';
           $ayuda[] = 'El campo código NO se puede modificar.';
           $vAyuda[] = $ayuda; unset($ayuda);
           
           
        break;



       case 'teleoperar': // ################################################################
         
           $ayuda[] = 'Teleoperación';
           $ayuda[] = '';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;

        

       case 'reporte': // ################################################################
          
           $ayuda[] = 'Reporte';
           $ayuda[] = 'Muestra los datos almacenados en el sistema por medio de los instrumentos.';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;

        
       case 'reportes': // ################################################################
          
           $ayuda[] = 'Formulario de Reportes';
           $ayuda[] = 'Permite agregar o editar los datos de un reporte en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Listado de Reportes';
           $ayuda[] = 'Todos los reportes registrados en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;


       case 'reportes_panel': // ################################################################
          
           $ayuda[] = 'Listado de Reportes';
           $ayuda[] = 'Los reportes activados para este instrumento';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;
           
       case 'reportegeneral': // ################################################################
           
           $ayuda[] = 'Reporte General';
           $ayuda[] = 'Resumen de las últimas encuestas efectivas por cada instrumento';
           
           $vAyuda[] = $ayuda; unset($ayuda);
           break;


       case 'ruta': // ################################################################
           
           $ayuda[] = 'Ruta ';
            $ayuda[] = 'Establece los caminos que puede tomar el seguiento de las entradas (preguntas y textos ) en el instrumento';        
           $vAyuda[] = $ayuda; unset($ayuda);
           break;
           


           
       case 'usuarios': // ################################################################
          
           $ayuda[] = 'Formulario de Usuarios';
           $ayuda[] = 'Permite agregar o editar los datos de un usuario del sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           
           $ayuda[] = 'Listado de Usuarios';
           $ayuda[] = 'Todos los usuarios registrados en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           break;
           

       case 'llamada': // ################################################################
          
           $ayuda[] = 'Listado de llamadas';
           $ayuda[] = 'Llamadas activas en el sistema';
           $vAyuda[] = $ayuda; unset($ayuda);
           

           break;


       case 'llamar_luego': // ################################################################
          
           $ayuda[] = 'Agendar Llamada';
           $ayuda[] = 'Agendar una nueva llamada para un prospecto';
           $vAyuda[] = $ayuda; unset($ayuda);
           

           break;
           
           
       default: // ################################################################
           $vAyuda = array();
           break;
       }




       


       if( $cadena == '' )
           $cad = Ayuda::info1( $vAyuda, $codigo );
       else
           $cad = Ayuda::info2( $vAyuda, $cadena );
       unset($vAyuda);
       return $cad;
    }



    /**
     * Despliega el indice de la ayuda
     */
    public static function indice(){
        $cad = '';
        $cad .= '<hr />';
        $cad .= '<h2> Índice </h2>';
        $cad .= '<div><ol>';
        
        $cad .= '<li><a href="#descripcion">Descripción</a></li>';
        $cad .= '<li><a href="#requerimientos">Requerimientos</a></li>';
        $cad .= '<li><a href="#paradigmas">Paradigmas</a></li>';
        $cad .= '<br />';
        
        $cad .= '<h3>Preguntas Frecuentes</h3>';
        
        
        $cad .= '<br />';
        $cad .= '<h3>Páginas</h3>';
        
        $vct = self::vector();
        foreach( $vct as $k => $v )
            $cad .= '<li><a href="#' .$k .'">' .$v .'</a></li>';
        
        $cad .= '</ol></div>';
        $cad .= '<hr />';
        return $cad;
    } // eof 
    
    
    /**
     * Inicio Rapido del sistema
     */    
    public static function inicio(){
        $cad = '<div>
<a name="descripcion"><h3>Inicio Rapido</h3></a>
<ol style="position:relative;left: 3.0em;">
<li> Instalar el sistema, 
<ol style="position:relative;left: 3.0em;">
<li> Copiar la aplicación al directorio público de Apache</li>
<li> Crear la Base de Datos en Postgresql </li>
<li> Activar el cron</li>
</ol></li>
<li> Cree un <b>Proyecto</b>, que sera su cliente</li> 
<li> Cree un <b>Instrumento</b></li>
<li> Agregue tipificaciones al Proyecto</li>
<li> Agregue Dominios si los necesita</li>
<li> Agregue reportes</li>
<li> Agregele prospectos al instrumento, cargado la <b>data</b> desde un archivo (hoja de calculo) </li>
<li> Active los prospectos </li>

<li> Los perfiles viene precargados</li>
<li> Puede usar el usuario admin para hacer sus pruebas. Si lo desea puede agregar mas usuarios</li>

<li> Puede enviar un reporte por correo electrónico, agregando un envio</li>

</ol>

</div>';
        
        return $cad;
    } // eof 

    
    
    /**
     * Descripción del sistema
     */
 public static function descripcion(){
        $cad = '<div>
<a name="descripcion"><h3>Descripción</h3></a>

Customer Relationship Managment (<b>CRM</b>)<br />
<p>Es una estrategia de negocio orientada a la fidelización de clientes.
Permite a todos los empleados de una empresa disponer de información actualizada sobre los mismos, con el objetivo de optimizar la relación entre empresa/cliente. Además, ayuda a conocer todos los diferentes puntos de contacto con los cuales el cliente interactúa en la empresa.</p>



<p>Un factor clave para el exito de una compañia es la relación con el cliente, la misma aumenta losingresos y la calidad de servicios de la empresa y permite tener un manejo centralizado de información de contactos. Es por esto que para promover y simplificar este proceso se utilizan estrategias de CRM efectivas.</p>


Metodología
<p>Se trata de utilizar la tecnología para organizar, automatizar y sincronizar los procesos de negocio, principalmnente actividades de ventas, de comercialización, servicio al cliente y soporte técnico.</p>
Objetivos:
<li>Atreer y retener clientes
<li>reducir costos de marketing y servicio al cliente.





</div>';

        return $cad;
    }

    /**
     * Requerimientos el sistema
     */    
    public static function requerimientos(){
        $cad = '<div>
<a name="requerimientos"><h3>Requerimientos</h3></a>
<ol>

<li>El sistema se puede instalar en cualquier plataforma, preferiblemente en Linux para ponerlo a interactuar con el CRON
<ul><li>Ha sido probado en Linux Debian 8.7</li></ul>
</li>

<li>Servidor Web
<ul><li>Apache 2.x</li></ul>
</li>

<li> Sistema de Bases de Datos
<ul><li>Probado en Postgresql 9.4</li></ul>
</li>



</ol>
</div>';


        return $cad;
    }
    
    /**
     * Paradigmas del sistema
     */
    public static function paradigmas(){
        $cad = '<div>   
<h3>Descripción Técnica</h3>
 
El CRM es una aplicación web que comprende varias funcionalidades para gestionar las encuestas y los clientes de la empresa.<br/>
Esta basado en
<ul style="position: relative; left: 2.0em;">
<li>YiiFramework 2.0</li>
<li>Postgresql</li>
<li>JQuery</li>
<li>JQuery-ui</li>
<li>JQgrid</li>
<li>Bootstrap</li>
 </ul>
Programado con los paradigmas:
<ul style="position: relative; left: 2.0em;">
<li>Aplicación Cliente - Servidor</li>
<li>Framework MVC
<ul style="position: relative; left: 2.0em;">
<li>Modelo, </li>
<li>Vista, </li>
<li>Controlador, </li>
</ul>
</li>
<li>POO (Programación Orientada a Objetos)</li>
<li>SOLID
<ul style="position: relative; left: 2.0em;">
<li>Principio de responsabilidad única. Un objeto solo debería tener una única responsabilidad.</li>
<li>Principio de abierto/cerrado. Entidades deben estar abiertas para su extensión, Cerradas para su modificación.</li>
<li>Principio de sustitución de Liskov. Los objetos de un programa deberían ser reemplazables por instancias de sus subtipos sin alterar el correcto funcionamiento del programa.</li>
<li>Principio de segregación de la interfaz. Interfaces cliente específicas, no una interfaz de propósito general.</li>
<li>Principio de inversión de la dependencia. Depender de abstracciones, no depender de implementaciones.</li>
 </ul>
</li>
 </ul>



</div>';
        
        return $cad;
    }
    
    
    
    
} // class
