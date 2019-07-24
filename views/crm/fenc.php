
<style>
#contentLeft li:hover {
    cursor: pointer;
}

#contentLeft li.ui-sortable-helper{
    cursor: move;
}
</style>

<script type="text/javascript" src="js/encuesta1.js"></script>
<script type="text/javascript">
var jid = <?=$jid;?>;
<?=$cad0;?>
</script>


    

<div class="titulos"><?=$titulo;?></div>




<form id="enc1" name="enc1" action="<?=Yii::$app->params['baseUrl'];?>index.php?r=soporte/instrumento/addencuesta2" method="POST">
      <input type="hidden" id="jid" name="jid" value="0"/>
      <input type="hidden" id="x3sortedIDs" name="x3sortedIDs" value="0"/>



      
<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;"> 
  <div class="control-label col-sm-1" for="de">Proyecto:</div>
  <div class="col-sm-2"><?=$proyectos;?></div>
  <div class="col-sm-1">  </div>
      
  <div class="control-label col-sm-1" for="admin">Administrador:</div>
  <div class="col-sm-2">
    <select id="admin" name="admin" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_usuarios;?></select>
  </div>
  <div class="col-sm-1">  </div>

  <div class="control-label col-sm-1" for="st">Estatus:</div>
  <div class="col-sm-2">
        <select id="st" name="st" placeholder="Seleccione" class="form-control" style="width:100%;">
          <option value="1">Activo</option>
          <option value="3">En espera</option>
          <option value="0">Eliminado</option>
        </select>
  </div>
      
</div>



 <!-- ///////////////  -->

<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">

  <div class="control-label col-sm-2" for="de">Descripción del Instrumento:</div>
  <div class="col-sm-5">
    <input type="text" class="form-control" id="de" name="de" placeholder="Coloque el nombre o la descripción del instrumento de la encuesta" style="width:100%;" value="<?=$instrumento_de;?>"/>
  </div>
  <div class="col-sm-1">  </div>

  <div class="control-label col-sm-1" for="codigo">Código:</div>
  <div class="col-sm-2">
      <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código del instrumento (alfanúmerico sin espacios)" value="<?=$codigo;?>" style="width:100%;"/>
  </div>
      
</div>

      
     
<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;"> 
          
      <div class="control-label col-sm-1" for="jquery"> &nbsp; Tipo de Instrumento:</div>
  <div class="col-sm-2">
    <select id="instr_tp" name="instr_tp" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_instr_tp;?></select>
  </div>
  <div class="col-sm-1">  </div>

</div>


<div style="clear:both;"><br /><hr /></div>


<div style="background-color:#cccccc; line-height: 2.0em;">
  <div class="subtitulos" title="Preguntas o Textos"> &nbsp;  Presentación</div>
</div>
<br />
      
<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;"> 
      
  <div class="control-label col-sm-1" for="jquery">Tema JQuery:</div>
  <div class="col-sm-2">
     <select id="jquery" name="jquery" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_jquery;?></select>
  </div>
  <div class="col-sm-1">  </div>
      
  <div class="control-label col-sm-1" for="bootstrap">Tema Bootstrap:</div>
  <div class="col-sm-2">
    <select id="bootstrap" name="bootstrap" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_bootstrap;?></select>
  </div>
  <div class="col-sm-1">  </div>
      
  <div class="control-label col-sm-1" for="font">Tipo de Letra:</div>
  <div class="col-sm-2">
    <select id="font" name="font" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_font;?></select>
  </div>

</div>


      

<div style="clear:both;"><br /><hr /></div>


<div style="background-color:#cccccc; line-height: 2.0em;">
  <div class="subtitulos" title="Preguntas o Textos"> &nbsp;  Comportamiento del instrumento</div>
</div>
<br />

      
<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
      
  <div class="control-label col-sm-1" for="columna_desplegar">Desplegar:</div>
  <div class="col-sm-2">
      <select id="columna_desplegar" name="columna_desplegar" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_desplegar;?></select>
    </div>
  <div class="col-sm-1">  </div>
      
  <div class="control-label col-sm-1" for="columna_siguiente">Siguiente:</div>
  <div class="col-sm-2">
      <select id="columna_siguiente" name="columna_siguiente" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_siguiente;?></select>
    </div>
  <div class="col-sm-1">  </div>


  <div class="control-label col-sm-1" for="columna_back">Retroceder:</div>
  <div class="col-sm-2">
    <select id="columna_back" name="columna_back" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_back;?></select>
  </div>
  <div class="col-sm-1">  </div>

