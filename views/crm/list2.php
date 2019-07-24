<?=$menu;?>
<div class="titulos"><?=$titulo;?></div>

<div style="clear: both;">
<div style="float: right;" onClick="if( x3help ==0){$('#x3ayuda').show();x3help =1;}else{$('#x3ayuda').hide();x3help =0;}"><img src="img/icons/help.png" title="Mostrar Ayuda" style="height:2.5em; cursor:hand;cursor:pointer;"/></div>
<div id="x3ayuda" style="clear:both;display: none;"><?=$ayuda;?></div>
</div>


<style>
#contentLeft li:hover {
    cursor: pointer;
}

#contentLeft li.ui-sortable-helper{
    cursor: move;
}
</style>

<script type="text/javascript">      
      var jid = 0; var entradas = ''; var opciones = ''; var edit1 = 0; var edit2 = 0; var x3aux = ''; var x3_entradas_orden = '';
var x3help = 0;

var x3help1 = x3help2 = x3help3 = x3help4 = x3help5 = x3help6 = x3help7 = x3help8 = x3help9 = x3help10 = x3help11 = x3help12 = x3help13 = x3help14 = x3help15 = x3help16 = x3help17 = x3help18 = x3help19 = x3help20 = x3help21 = x3help22 = x3help23 = x3help24 = x3help25 = x3help26 = x3help27 = x3help28 = x3help29 = x3help30 = x3help31 = x3help32 = x3help33 = x3help34 = x3help35 = x3help36 = x3help37 = x3help38 = x3help39 = x3help40 = x3help41 = x3help42 = x3help43 = x3help44 = x3help45 = x3help46 = x3help47 = x3help48 = x3help49 = x3help50 = x3help51 = x3help52 = x3help53 = x3help54 = x3help55 = x3help56 = x3help57 = x3help58 = x3help59 = x3help60 = x3help61 = x3help62 = x3help63 = x3help64 = x3help65 = x3help66 = x3help67 = x3help68 = x3help69 = x3help70 = x3help71 = x3help72 = x3help73 = x3help74 = x3help75 = x3help76 = x3help77 = x3help78 = x3help79 = x3help80 = x3help81 = x3help82 = x3help83 = x3help84 = x3help85 = x3help86 = x3help87 = x3help88 = x3help89 = x3help90 = x3help91 = x3help92 = x3help93 = x3help94 = x3help95 = x3help96 = x3help97 = x3help98 = x3help99 = x3help100 = x3help101 = x3help102 = x3help103 = x3help104 = x3help105 = x3help106 = x3help107 = x3help108 = x3help109 = x3help110 = x3help111 = x3help112 = x3help113 = x3help114 = x3help115 = x3help116 = x3help117 = x3help118 = x3help119 = x3help120 = x3help121 = x3help122 = x3help123 = x3help124 = x3help125 = x3help126 = x3help127 = x3help128 = x3help129 = x3help130 = x3help131 = x3help132 = x3help133 = x3help134 = x3help135 = x3help136 = x3help137 = x3help138 = x3help139 = x3help140 = x3help141 = x3help142 = x3help143 = x3help144 = x3help145 = x3help146 = x3help147 = x3help148 = x3help149 = x3help150 = x3help151 = x3help152 = x3help153 = x3help154 = x3help155 = x3help156 = x3help157 = x3help158 = x3help159 = x3help160 = x3help161 = x3help162 = x3help163 = x3help164 = x3help165 = x3help166 = x3help167 = x3help168 = x3help169 = x3help170 = x3help171 = x3help172 = x3help173 = x3help174 = x3help175 = x3help176 = x3help177 = x3help178 = x3help179 = x3help180 = x3help181 = x3help182 = x3help183 = x3help184 = x3help185 = x3help186 = x3help187 = x3help188 = x3help189 = x3help190 = x3help191 = x3help192 = x3help193 = x3help194 = x3help195 = x3help196 = x3help197 = x3help198 = x3help199 = x3help200 = 0;         

var x3vctenc1 = new Array();
var x3vctenc2 = new Array();
var x3op0sig = '';
var x3op0req = new Array();

var jid2 = new Array();
for( d=1;d<=200;d++)
    jid2[d] = 0;

function entrada(idiv){
    $(':input[type="submit"]').prop('disabled', true);
    if( jid >= 200 )
        return jid;
    jid = jid + 1;
    x3aux = entradas.replace(/__id__/g, jid);
    $( '#'+idiv ).append( x3aux );
    // x3vctenc1[jid] = 'ir_a_' +jid;  
    x3listselect();
    return jid;
};


