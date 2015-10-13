
function showModal(type, data1, data2, data3){
	var modal = $('#modalDialog');
	switch(type){
	
	   case ("delCOM"): { //Borrar competicion
		   $('#data1').html(data1);
		   $('#data2').html(data2);
		   $('#confirmbutton').attr("href", data3);
	   } break;
	   
       case ("newTPR"): { //Nuevo tipo de prueba
    	   $('#dialog-label').html("Nuevo tipo de prueba");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Crear</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./configuracion/tipoprueba/nuevo", function(data, status){
    		  if (status = "success"){
    		     $("#dialog-body").html(data.message);
    		     collectionHolder = $('#form-collection');
    		     collectionHolder.data('index', collectionHolder.find('.subform-row').length);
    	      } else {
    			 $("#dialog-body").html("Error al cargar datos");
    	      }	
    	   });
       } break;
       
       case ("delTPR"):{ //Borrar tipo de prueba
    	   $('#dialog-label').html("Borrar tipo de prueba");
		   $("#dialog-body").html("¿Está seguro de borrar el tipo de prueba <strong>" + data1 + "</strong>?");
		   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"./configuracion/tipoprueba/borrar?i=" + data2 + "\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</a>");           
       } break;
       
       case ("ediTPR"): { //Editar tipo de prueba
    	   $('#dialog-label').html("Editar tipo de prueba <small> - " + data1 + "</small>");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./configuracion/tipoprueba/editar/" + data2, function(data, status){
  		      if (status = "success"){
  			    $("#dialog-body").html(data.message);
                collectionHolder = $('#form-collection');
		        collectionHolder.data('index', collectionHolder.find('.subform-row').length);
  	          } else {
  			     $("#dialog-body").html("Error al cargar datos");
  			  }	
  	       });
       } break;
       
       case ("newPRU"): { //Nueva prueba
    	   $('#dialog-label').html("Agregar prueba");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./" + data1 + "/nuevo", function(data, status){
   		      if (status = "success"){
   			    $("#dialog-body").html(data.message);
   			    $("#dialog-body").data('listener', 'pru_tprf');
   			    addListeners("pru_tprf");
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;

       case ("newRondaPRU"): { //Añadir ronda prueba
    	   $('#dialog-label').html("Añadir una ronda");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./" + data1 + "/nuevo?pru=" + data2+"&mod=ronda", function(data, status){
   		      if (status = "success"){
   			    $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
       case ("dupPRU"): { //Duplicar prueba
    	   $('#dialog-label').html("Duplicar prueba");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./" + data1 + "/nuevo?pru=" + data2+"&mod=dupl", function(data, status){
   		      if (status = "success"){
   			    $("#dialog-body").html(data.message);
   	          } else {
   			    $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
       case ("ediPRU"): { //Editar prueba
    	   $('#dialog-label').html("Editar prueba");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./" + data1 + "/editar/"+data2, function(data, status){
   		      if (status = "success"){
   			    $("#dialog-body").html(data.message);
   			    $("#dialog-body").data('listener', 'pru_tprf');
   			    addListeners("pru_tprf");
   	          } else {
   			    $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("delPRU"): { //Borrar prueba
    	   $('#dialog-label').html("Borrar prueba");
    	   if ((data2 == null) || (data2 == "")){
    		   $("#dialog-body").html("¿Está seguro de borrar esta prueba?");
    	   } else {
    		   $("#dialog-body").html("¿Está seguro de borrar la prueba <strong>" + data2 + "</strong>?");
    	   }
		   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"./" + data1 + "/borrar?i=" + data3 + "\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</a>");           
	   } break;
	   
	   case ("delPRUwarn"): { //Borrar prueba con ronda unica
    	   $('#dialog-label').html("Borrar prueba");
    	   if ((data2 == null) || (data2 == "")){
    		   $("#dialog-body").html("Esta prueba es la última de esta ronda. Se borraran también las pruebas con ronda posterior. ¿Está seguro de borrar?");
    	   } else {
    		   $("#dialog-body").html("La prueba <strong>" + data2 + "</strong> es la última de esta ronda. Se borrarán también las pruebas con ronda posterior. ¿Está seguro de borrar?");
    	   }
		   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"./" + data1 + "/borrar?i=" + data3 + "\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</a>");           
	   } break;
	   
       case ("newCAT"): { //Nueva categoria
    	   $('#dialog-label').html("Agregar categoría");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./configuracion/categoria/nuevo", function(data, status){
   		      if (status = "success"){
   			    $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
       case ("ediCAT"): { //Editar categoria
    	   $('#dialog-label').html("Editar categoría <small> - " + data1 + "</small>");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./configuracion/categoria/editar/" + data2, function(data, status){
   		      if (status = "success"){
   			    $("#dialog-body").html(data.message);
   	          } else {
   			    $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("cadCAT"): { //Caducar categoria
    	   $('#dialog-label').html("Caducar categoría");
    	   $("#dialog-body").html("¿Está seguro de marcar como caducada la categoría <strong>" + data1 + "</strong>?");
		   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"./configuracion/categoria/caducar?i=" + data2 + "\"><span class=\"glyphicon glyphicon-remove-circle\"></span> Caducar</a>");           
	   } break;
	   
	   case ("delATL"): { //Borrar atleta
		   $('#data1').html(data1);
		   $('#confirmbutton').attr("href", data2);
	   } break;
       
       default: break;
	}
	
	modal.modal();
}

//funcion para modificar datos de formulario en funcion de una seleccion previa
function addListeners(name){
	switch (name){
	   case("pru_tprf"): {
 		      $('#pru_tprf').change(function(){
   			   form = $(this).closest('form');
   			   data = {};
   			   data[$(this).attr('name')] = $(this).val();
   			   $.ajax({
   			      url : form.attr('action'),
   			      type: form.attr('method'),
   			      data : data,
   			      success: function(html) {
     			      $('#pru_sidtprm').replaceWith($(html.message).find('#pru_sidtprm'));
                  }
   			   });
   			});
	   } break;
	   default: break;
	}
}

function submitDialogForm(){
	  var values = {};
	  form = $('form');
	  $.each( form.serializeArray(), function(i, field) {
	    values[field.name] = field.value;
	  });
	 
	  $.ajax({
	    type        : form.attr( 'method' ),
	    url         : form.attr( 'action' ),
	    data        : values,
	    success     : function(data) {
	    	if (data.success == false){
	  	       $('#dialog-body').html(data.message);
			   addListeners($("#dialog-body").data('listener'));
	    	} else if (data.success == true){
	    	   $("#modal-dismiss").click();
	    	   $(".updater").click();
	    	}
	    }
	  });
}