</div>



<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">

  <div class="control-label col-sm-1" title="Habilitar llamar luego">LLamar Luego:</div>
  <div class="col-sm-2"><?=$option_llamar_luego;?></div>
  <div class="col-sm-1">  </div>
      

  <div class="control-label col-sm-1"> Uso de dominios:</div>
  <div class="col-sm-2"><?=$option_dominios;?></div>
  <div class="col-sm-1">  </div>                                                                  
                                                                  


  <div class="control-label col-sm-1"> Uso de cuotas:</div>
  <div class="col-sm-2"><?=$option_cuotas;?></div>
  <div class="col-sm-1">  </div>

                                                                  
</div>


<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">

  <div class="control-label col-sm-1" for="entrada_inicial">Entrada Inicial:</div>
  <div class="col-sm-2">
    <input placeholder="Primera pregunta" class="form-control" style="width:100%;" type="text" id="entrada_inicial" name="entrada_inicial" value="<?=$entrada_inicial;?>"/>
  </div>
  <div class="col-sm-1">  </div>

                                                                  
</div>

      


<div style="clear:both;"><br /><hr /></div>
<div style="background-color:#cccccc; line-height: 2.0em;">
  <div class="subtitulos" title="Preguntas o Textos"> &nbsp;  Origen de los datos, para uso interno del sistema</div>
</div>
<br />
      
<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
      
  <div class="control-label col-sm-1" for="columna_fecha_ref">Fecha de REF:</div>
  <div class="col-sm-2">
    <select id="columna_fecha_ref" name="columna_fecha_ref" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_fecha_ref;?></select>
  </div>
  <div class="col-sm-1">  </div>

  <div class="control-label col-sm-1" for="columna_movil">Telefono:</div>
  <div class="col-sm-2">
      <select id="columna_movil" name="columna_movil" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_movil;?></select>
  </div>
  <div class="col-sm-1">  </div>

  <div class="control-label col-sm-1" for="columna_nombre">Nombre del Prospecto:</div>
  <div class="col-sm-2">
    <select id="columna_nombre" name="columna_nombre" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_nombre;?></select>
  </div>
  <div class="col-sm-1">  </div>

</div>



      

<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-1" for="columna_agente">Agente:</div>
  <div class="col-sm-2">
    <select id="columna_agente" name="columna_agente" placeholder="Seleccione" class="form-control" style="width:100%;"><?=$option_agente;?></select>
  </div>
  <div class="col-sm-1">  </div>



  <div class="control-label col-sm-1" for="cdominio">Columna de dominio:</div>
  <div class="col-sm-2">
    <select id="columna_dominio" name="columna_dominio" placeholder="Seleccione" class="form-control" style="width:100%;">
      <?=$option_cdominio;?>
    </select>
  </div>
  <div class="col-sm-1">  </div>
   
      
</div>





<div style="clear:both;"><br /><hr /></div>
<div style="background-color:#cccccc; line-height: 2.0em;">
  <div class="subtitulos" title="Preguntas o Textos"> &nbsp; Carga de data</div>
</div>
<br />
<?=$carga_data;?>


                                                                 

   <div class="" style="clear: both;">
      <button type="button" class="btn btn-primary" style="width:150px;" onClick="nCabecera=addcolumna(nCabecera);" > Agregar Columna </button>
      </div>

                                                                  
 <!-- ///////////////  -->


<div style="clear:both;"><br /><hr /></div>
<div style="background-color:#cccccc; line-height: 2.0em;">
  <div class="subtitulos" title="Reportes"> &nbsp; Reportes permitidos</div>
</div>
<br />      
<?=$reportes;?>
                                                    
   
                                                                  
  <!-- ///////////////  -->

       
      <div style="clear:both;"><br /></div>

      <div style="background-color:#cccccc; line-height: 2.0em;">
        <div class="subtitulos" title="Preguntas o Textos"> &nbsp;  Preguntas o Textos</div>
      </div>
    
  
      
      
  <ul id="sortable" class="sortable"><?=$cargado;?></ul>
  
      
      <div cl555ass="btn-group" style="clear: both;">
      <hr/>
      <button type="button" class="btn btn-primary" style="width:150px;" onClick="jid=entrada('sortable');" > Agregar Entrada </button>

      <button type="button" class="btn btn-danger" style="width:150px;" onClick="x3funSubmit('enc1');" > Salvar </button>
      </div>




</form>


      




      
<script>
$(document).ready(function(){$(':input[type="submit"]').prop('disabled', true);});
</script>
