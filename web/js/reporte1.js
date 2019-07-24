function upTabla( tabla, num ){
    var cad, aux1, aux2;
    switch( tabla ){
    case '1':
	aux1 = auxCuotaK;
	aux2 = auxCuotaV;
	break;
    case '2':
	aux1 = auxInstrumentoK;
	aux2 = auxInstrumentoV;	
	break;
    case '3':
	aux1 = auxLlamadaK;
	aux2 = auxLlamadaV;	
	break;
    case '4':
	aux1 = auxProspectoK;
	aux2 = auxProspectoV;
	break;
    case '5':
	aux1 = auxTipificacionK;
	aux2 = auxTipificacionV;
	break;
    case '0':
    default:
	aux1 = aux2 = [];
	break;	
    }
    cad = '<option value="0"> => Seleccione <= </option>';
    for( i=0; i < aux1.length ;i++ )
	if( aux1[i] != undefined && aux2[i] != undefined )
	cad += '<option value="' +aux1[i] +'"> &nbsp; ' +aux2[i] +' </option>';
    $( '#xcampo_'+num ).html( cad );
}


function addCampoRpt( nCampoRpt ){    
    if( nCampoRpt == 'undefined' )
            nCampoRpt = 0;
    nCampoRpt++;
    var cad;
    cad = '<div class="form-group" style="clear:both; height: 2.0em;">';
    cad += '<input type="hidden" name="id_' +nCampoRpt +'" id="id_' +nCampoRpt +'" value="0" />';
    cad += '<div class="col-sm-3">Nombre: <br /><input type="text" name="de_' +nCampoRpt +'" id="de_' +nCampoRpt +'" value="" style="width:200px;"/></div>';
    cad += '<div class="col-sm-3"> Tabla:<br />';
    cad += '<select name="tabla_' +nCampoRpt +'" id="tabla_' +nCampoRpt +'" class="form-control" style="width:200px;" onChange="upTabla( $(this).val(),\'' +nCampoRpt +'\' );">';
    for( i = 0 ; i < vctT.length ; i++ )
        cad += '<option value="' +i +'"> &nbsp; ' +vctT[i] +'</option>';
    cad +='</select></div>';
    cad += '<div class="col-sm-3"> Campo: <br />';
    cad += '<select name="xcampo_' +nCampoRpt +'" id="xcampo_' +nCampoRpt +'" class="form-control" style="width:200px;"></select>';
    cad += '</div>';    
    cad += '<div class="col-sm-1">';
    cad += ' Activo:<br /> ';        
    cad += ' <input type="checkbox" name="st_' +nCampoRpt +'" id="st_' +nCampoRpt +'" value="1" checked="checked"/> Sí';
    cad += '</div>';
    cad += '<div class="col-sm-2">';
    cad += ' Salvar<br />';
    cad += '<input type="checkbox" id="delCol_' +nCampoRpt +'" name="delCol[]" value="1"/> No';
    cad += '</div>';
    cad += '</div>';
    $( '#div_campo_reporte' ).append( cad );
    return nCampoRpt;
}

var nCampoRpt = 0;
var vctT = [];
var vctC = [];
var auxCuotaK = [];
var auxCuotaV = [];
var auxInstrumentoK = [];
var auxLlamadaK = [];
var auxProspectoK = [];
var auxTipificacionK = [];
var auxInstrumentoV = [];
var auxLlamadaV = [];
var auxProspectoV = [];
var auxTipificacionV = [];
vctT[0] = ' => Seleccione <= ';
vctT[1] = 'Cuota';
vctT[2] = 'Instrumento';
vctT[3] = 'Llamada';
vctT[4] = 'Prospecto';
vctT[5] = 'Tipificacion';


// CUOTA       // id 	id_instrumento 	id_dominio id_data 	id_usuario    
auxCuotaK[0] = 'fecha_encuesta';
auxCuotaK[1] = 'fecha_ref';
auxCuotaK[2] = 'cuota';
auxCuotaK[3] = 'reg';
auxCuotaK[4] = 'conteo';
auxCuotaK[5] = 'cod_dominio';

