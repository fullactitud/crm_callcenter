/* CREAR INSTRUMENTO */
var entradas = '';
var opciones = '';
var edit1 = 0;
var edit2 = 0;
var x3aux = '';
var x3_entradas_orden = '';
var x3help = 0;

var x3help1 = x3help2 = x3help3 = x3help4 = x3help5 = x3help6 = x3help7 = x3help8 = x3help9 = x3help10 = x3help11 = x3help12 = x3help13 = x3help14 = x3help15 = x3help16 = x3help17 = x3help18 = x3help19 = x3help20 = x3help21 = x3help22 = x3help23 = x3help24 = x3help25 = x3help26 = x3help27 = x3help28 = x3help29 = x3help30 = x3help31 = x3help32 = x3help33 = x3help34 = x3help35 = x3help36 = x3help37 = x3help38 = x3help39 = x3help40 = x3help41 = x3help42 = x3help43 = x3help44 = x3help45 = x3help46 = x3help47 = x3help48 = x3help49 = x3help50 = x3help51 = x3help52 = x3help53 = x3help54 = x3help55 = x3help56 = x3help57 = x3help58 = x3help59 = x3help60 = x3help61 = x3help62 = x3help63 = x3help64 = x3help65 = x3help66 = x3help67 = x3help68 = x3help69 = x3help70 = x3help71 = x3help72 = x3help73 = x3help74 = x3help75 = x3help76 = x3help77 = x3help78 = x3help79 = x3help80 = x3help81 = x3help82 = x3help83 = x3help84 = x3help85 = x3help86 = x3help87 = x3help88 = x3help89 = x3help90 = x3help91 = x3help92 = x3help93 = x3help94 = x3help95 = x3help96 = x3help97 = x3help98 = x3help99 = x3help100 = x3help101 = x3help102 = x3help103 = x3help104 = x3help105 = x3help106 = x3help107 = x3help108 = x3help109 = x3help110 = x3help111 = x3help112 = x3help113 = x3help114 = x3help115 = x3help116 = x3help117 = x3help118 = x3help119 = x3help120 = x3help121 = x3help122 = x3help123 = x3help124 = x3help125 = x3help126 = x3help127 = x3help128 = x3help129 = x3help130 = x3help131 = x3help132 = x3help133 = x3help134 = x3help135 = x3help136 = x3help137 = x3help138 = x3help139 = x3help140 = x3help141 = x3help142 = x3help143 = x3help144 = x3help145 = x3help146 = x3help147 = x3help148 = x3help149 = x3help150 = x3help151 = x3help152 = x3help153 = x3help154 = x3help155 = x3help156 = x3help157 = x3help158 = x3help159 = x3help160 = x3help161 = x3help162 = x3help163 = x3help164 = x3help165 = x3help166 = x3help167 = x3help168 = x3help169 = x3help170 = x3help171 = x3help172 = x3help173 = x3help174 = x3help175 = x3help176 = x3help177 = x3help178 = x3help179 = x3help180 = x3help181 = x3help182 = x3help183 = x3help184 = x3help185 = x3help186 = x3help187 = x3help188 = x3help189 = x3help190 = x3help191 = x3help192 = x3help193 = x3help194 = x3help195 = x3help196 = x3help197 = x3help198 = x3help199 = x3help200 = 0;
var x3sortedIDs;
var x3vctenc1 = new Array();
var x3vctenc2 = new Array();
var x3op0sig = '';
var x3op0req = new Array();
var jid2 = new Array();
for( d=1 ; d<=200 ; d++)
    jid2[d] = 0;

/**
 * Agrega una entrada al instrumento
 */
function entrada( idiv ){
    $(':input[type="submit"]').prop('disabled', true);
    if( jid >= 200 )
        return jid;
    jid++;
    x3aux = entradas.replace(/__id__/g, jid);
    $( '#'+idiv ).append( x3aux );
    /* x3vctenc1[jid] = 'ir_a_' +jid;  */
    // x3listselect();
	x3AppendSelect();
    return jid;
};


/**
 * Agrega las opciones en el select
 <div id="div_requiere___id__" class="col-sm-9">
 requieres___iden_id__
 </div>

 
<input type="checkbox" id="requiere_{{ID}}_' .$entrada3->codigo .'" name="requiere_{{ID}}[]" value="' .$entrada3->codigo .'::{{ST' .$entrada3->codigo .'}}" {{CHCK' .$entrada3->codigo .'}} title="' .$entrada3->codigo .'"/> Entrada <b>' .$entrada3->codigo .'</b>

 */
