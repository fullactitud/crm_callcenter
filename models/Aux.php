<?php
namespace app\models;
use Yii;

/**
 * Clase temporal para permitir las consultas a DB
 */
class Aux extends \yii\db\ActiveRecord{

    public $acumulado, $admin, $activo, $agente, $apellidos;
    
    public $back, $barrida, $bootstrap, $borrar;
    
    public $cant, $codigo, $clave, $created, $cargados, $cantidad, $columna_dominio, $cod, $count, $conteo, $columna, $cuota, $contactados, $color, $cuenta, $columna_desplegar, $columna_back, $columna_siguiente, $columna_fecha_ref, $columna_movil, $columna_nombre, $columna_agente, $columna_cod, $cuotas, $campo, $cod_proyecto, $cod_instrumento, $chk, $col_dominio, $col_telf, $col_fecha_ref, $col_nombre;
    
    public $c001, $c002, $c003, $c004, $c005, $c006, $c007, $c008, $c009, $c010, $c011, $c012, $c013, $c014, $c015, $c016, $c017, $c018, $c019, $c020, $c021, $c022, $c023, $c024, $c025, $c026, $c027, $c028, $c029, $c030, $c031, $c032, $c033, $c034, $c035, $c036, $c037, $c038, $c039, $c040, $c041, $c042, $c043, $c044, $c045, $c046, $c047, $c048, $c049, $c050, $c051, $c052, $c053, $c054, $c055, $c056, $c057, $c058, $c059, $c060, $c061, $c062, $c063, $c064, $c065, $c066, $c067, $c068, $c069, $c070, $c071, $c072, $c073, $c074, $c075, $c076, $c077, $c078, $c079, $c080, $c081, $c082, $c083, $c084, $c085, $c086, $c087, $c088, $c089, $c090, $c091, $c092, $c093, $c094, $c095, $c096, $c097, $c098, $c099;

    
    public $desplegar, $dominio, $disponible, $description, $dominios, $desde, $data_tp, $d, $de_instrumento, $duracion, $dat, $de, $documento;
    
    public $d01, $d02, $d03, $d04, $d05, $d06, $d07, $d08, $d09, $d10, $d11, $d12, $d13, $d14, $d15, $d16, $d17, $d18, $d19, $d20, $d21, $d22, $d23, $d24, $d25, $d26, $d27, $d28, $d29, $d30, $d31, $d32, $d33, $d34, $d35, $d36, $d37, $d38, $d39, $d40, $d41;

    public $ente, $estatus, $efectiva, $efectivas, $encuesta, $efectivas1, $efectivas2, $entrada_inicial, $editar, $emisor;
    public $email;
    
    public $fecha_atencion, $fecha_ref, $fecha_tp, $fecha_encuesta, $fecha, $fin, $fechas_iguales, $filtrar_proyecto, $filtrar_desde, $filtrar_hasta, $filtrar_tlo, $filtrar_instrumento, $filtrar_fecha_tp, $filtrar_dominio, $filtrar_agente, $filtrar_data_tp, $font, $foto;
   

    public $hasta, $hora;

    public $inicio, $id_instrumento, $id_dominio, $id_pregunta_tp, $ir_a, $id_cuota, $instrumento, $idinstrumento, $item_name, $id_prospecto, $id_font, $id_bootstrap, $id_jqueryui, $id_instrumento_tp, $imagen, $id_admin, $id_reporte, $i, $id_estatus, $id, $id_pregunta, $id_proyecto, $id_opcion, $id_perfil, $id_tp, $id_tipificacion, $id_tipificacion_pregunta, $id_usuario, $id_archivo, $id_agenda, $id_cliente, $id_couta, $id_data, $id_entrada, $id_encuesta, $id_llamada, $id_modulo, $id_entrada_padre;
    
    public $js, $j, $jqueryui;
    
    public $llamadas1, $llamadas2, $llamar_luego, $llamadas, $llamar, $l, $lastlogintime;

    public $mostrar, $m;
    
    public $name, $nombre, $nextcal, $no_disponibles, $num, $nombres;

    public $obs, $orden;
    
    public $pregunta, $pais, $planificado, $password, $proyecto, $paso;

    public $query;
    
    public $reg_ini, $reg_fin, $requiere, $rol, $reporte, $receptor, $reg, $repassword;
  
    public $schema_name, $siguiente, $s, $st, $src;
    
    public $tiempo_invertido1, $tiempo_invertido2, $tiempo_invertido3, $tiempo_invertido4, $tiempo_invertido5, $tiempo_invertido, $tiempo_efectivo, $tp, $tiempo, $tlo, $tabla, $table, $tipo;

    public $user_id, $username, $up, $url, $usuario;

    public $valor, $veces, $v;

    /**
     * Retorna el nombre de la tabla
     */
    public static function tableName(){
        return 'a.aux';
    }
    
}