auxCuotaV[0] = 'Fecha de la encuesta';
auxCuotaV[1] = 'Fecha de referencia';
auxCuotaV[2] = 'Cuota';
auxCuotaV[3] = 'Fecha de registro';
auxCuotaV[4] = 'Conteo';
auxCuotaV[5] = 'Codigo del dominio';


// PROSPECTO
auxProspectoK[1] = 'id';
auxProspectoK[2] = 'id_instrumento';
auxProspectoK[3] = 'id_data';
auxProspectoK[4] = 'barrida';
auxProspectoK[5] = 'llamar';
auxProspectoK[6] = 'reg';
auxProspectoK[7] = 'up';
auxProspectoK[8] = 'st';
auxProspectoK[9] = 'tlo';
auxProspectoK[10] = 'inicio';
auxProspectoK[11] = 'fin';
auxProspectoK[12] = 'id_tipificacion';
auxProspectoK[13] = 'encuesta';

auxProspectoV[1] = 'Número ID';
auxProspectoV[2] = 'Número ID del instrumento';
auxProspectoV[3] = 'id_data';
auxProspectoV[4] = 'Barrida';
auxProspectoV[5] = 'llamar';
auxProspectoV[6] = 'Fecha de registro';
auxProspectoV[7] = 'Fecha de última actualización';
auxProspectoV[8] = 'Estatus';
auxProspectoV[9] = 'ID del Teleoperador';
auxProspectoV[10] = 'Fecha de inicio';
auxProspectoV[11] = 'Fecha de fin';
auxProspectoV[12] = 'ID de tipificacion';
auxProspectoV[13] = 'Número de encuesta';


auxProspectoK[101] = 'c001'; auxProspectoK[102] = 'c002'; auxProspectoK[103] = 'c003';
auxProspectoK[104] = 'c004'; auxProspectoK[105] = 'c005'; auxProspectoK[106] = 'c006';
auxProspectoK[107] = 'c007'; auxProspectoK[108] = 'c008'; auxProspectoK[109] = 'c009';
auxProspectoK[110] = 'c010'; auxProspectoK[111] = 'c011'; auxProspectoK[112] = 'c012';
auxProspectoK[113] = 'c013'; auxProspectoK[114] = 'c014'; auxProspectoK[115] = 'c015';
auxProspectoK[116] = 'c016'; auxProspectoK[117] = 'c017'; auxProspectoK[118] = 'c018';
auxProspectoK[119] = 'c019'; auxProspectoK[120] = 'c020'; auxProspectoK[121] = 'c021';
auxProspectoK[122] = 'c022'; auxProspectoK[123] = 'c023'; auxProspectoK[124] = 'c024';
auxProspectoK[125] = 'c025'; auxProspectoK[126] = 'c026'; auxProspectoK[127] = 'c027';
auxProspectoK[128] = 'c028'; auxProspectoK[129] = 'c029'; auxProspectoK[130] = 'c030';
auxProspectoK[131] = 'c031'; auxProspectoK[132] = 'c032'; auxProspectoK[133] = 'c033';
auxProspectoK[134] = 'c034'; auxProspectoK[135] = 'c035'; auxProspectoK[136] = 'c036';
auxProspectoK[137] = 'c037'; auxProspectoK[138] = 'c038'; auxProspectoK[139] = 'c039';
auxProspectoK[140] = 'c040'; auxProspectoK[141] = 'c041'; auxProspectoK[142] = 'c042';
auxProspectoK[143] = 'c043'; auxProspectoK[144] = 'c044'; auxProspectoK[145] = 'c045';
auxProspectoK[146] = 'c046'; auxProspectoK[147] = 'c047'; auxProspectoK[148] = 'c048';
auxProspectoK[149] = 'c049'; auxProspectoK[150] = 'c050'; auxProspectoK[151] = 'c051';
auxProspectoK[152] = 'c052'; auxProspectoK[153] = 'c053'; auxProspectoK[154] = 'c054';
auxProspectoK[155] = 'c055'; auxProspectoK[156] = 'c056'; auxProspectoK[157] = 'c057';
auxProspectoK[158] = 'c058'; auxProspectoK[159] = 'c059'; auxProspectoK[160] = 'c060';
auxProspectoK[161] = 'c061'; auxProspectoK[162] = 'c062'; auxProspectoK[163] = 'c063';
auxProspectoK[164] = 'c064'; auxProspectoK[165] = 'c065'; auxProspectoK[166] = 'c066';
auxProspectoK[167] = 'c067'; auxProspectoK[168] = 'c068'; auxProspectoK[169] = 'c069';
auxProspectoK[170] = 'c070'; auxProspectoK[171] = 'c071'; auxProspectoK[172] = 'c072';
auxProspectoK[173] = 'c073'; auxProspectoK[174] = 'c074'; auxProspectoK[175] = 'c075';
auxProspectoK[176] = 'c076'; auxProspectoK[177] = 'c077'; auxProspectoK[178] = 'c078';
auxProspectoK[179] = 'c079'; auxProspectoK[180] = 'c080'; auxProspectoK[181] = 'c081';
auxProspectoK[182] = 'c082'; auxProspectoK[183] = 'c083'; auxProspectoK[184] = 'c084';
auxProspectoK[185] = 'c085'; auxProspectoK[186] = 'c086'; auxProspectoK[187] = 'c087';
auxProspectoK[188] = 'c088'; auxProspectoK[189] = 'c089'; auxProspectoK[190] = 'c090';
auxProspectoK[191] = 'c091'; auxProspectoK[192] = 'c092'; auxProspectoK[193] = 'c093';
auxProspectoK[194] = 'c094'; auxProspectoK[195] = 'c095'; auxProspectoK[196] = 'c096';
auxProspectoK[197] = 'c097'; auxProspectoK[198] = 'c098'; auxProspectoK[199] = 'c099';

