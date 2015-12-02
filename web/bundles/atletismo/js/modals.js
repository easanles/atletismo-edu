
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
    		  if (status == "success"){
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
  		      if (status == "success"){
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
   		      if (status == "success"){
   			    $("#dialog-body").html(data.message);
   		        collectionHolder = $('#form-collection');
		        collectionHolder.data('index', collectionHolder.find('.subform-row').length);
   			    $("#dialog-body").data('listener', 'pru_tprf');
   			    addListeners("pru_tprf");
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
   		      if (status == "success"){
   			    $("#dialog-body").html(data.message);
   		        collectionHolder = $('#form-collection');
		        collectionHolder.data('index', collectionHolder.find('.subform-row').length);
   			    $("#dialog-body").data('listener', 'pru_tprf');
   			    addListeners("pru_tprf");
   	          } else {
   			    $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("delPRU"): { //Borrar prueba
    	   $('#dialog-label').html("Borrar prueba");
    	   $("#dialog-body").html("¿Está seguro de borrar la prueba <strong>" + data3 + "</strong>?<br>Se borrarán también las inscripciones y registros de esta prueba.");
		   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"./" + data1 + "/borrar?i=" + data2 + "\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</a>");           
	   } break;
	   
       case ("newCAT"): { //Nueva categoria
    	   $('#dialog-label').html("Agregar categoría");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./configuracion/categoria/nuevo", function(data, status){
   		      if (status == "success"){
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
   		      if (status == "success"){
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
	   
	   case ("newPAR"): { //Confirmar participacion
    	   $('#dialog-label').html(data2);
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-bullhorn\"></span> Confirmar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./participar?atl=" + data1, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("ediINS"): { //Editar inscripciones
    	   $('#dialog-label').html("Editar inscripciones");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./inscripciones/editar?atl=" + data1, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("delINS"): { //Borrar inscripciones
		   dataIns = [];
    	   $('#dialog-label').html("Eliminar inscripciones");
    	   $('#dialog-btn').html("<button id=\"btn-send\" class=\"btn btn-danger\" disabled><span class=\"glyphicon glyphicon-remove-sign\"></span> Desinscribir</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./inscripciones/borrar?atl=" + data1, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
    	   $('#dialog-btn').click(function(){
    	      $.ajax({
    		     type        : "post",
    			 url         : "./inscripciones/borrar?atl=" + data1,
    		     data        : {data: dataIns},
    			 success     : function(data) {
    			    if (data.success == false){
    				   $('#dialog-body').html(data.message);
			    	} else if (data.success == true){
     	               $("#modal-dismiss").click();
    				   location.reload();
    				}
    		     }
    	      })
    	   });

       } break;
	   
	   default: break;
	}
	
	modal.modal();
}

function modalToggleButton(item){
	id = item.id.split("-")[1];
	if ($(item).hasClass("btn-default")){
		$(item).removeClass("btn-default");
		$(item).addClass("btn-danger");
		$(item).html("<span class=\"glyphicon glyphicon-remove-sign\"></span> <strong>SI</strong>");
		dataIns.push(id);
		$("#btn-send").attr("disabled", false);
	} else {
		$(item).removeClass("btn-danger");
		$(item).addClass("btn-default");		
		$(item).html("<span class=\"glyphicon glyphicon-remove-sign\"></span> NO");
		dataIns.splice($.inArray(id, dataIns), 1);
		if (dataIns.length == 0){
			$("#btn-send").attr("disabled", true);
		}
	}
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


