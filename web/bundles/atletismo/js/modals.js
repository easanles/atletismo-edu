

function showModal(type, data1, data2, data3){
	var modal = $('#modalDialog');
	switch(type){
	
	   case ("delCOM"): { //Borrar competicion
		   $('#data1').html(data1);
		   $('#data2').html(data2);
		   $('#confirmbutton').attr("href", "./competiciones/borrar?i="+ data3);
	   } break;
	   
       case ("newTPR"): { //Nuevo tipo de prueba
    	   $('#dialog-label').html("Nuevo tipo de prueba");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Crear</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./tipoprueba/nuevo", function(data, status){
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
		   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"./tipoprueba/borrar?i=" + data2 + "\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</a>");           
       } break;
       
       case ("ediTPR"): { //Editar tipo de prueba
    	   $('#dialog-label').html("Editar tipo de prueba <small> - " + data1 + "</small>");
    	   $('#dialog-btn').html("<a class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</a>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./tipoprueba/editar/" + data2, function(data, status){
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
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
       default: break;
	}
	
	modal.modal();
}