auxProspectoV[101] = 'Columna 1'; auxProspectoV[102] = 'Columna 2'; auxProspectoV[103] = 'Columna 3';
auxProspectoV[104] = 'Columna 4'; auxProspectoV[105] = 'Columna 5'; auxProspectoV[106] = 'Columna 6';
auxProspectoV[107] = 'Columna 7'; auxProspectoV[108] = 'Columna 8'; auxProspectoV[109] = 'Columna 9';
auxProspectoV[110] = 'Columna 10'; auxProspectoV[111] = 'Columna 11'; auxProspectoV[112] = 'Columna 12';
auxProspectoV[113] = 'Columna 13'; auxProspectoV[114] = 'Columna 14'; auxProspectoV[115] = 'Columna 15';
auxProspectoV[116] = 'Columna 16'; auxProspectoV[117] = 'Columna 17'; auxProspectoV[118] = 'Columna 18';
auxProspectoV[119] = 'Columna 19'; auxProspectoV[120] = 'Columna 20'; auxProspectoV[121] = 'Columna 21';
auxProspectoV[122] = 'Columna 22'; auxProspectoV[123] = 'Columna 23'; auxProspectoV[124] = 'Columna 24';
auxProspectoV[125] = 'Columna 25'; auxProspectoV[126] = 'Columna 26'; auxProspectoV[127] = 'Columna 27';
auxProspectoV[128] = 'Columna 28'; auxProspectoV[129] = 'Columna 29'; auxProspectoV[130] = 'Columna 30';
auxProspectoV[131] = 'Columna 31'; auxProspectoV[132] = 'Columna 32'; auxProspectoV[133] = 'Columna 33';
auxProspectoV[134] = 'Columna 34'; auxProspectoV[135] = 'Columna 35'; auxProspectoV[136] = 'Columna 36';
auxProspectoV[137] = 'Columna 37'; auxProspectoV[138] = 'Columna 38'; auxProspectoV[139] = 'Columna 39';
auxProspectoV[140] = 'Columna 40'; auxProspectoV[141] = 'Columna 41'; auxProspectoV[142] = 'Columna 42';
auxProspectoV[143] = 'Columna 43'; auxProspectoV[144] = 'Columna 44'; auxProspectoV[145] = 'Columna 45';
auxProspectoV[146] = 'Columna 46'; auxProspectoV[147] = 'Columna 47'; auxProspectoV[148] = 'Columna 48';
auxProspectoV[149] = 'Columna 49'; auxProspectoV[150] = 'Columna 50'; auxProspectoV[151] = 'Columna 51';
auxProspectoV[152] = 'Columna 52'; auxProspectoV[153] = 'Columna 53'; auxProspectoV[154] = 'Columna 54';
auxProspectoV[155] = 'Columna 55'; auxProspectoV[156] = 'Columna 56'; auxProspectoV[157] = 'Columna 57';
auxProspectoV[158] = 'Columna 58'; auxProspectoV[159] = 'Columna 59'; auxProspectoV[160] = 'Columna 60';
auxProspectoV[161] = 'Columna 61'; auxProspectoV[162] = 'Columna 62'; auxProspectoV[163] = 'Columna 63';
auxProspectoV[164] = 'Columna 64'; auxProspectoV[165] = 'Columna 65'; auxProspectoV[166] = 'Columna 66';
auxProspectoV[167] = 'Columna 67'; auxProspectoV[168] = 'Columna 68'; auxProspectoV[169] = 'Columna 69';
auxProspectoV[170] = 'Columna 70'; auxProspectoV[171] = 'Columna 71'; auxProspectoV[172] = 'Columna 72';
auxProspectoV[173] = 'Columna 73'; auxProspectoV[174] = 'Columna 74'; auxProspectoV[175] = 'Columna 75';
auxProspectoV[176] = 'Columna 76'; auxProspectoV[177] = 'Columna 77'; auxProspectoV[178] = 'Columna 78';
auxProspectoV[179] = 'Columna 79'; auxProspectoV[180] = 'Columna 80'; auxProspectoV[181] = 'Columna 81';
auxProspectoV[182] = 'Columna 82'; auxProspectoV[183] = 'Columna 83'; auxProspectoV[184] = 'Columna 84';
auxProspectoV[185] = 'Columna 85'; auxProspectoV[186] = 'Columna 86'; auxProspectoV[187] = 'Columna 87';
auxProspectoV[188] = 'Columna 88'; auxProspectoV[189] = 'Columna 89'; auxProspectoV[190] = 'Columna 90';
auxProspectoV[191] = 'Columna 91'; auxProspectoV[192] = 'Columna 92'; auxProspectoV[193] = 'Columna 93';
auxProspectoV[194] = 'Columna 94'; auxProspectoV[195] = 'Columna 95'; auxProspectoV[196] = 'Columna 96';
auxProspectoV[197] = 'Columna 97'; auxProspectoV[198] = 'Columna 98'; auxProspectoV[199] = 'Columna 99';