function x3AppendSelect(){
    var aux, i, i2, auxSig, auxReq = '';
	
	if( jid <= 1 ){
		auxSig = '<option value=""> Al seleccionar ir a </option>';
	}
	if( jid < 10 ) i2 = '0' +jid;
	else i2 = jid;
	auxSig += '<option value="' +jid +'"> &nbsp; Entrada ' +jid +'</option>';
	auxReq += ' &nbsp;&nbsp; <input type="checkbox" id="requiere_' +jid +'_' +jid +'" name="requiere_' +jid +'[]" value="' +jid +'::0"/> Entrada ' + i2 +'';

	$('#ir_a_' +jid).append( $('#ir_a_' +(jid - 1) ).html() );
	$('#div_requiere_' +jid).append( $('#div_requiere_' +(jid - 1) ).html() );
	
    for( i=1 ;  i<=jid ; i++ ){
		$('#ir_a_' +i).append(auxSig);
		$('#div_requiere_' +i).append(auxReq);
		$('#antes_requiere_' +i).append(auxReq);	
    }
	aux = $('#div_requiere_' +jid).html();
	aux = aux.replace(/requieres_/g, '');
	$('#div_requiere_' +jid).html(aux);	
};


/**
 * Agrega las opciones en el select
 */
function x3listselect( id ){
    var i,j,i2,j2;
    var x3aux = new Array();
    x3op0sig = '<option value=""> Al seleccionar ir a </option>';
    x3op0req = '';
    for( k=1; k <= 200 ;k++ ){
        x3aux[k] = false;
    }
    x3aux[10] = x3aux[20] = x3aux[30] = x3aux[40] = x3aux[50] = x3aux[60] = x3aux[70] = x3aux[80] = x3aux[90] = x3aux[100] = x3aux[110] = x3aux[120] = x3aux[130] = x3aux[140] = x3aux[150] = x3aux[160] = x3aux[170] = x3aux[180] = x3aux[190] = x3aux[200] = true;
    for( i=1 ;  i<=jid ; i++ ){
        if( i < 10 )
            i2 = '0' +i;
        else
            i2 = i;
        x3op0sig += '<option value="' +i +'"> &nbsp; Entrada ' +i +'</option>';
        x3op0req += ' &nbsp;&nbsp; <input type="checkbox" id="x3op0req_' +id +'_' +i +'" name="x3op0req_' +id +'_' +i +'" value="' +i +'"> Entrada ' +i2 +'';
        if( x3aux[i] == true )
            x3op0req += '<br />';
    }
    for( i=1 ;  i<=jid ; i++ ){
        $('#ir_a_' +i).html(x3op0sig);
        $('#div_requiere_' +i).html(x3op0req);
        $('#antes_requiere_' +i).html(x3op0req);
        for( j=1 ;  j<=jid2.length ; j++ )
            $('#ir_a_' +i +'_' +j).html(x3op0sig);
    }
};



/**
 * Agrega una opción
 */
function agregaropcion( idiv, id ){
    if( jid2[id] == 500 )
        return;
    if( jid2[id] == 'undefined' )
        jid2[id] = 0;
    jid2[id]++;
    x3aux = opciones.replace(/__id__/g, id); 
    x3aux = x3aux.replace(/__id2__/g, jid2[id]);
    $( '#'+idiv ).append( x3aux );
    x3listselect(id);
};


var nCabecera;
/**
 * Agrega una columna
 */
