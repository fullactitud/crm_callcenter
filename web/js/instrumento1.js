/* CRM de Call&Call */
var x3entAct, x3entNext, x3entAnt, x3respuestaAnt; 
var x3vctRespEnt = new Array();
var x3vctRespEntOp = new Array();
var x3vctNextEnt = new Array();
var x3vctEntTp = new Array();
var x3mk001 = x3mk002 = x3mk003 = x3mk004 = x3mk005 = x3mk006 = x3mk007 = x3mk008 = x3mk009 = x3mk010 = x3mk011 = x3mk012 = x3mk013 = x3mk014 = x3mk015 = x3mk016 = x3mk017 = x3mk018 = x3mk019 = x3mk020 = x3mk021 = x3mk022 = x3mk023 = x3mk024 = x3mk025 = x3mk026 = x3mk027 = x3mk028 = x3mk029 = x3mk030 = x3mk031 = x3mk032 = x3mk033 = x3mk034 = x3mk035 = x3mk036 = x3mk037 = x3mk038 = x3mk039 = x3mk040 = x3mk041 = x3mk042 = x3mk043 = x3mk044 = x3mk045 = x3mk046 = x3mk047 = x3mk048 = x3mk049 = x3mk050 = x3mk051 = x3mk052 = x3mk053 = x3mk054 = x3mk055 = x3mk056 = x3mk057 = x3mk058 = x3mk059 = x3mk060 = x3mk061 = x3mk062 = x3mk063 = x3mk064 = x3mk065 = x3mk066 = x3mk067 = x3mk068 = x3mk069 = x3mk070 = x3mk071 = x3mk072 = x3mk073 = x3mk074 = x3mk075 = x3mk076 = x3mk077 = x3mk078 = x3mk079 = x3mk080 = x3mk081 = x3mk082 = x3mk083 = x3mk084 = x3mk085 = x3mk086 = x3mk087 = x3mk088 = x3mk089 = x3mk090 = x3mk091 = x3mk092 = x3mk093 = x3mk094 = x3mk095 = x3mk096 = x3mk097 = x3mk098 = x3mk099 = null;


/** 
PASAR ESTO AL CONTROLLER ASI
 * Usado en ASI
 * Evalua que al llegar a una pregunta (de codigo 'next'), 
 * se halla pasado por otra pregunta u opción (donde se marco la opcion 'x3mk001')
 * 
 * Se requiere (obligatoriamente) una funcion por cada instrumento, aunque no haga nada.
 * Nombre de la función: comiensa con 'x3' + el codigo del instrumento + 'IrA'
 * Los parametros son; 1:entrada actual, 2:entrada proxima, 3:tipo de entrada, 4:javascript adicional
 
function x3asiIrA( act, next, tp, valorOpcion ){
	if( next == 'co1_pre' && x3mk001 != '1' )
		next = 'ofe1_pre';
	if( next == 'pre1_pre' && x3mk001 != '1' )
		next = 'acc1_pre';
	return true;
};
*/



var answerFunction;

 

function x3Confirm( text, boton1, boton2, answerFunc ){
    alert('confirm');  
    if( false ){	
	var box = document.getElementById("x3ConfirmBox");
	box.getElementsByTagName("p")[0].firstChild.nodeValue = text;
	var button = box.getElementsByTagName("input");
	button[0].value=button1;
	button[1].value=button2;
	answerFunction = answerFunc;
	// box.style.visibility="visible";
	$('#x3ConfirmBox_text').html(text);
	alert('aqui');
	$('#x3ConfirmBox').show();
    }
    
    var box2 = $('#x3ConfirmBox');
    
    box2.dialog({ title: 'Selección', width: 400, height: 300 ,modal: true });
    // box2.html( text );
    $('#x3ConfirmBox_text').html( text );
    $('#x3ConfirmBox_op1').html( boton1 );	
    box2.show();
    
    
};






function answer( response ){
    alert('ocultar');
    $('#x3ConfirmBox').hide();
    answerFunction(response);
};

 



var soloBorrar = false;

function x3IrA( codigo, act, next, tp = '2', valorOpcion = null ){
    if( x3vctRespEnt[act] != undefined ){
	/*
	  x3Confirm("Seleccione una opción","Borrar las respuestas posteriores a esta pregunta","No borrar, solo corregir el texto",
	  function(answer){
	  button.value="Last answer was: "+(answer?"Aye":"Nay");
	  });
	  //	alert('seth');
	  */
    }
    
    
   // soloBorrar = false;
    if( soloBorrar == false ){
	x3entAct = act;
	x3entNext = next;
 	
	/* de-seleccionar la entrada que se habia guardado anterioemente */
	if( x3vctRespEnt[act] != undefined )
	    x3deSeleccionar( x3vctRespEnt[act] );
	
	/* guarda la entrada siguiente de la actual */
	if( x3entAnt != undefined ){
	    //    x3vctRespEnt[x3entAnt] = act;
	    x3vctRespEnt[act] = next;
	    x3vctRespEntOp[act] = valorOpcion;
	}else x3vctRespEnt['p'] = 'f';
	x3entAnt = act;
	
	if( x3entNext != undefined )
	    next = x3entNext;
	if( next = eval('x3' +codigo +'IrA(act, next, tp, valorOpcion)' ) ){
//    alert( 'c:' +codigo+' a:'+ act+' n:'+ next+' tp:'+ tp+' vo:'+ valorOpcion  );	    
	    if( tp == '2' && $('#x3campo_'+act).val() == '' ){
		alert('Debe introducir texto');
	    }else{			
		if( tp == '2' ) 
		    $('#btn_' +act).hide();
		$('#x3entrada_' +next).show();
		if( x3vctEntTp[next] == 1 ){ /* es un texto */
		    x3entNext = undefined;
		    x3IrA(codigo, next, x3vctNextEnt[next], x3vctEntTp[next]);
		}else /* es una pregunta */
		    $('#btn_' +next).show(); 
		$('html,body').animate({scrollTop: $('#a_'+next).position().top},150);
		x3entNext = undefined;
   		
	    }
	}
	soloBorrar = false;
    }
        
};






function x3deSeleccionar( id ){
    if( x3vctRespEnt[id] != undefined ) 
	x3deSeleccionar( x3vctRespEnt[id] );
    x3vctRespEnt[id] = undefined;
    x3Clear( id, x3vctRespEntOp[id] );
    return;
};






function x3Clear( id, valorOpcion ){
    if( $('#x3campo_' +id) != undefined ){
	if( $('#x3campo_' +id ).is( ':text' ) ) 
	    $('#x3campo_' +id).val('');
	else if( $('#x3campo_' +valorOpcion ).is( ':radio' ) )
	    $('#x3campo_' +valorOpcion).prop( 'checked', false );
    }
    if( $('#x3entrada_' +id) != undefined ) 
	$('#x3entrada_' +id).hide(); 
    if( $('#btn_' +id) != undefined ) 
	$('#btn_' +id).hide();    
    return;
};


