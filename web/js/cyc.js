/* CRM de Call&Call */
var x3help = 0;

function toInt(valor){
    if( isNaN(valor) ) return 0; 
    else return valor; 
};

function carga( div, op, accion = 'df', control = 'df' ){
    div = $( "#" + div );
    $.ajax({ type: 'POST', async: true
			 , url: baseUrl +'/index.php?r=' +control +'/' +accion
			 , data: '&' +op +'&'
			 , success: function( txt ){
				 div.html( txt );
				 /* div.dialog({ title: titulo, width: 400, modal: true }); */
			 }
           });
    return;
};

function update( a, b, c, d ){
    $.ajax({ type: 'POST', async: true
			 , url: baseUrl + 'index.php?r=df/in'
			 , data: '&a='+ a + '&b='+ b + '&c='+ c + '&d='+ d + '&'
			 , success: function( txt ){}
           });
	/*	alert(baseUrl + 'index.php?r=df/in&a='+ a + '&b='+ b + '&c='+ c + '&d='+ d + '&'); */ 
    return;
};

$(function(){
    var menuVisible = false;
    var containerVisible = true;
    $('#menuBtn').click(function(){
        if( menuVisible ){
            $('#myMenu').css({'display':'none'});
            $('#contenedor').css({'display':'block'});
            menuVisible = false;
            containerVisible = true;
            return;
        }
        $('#myMenu').css({'display':'block'});
        $('#contenedor').css({'display':'none'});
        menuVisible = true;
        containerVisible = false;
    });
    $('#myMenu').click(function(){
        $(this).css({'display':'none'});
        $('#contenedor').css({'display':'block'});
        menuVisible = false;
        containerVisible = true;
    });
});