function addcolumna( nCabecera ){
    if( nCabecera == 'undefined' )
            nCabecera = 0;
    nCabecera++;  
    
    var cad = '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">';
    
    cad += '<div class="col-sm-1"> Data:</div>';
    cad += '<input type="hidden" id="dato_id_' +nCabecera +'" name="dato_id_' +nCabecera +'" value="0"/>';    
    cad += '<div class="col-sm-2"><input type="text" id="dato_' +nCabecera +'" name="dato_' +nCabecera +'" placeholder="Dato" class="form-control" style="width:100%;" value=""/></div>';
    cad += '<div class="col-sm-1"> Columna:</div>';
    cad += '<div class="col-sm-2"><select id="columna_' +nCabecera +'" name="columna_' +nCabecera +'" placeholder="Seleccione" class="form-control" style="width:100%;">';

    cad += '<option value="c001"> &nbsp; Columna 1</option>';
    cad += '<option value="c002"> &nbsp; Columna 2</option>';
    cad += '<option value="c003"> &nbsp; Columna 3</option>';
    cad += '<option value="c004"> &nbsp; Columna 4</option>';
    cad += '<option value="c005"> &nbsp; Columna 5</option>';
    cad += '<option value="c006"> &nbsp; Columna 6</option>';
    cad += '<option value="c007"> &nbsp; Columna 7</option>';
    cad += '<option value="c008"> &nbsp; Columna 8</option>';
    cad += '<option value="c009"> &nbsp; Columna 9</option>';
    cad += '<option value="c000"> &nbsp; Columna 10</option>';
    cad += '<option value="c011"> &nbsp; Columna 11</option>';
    cad += '<option value="c012"> &nbsp; Columna 12</option>';
    cad += '<option value="c013"> &nbsp; Columna 13</option>';
    cad += '<option value="c014"> &nbsp; Columna 14</option>';
    cad += '<option value="c015"> &nbsp; Columna 15</option>';
    cad += '<option value="c016"> &nbsp; Columna 16</option>';
    cad += '<option value="c017"> &nbsp; Columna 17</option>';
    cad += '<option value="c018"> &nbsp; Columna 18</option>';
    cad += '<option value="c019"> &nbsp; Columna 19</option>';
    cad += '<option value="c020"> &nbsp; Columna 20</option>';
    cad += '<option value="c021"> &nbsp; Columna 21</option>';
    cad += '<option value="c022"> &nbsp; Columna 22</option>';
    cad += '<option value="c023"> &nbsp; Columna 23</option>';
    cad += '<option value="c024"> &nbsp; Columna 24</option>';
    cad += '<option value="c025"> &nbsp; Columna 25</option>';
    cad += '<option value="c026"> &nbsp; Columna 26</option>';
    cad += '<option value="c027"> &nbsp; Columna 27</option>';
    cad += '<option value="c028"> &nbsp; Columna 28</option>';
    cad += '<option value="c029"> &nbsp; Columna 29</option>';
    cad += '<option value="c030"> &nbsp; Columna 30</option>';
    cad += '<option value="c031"> &nbsp; Columna 31</option>';
    cad += '<option value="c032"> &nbsp; Columna 32</option>';
    cad += '<option value="c033"> &nbsp; Columna 33</option>';
    cad += '<option value="c034"> &nbsp; Columna 34</option>';
    cad += '<option value="c035"> &nbsp; Columna 35</option>';
    cad += '<option value="c036"> &nbsp; Columna 36</option>';
    cad += '<option value="c037"> &nbsp; Columna 37</option>';
    cad += '<option value="c038"> &nbsp; Columna 38</option>';
    cad += '<option value="c039"> &nbsp; Columna 39</option>';
    cad += '<option value="c040"> &nbsp; Columna 40</option>';
    cad += '<option value="c041"> &nbsp; Columna 41</option>';
    cad += '<option value="c042"> &nbsp; Columna 42</option>';
    cad += '<option value="c043"> &nbsp; Columna 43</option>';
    cad += '<option value="c044"> &nbsp; Columna 44</option>';
    cad += '<option value="c045"> &nbsp; Columna 45</option>';
    cad += '<option value="c046"> &nbsp; Columna 46</option>';
    cad += '<option value="c047"> &nbsp; Columna 47</option>';
    cad += '<option value="c048"> &nbsp; Columna 48</option>';
    cad += '<option value="c049"> &nbsp; Columna 49</option>';
    cad += '<option value="c050"> &nbsp; Columna 50</option>';
    cad += '<option value="c051"> &nbsp; Columna 51</option>';
    cad += '<option value="c052"> &nbsp; Columna 52</option>';
    cad += '<option value="c053"> &nbsp; Columna 53</option>';
    cad += '<option value="c054"> &nbsp; Columna 54</option>';
    cad += '<option value="c055"> &nbsp; Columna 55</option>';
    cad += '<option value="c056"> &nbsp; Columna 56</option>';
    cad += '<option value="c057"> &nbsp; Columna 57</option>';
    cad += '<option value="c058"> &nbsp; Columna 58</option>';
    cad += '<option value="c059"> &nbsp; Columna 59</option>';
    cad += '<option value="c060"> &nbsp; Columna 60</option>';

    cad += '</select>';
    cad += ' </div>';
    cad += '<div class="col-sm-2">';
    cad += '<input type="checkbox" id="dato_mostrar_' +nCabecera +'" name="dato_mostrar_' +nCabecera +'" value="1"/> &nbsp; Mostrar';
    cad += '</div>';
    cad += '<div class="col-sm-2">';
    cad += '<input type="checkbox" id="dato_editar_' +nCabecera +'" name="dato_editar_' +nCabecera +'" value="1"/> &nbsp; Editar';
    cad += '</div>';
    cad += '<div class="col-sm-2">';
    cad += '<input type="checkbox" id="delCol_' +nCabecera +'" name="delCol_' +nCabecera +'" value="1"/> &nbsp; Eliminar';
    cad += '</div>';                                 
    cad += '</div>';


        $( '#div_carga_data' ).append( cad );
    return nCabecera;
}