// LLAMADA  / id 	id_usuario 	id_prospecto 
auxLlamadaK[0] = 'id';
auxLlamadaK[1] = 'id_tipificacion';
auxLlamadaK[2] = 'barrida';
auxLlamadaK[3] = 'observacion';
auxLlamadaK[4] = 'reg';
auxLlamadaK[5] = 'fin';
auxLlamadaK[6] = 'st';

auxLlamadaV[0] = 'Número ID de llamada'; 	 	 	
auxLlamadaV[1] = 'Número ID de Tipificacion';
auxLlamadaV[2] = 'Barrida';
auxLlamadaV[3] = 'Observaciones';
auxLlamadaV[4] = 'Fecha de registro';
auxLlamadaV[5] = 'Fecah de finalización de llamada';
auxLlamadaV[6] = 'Estatus';


// INSTRUMENTO
auxInstrumentoK[0] = 'barrida';
auxInstrumentoK[1] = 'codigo';
auxInstrumentoK[2] = 'de';
auxInstrumentoK[3] = 'st';
auxInstrumentoK[4] = 'id_admin';
auxInstrumentoK[5] = 'id';

auxInstrumentoV[0] = 'Barrida';
auxInstrumentoV[1] = 'Codigo';
auxInstrumentoV[2] = 'Descripción';
auxInstrumentoV[3] = 'Estatus';
auxInstrumentoV[4] = 'ID Administrador';
auxInstrumentoV[5] = 'ID Instrumento';

// TIPIFICACION
auxTipificacionK[0] = 'id';
auxTipificacionK[1] = 'de';
auxTipificacionV[0] = 'Número ID';
auxTipificacionV[1] = 'Descripción';