function x3listselect(){
    var i,j,i2,j2;
    var x3aux = new Array();
    x3op0sig = '<option value=""> Seleccione </option>';
    x3op0req = '';
    for( k=1; k <= 200 ;k++ ){
        x3aux[k] = false;
    }
    x3aux[10] = x3aux[20] = x3aux[30] = x3aux[40] = x3aux[50] = x3aux[60] = x3aux[70] = x3aux[80] = x3aux[90] = x3aux[100] = x3aux[110] = x3aux[120] = x3aux[130] = x3aux[140] = x3aux[150] = x3aux[160] = x3aux[170] = x3aux[180] = x3aux[190] = x3aux[200] = true;
    for( i=1 ;  i<=jid ; i++ ){
        if( i < 10 )
            i2 = '0' +i;
        else
            i2=i;
        x3op0sig += '<option value="' +i +'">Pregunta ' +i +'</option>';
        x3op0req += ' &nbsp;&nbsp; <input type="checkbox" id="x3op0req_' +i +'" value="' +i +'"> ' +i2 +'';
        if( x3aux[i] == true )
            x3op0req += '<br />';
    }
    for( i=1 ;  i<=jid ; i++ ){
        $('#ir_a_' +i).html(x3op0sig);
        $('#requiere_' +i).html(x3op0req);
        $('#antes_requiere_' +i).html(x3op0req);
        for( j=1 ;  j<=jid2.length ; j++ )
            $('#ir_a_' +i +'_' +j).html(x3op0sig);
    }
       
};





function agregaropcion(idiv,id){

 
    
    if( jid2[id] == 500 )
        return;
    if( jid2[id] == 'undefined' )
        jid2[id] = 0;
    jid2[id] = jid2[id] + 1;
    x3aux = opciones.replace(/__id__/g, id); 
    x3aux = x3aux.replace(/__id2__/g, jid2[id]);
    $( '#'+idiv ).append( x3aux );
    x3listselect();
      
   
};



function x3fun1(id){
    
    op = $('#tipo' +id).val();
    switch( op ){
    case '1':
        $('#x3div_id' +id).hide();
        $('#x3div_opciones' +id).hide();
        break;
    case '2':
        $('#x3div_id' +id).show();
        $('#x3div_opciones' +id).hide();
        break;
    case '3':
    case '4':
    case '5':
        $('#x3div_id' +id).show();
        $('#x3div_opciones' +id).show();
        break;
             
    }     
}; 


var x3sortedIDs;

 
function x3funSubmit(id){
    // Habilitar todo el formulario
    $( '#sortable' ).sortable();
    $( '#sortable2' ).sortable();
    $( '#sortable' ).enableSelection();
    $( '#sortable2' ).enableSelection();
    $( '#sortable' ).sortable( "option", "disabled", true );      
    $( '#sortable2' ).sortable( "option", "disabled", true );
    $( ".lisort" ).attr("contentEditable","true");      
    $( ".lisort2" ).attr("contentEditable","true");
    
    // Asignar valores
    $('#jid').val(jid);
    var i;
    for(i=1; i<=jid ;i++){
        $('#ops' +i).val(jid2[i]);
    }
    
    var aux = $( '#sortable' ).sortable( 'toArray' );
    $('#x3sortedIDs').val(aux);
    
    // Habilitar formulario
    $(':input[type="submit"]').prop('disabled', false);
    // Submit formulario
    $('#' +id ).submit();
};


      
<?=$cad;?>
function x3sortable(){
    if( edit1 == 0 ){

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
      for( i = 1 ; i <= jid ; i++ ){
          $('#lisort1s'+i).show();
      }
      return 0;
    }
};


  function x3sortable2(){
    if( edit2 == 0 ){

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


      return 0;
    }
};


</script>

<form id="enc1" name="enc1" action="<?=Yii::$app->params['baseUrl'];?>index.php?r=soporte/instrumento/addencuesta2" method="POST">
      
      <input type="hidden" id="jid" name="jid" value="0"/>
      <input type="hidden" id="x3sortedIDs" name="x3sortedIDs" value="0"/>


      <div class="form-group">
      <label for="de">Proyecto:</label>
      <select class="form-control" id="proyecto" name="proyecto" placeholder="Seleccione"/>
<option value="test63">test63</option>
      </select>
      </div>

      
      
       <div class="form-group">
      <label for="de">Descripción de la encuesta:</label>
      <input type="text" class="form-control" id="de" name="de" placeholder="Coloque el nombre o la descripción del instrumento de la encuesta"/>
      </div>

      
<div class="btn-group">
  <button type="button" class="btn btn-primary" style="width:150px;" onClick="jid=entrada('sortable');" >Agregar Entrada</button>
  <button type="button" class="btn btn-primary" style="width:150px;" onClick="edit1=x3sortable();" >Ordenar / Editar</button>
  <button type="button" class="btn btn-danger" style="width:150px;" onClick="x3funSubmit('enc1');" > Salvar </button>
</div>

      <ul id="sortable" class="sortable"></ul>      


</form>
<script>
$(document).ready(function(){$(':input[type="submit"]').prop('disabled', true);});
</script>