/**
 * Oculta y muestra elementos
 */
function x3fun1(id){    
    op = $('#tipo' +id).val();
    switch( op ){
    case '1':
        $('#x3div_id' +id).hide();
        $('#x3div_opciones_' +id).hide();
        break;
    case '2':
        $('#x3div_id' +id).show();
        $('#x3div_opciones_' +id).hide();
        break;
    case '3':
    case '4':
    case '5':
        $('#x3div_id' +id).show();
        $('#x3div_opciones_' +id).show();
        break;
    }
};

/**
 * Acciones que se ejecutan al submit el instrumento
 */
function x3funSubmit(id){
    /* Habilitar todo el formulario */
    $( '#sortable' ).sortable();
    $( '#sortable2' ).sortable();
    $( '#sortable' ).enableSelection();
    $( '#sortable2' ).enableSelection();
    $( '#sortable' ).sortable( "option", "disabled", true );      
    $( '#sortable2' ).sortable( "option", "disabled", true );
    $( ".lisort" ).attr("contentEditable","true");      
    $( ".lisort2" ).attr("contentEditable","true");
    /* Asignar valores */
    $('#jid').val(jid);
    var i;
    for(i=1; i<=jid ;i++){
        $('#ops' +i).val(jid2[i]);
    }
    var aux = $( '#sortable' ).sortable( 'toArray' );
    $('#x3sortedIDs').val(aux);
    /* Habilitar formulario */
    $(':input[type="submit"]').prop('disabled', false);
    /* Submit formulario */
    $('#' +id ).submit();
};

/**
 * Habilita y desabilita la opción de ordenar
 */
function x3sortable(){
    if( edit1 == 0 ){
        /* $( '#sortable' ).addClass('sortable'); */
      $( '#sortable' ).sortable();
      $( '#sortable' ).disableSelection();
      $( '#sortable' ).sortable( "option", "disabled", false );
      $( "lisort1" ).attr("contentEditable","false");
      $( 'lisort1' ).css('cursor','move');
      var i;
      for( i = 1 ; i <= jid ; i++ ){
          $('#lisort1s'+i).hide();
      }
      return 1;
    }else{
      $( "#sortable" ).sortable("disable");
      $( '#sortable' ).enableSelection();
      $( '#sortable' ).sortable( "option", "disabled", true );
      $( ".lisort1" ).attr("contentEditable","true");
      $( ".lisort1" ).css("cursor","default");
      /* $('#sortable').removeClass('sortable').removeClass('ui-sortable'); */
      for( i = 1 ; i <= jid ; i++ ){
          $('#lisort1s'+i).show();
      }
      return 0;
    }
};

/**
 * Habilita y desabilita la opción de ordenar
 */
function x3sortable2(){
    if( edit2 == 0 ){
        /* $( '#sortable' ).addClass('sortable'); */
		$( '#sortable2' ).sortable();
		$( '#sortable2' ).disableSelection();
		$( '#sortable2' ).sortable( "option", "disabled", false );
		$( "lisort2" ).attr("contentEditable","false");
		$( 'lisort2' ).css('cursor','move');
		return 1;
    }else{
		$( "#sortable2" ).sortable("disable");
		$( '#sortable2' ).enableSelection();
		$( '#sortable2' ).sortable( "option", "disabled", true );
		$( ".lisort2" ).attr("contentEditable","true");
		$( ".lisort2" ).css("cursor","default");
		
		/* $('#sortable').removeClass('sortable').removeClass('ui-sortable'); */
		return 0;
    }
};
