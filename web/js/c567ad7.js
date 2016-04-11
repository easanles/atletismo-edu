
//FORMULARIO DE REGISTRO DE MARCAS

var pruData = null;
var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";
var selectedPru = null;


function loadPru(com){
    $("#select-cat").html("");
    $("#select-cat").attr("disabled", true);		
    $("#select-atl").html("");	
    selectedPru = null;
    $("#select-ron").html("");	

	if ((com == null) || com === ""){
        $("#select-pru").html("");
        $("#select-pru").attr("disabled", true);		
	} else {
		
		$.ajax({
		    type: "get",
			url: "./marcas/getpru?com="+com,
			success: function(data, status) {
		        if (status == "success"){
		            pruData = data['result'];
		            html = "<option value=\"\"> </option>";
		            for (i = 0; i < pruData.length; i++){
		               html = html + "<option value=\"" + i + "\">" + pruData[i]['tprm'] + "</option>";
		            }
		            $("#select-pru").html(html);
		            $("#select-pru").attr("disabled", false);
		            
		    		if ((typeof autoSelectPru !== 'undefined') && (autoSelectPru != null)){
		    			ok = false;
		    			for(i = 0; i < pruData.length; i++){
		    				for(j = 0; j < pruData[i]['cats'].length; j++ ){
		    					if (pruData[i]['cats'][j]['sid'] == autoSelectPru){
		    						$('#select-pru').val(i);
		    						showCats(i);
		    						$('#select-cat').val(autoSelectPru);
		    						ok = true;
		    						break;
		    					}
		    				}
		    				if (ok) break;
		    			}
		    			loadAtls(autoSelectPru);
		    			autoSelectPru = null;
		    		}
		        }
		    }
		});	
	}	
}

function showCats(pru){
    $("#select-atl").html("");
    selectedPru = null;
    $("#select-ron").html("");	

	if ((pru == null) || (pru === "")){
        $("#select-cat").html("");
        $("#select-cat").attr("disabled", true);
	} else {
        html = "<option value=\"\"> </option>";
        for (i = 0; i < pruData[pru]['cats'].length; i++){
           html = html + "<option value=\"" + pruData[pru]['cats'][i]['sid'] + "\">" + pruData[pru]['cats'][i]['nombre'] + "</option>";
        }
        $("#select-cat").html(html);
        $("#select-cat").attr("disabled", false);		
	}
}

function loadAtls(pru){
    $("#select-ron").html("");
    $("#select-atl").html(loadingIcon);
	$.ajax({
	    type: "get",
		url: "./marcas/getatl?pru="+pru,
		success: function(data, status) {
	        if (status == "success"){
	            $("#select-atl").html(data);
	            selectedPru = pru;
    			if ((typeof autoSelectAtl !== 'undefined') && (autoSelectAtl != null)){
    				toggleRadioButton($('#sel-atl-' + autoSelectAtl));
    				autoSelectAtl = null;
    			}
	        }
	    }
	});	
}

function loadRons(){
	item = $('.btn-info')[0];
	data = item.id.split("-");
	type = data[1];
	idAtl = data[2];
	$("#select-ron").html(loadingIcon);
    $.get("./marcas/getron?pru="+ selectedPru + "&atl=" + idAtl, function(data, status){
        if (status == "success"){
           $("#select-ron").html(data);
 		   $('abbr').tooltip()
        } else {
           $("#select-ron").html("Error al cargar datos");
        }
     }); 
}

function toggleRadioButton(item){
	if ($(item).hasClass("btn-default")){
		$(".sel-atl").each(function(){
			$(this).removeClass("btn-info");
			$(this).removeClass("updater");
			$(this).addClass("btn-default");
			$(this).attr("aria-pressed", false);
			$(this).parent().closest("tr").attr("class", "");
		})
		$(item).removeClass("btn-default");
		$(item).addClass("btn-info");
		$(item).addClass("updater");
		$(item).attr("aria-pressed", true);
		$(item).parent().closest("tr").attr("class", "info");
	}
	loadRons();
}

function checkIntentos(){
	button = $("#btn-addInt");
	button.prop("disabled", false);
	maxNumInt = parseInt($('#max-num-int').html());
	inputGroups = $('.input-group');
	$('.has-success').removeClass("has-success");
	checkboxes = $('input:checkbox');
	countInvalidos = 0;
	for (i = 0; i < checkboxes.length; i++){
		if ($(checkboxes[i]).is(":checked")){
			$('.has-success').removeClass("has-success");
			$(inputGroups[i]).addClass("has-success");
			countInvalidos = 0;
		} else countInvalidos++;
		if (countInvalidos >= maxNumInt){
			button.prop("disabled", true);
			break;
		}
	}
}

function validateFields(){
	error = false;

	function checkIndivField(id){
		number = parseInt($(id).val());
		valid = $(id).prop("validity").valid;
		if (!valid || (number < 0)
				|| ( ((id == "#marca-minutos") || (id == "#marca-segundos")) && (number > 59))
				|| ((id == "#marca-milesimas") && (number > 999))){
		   $(id).addClass("has-error");
		   return true;
		} else {
		   $(id).removeClass("has-error");
		   return false;
		}
	}
	
	if (checkIndivField("#marca-horas")) error = true;
	if (checkIndivField("#marca-minutos")) error = true;
	if (checkIndivField("#marca-segundos")) error = true;
	if (checkIndivField("#marca-milesimas")) error = true;
	if (error) {
	   $("#dialog-btn a").attr("disabled", true);
	} else {
	   $("#dialog-btn a").attr("disabled", false);
	   valor = 0;
	   aux = parseInt($("#marca-horas").val());
	   if (!isNaN(aux)) valor += aux * 3600;
	   aux = parseInt($("#marca-minutos").val());
	   if (!isNaN(aux)) valor += aux * 60;
	   aux = parseInt($("#marca-segundos").val());
	   if (!isNaN(aux)) valor += aux;
	   aux = parseInt($("#marca-milesimas").val());
	   if (!isNaN(aux)) valor += aux / 1000;
	   $("#intGroup_intentos_0_marca").val(valor);
	}
}


$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		  $('abbr').tooltip()
          $('.dropdown-toggle').dropdown()
    });
	if ($("#select-com").selectedIndex != 0){
		loadPru($("#select-com").find(":selected").attr("value"));
	}
})


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
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Crear</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./configuracion/tipoprueba/nuevo", function(data, status){
    		  if (status == "success"){
    		     $("#dialog-body").html(data.message);
    		     collectionHolder = $('#form-collection');
    		     collectionHolder.data('index', collectionHolder.find('.subform-row').length);
    		     addAutoComplete(".subform-row input", data.entornos); //scripts.js
    	      } else {
    			 $("#dialog-body").html("Error al cargar datos");
    	      }	
    	   });
       } break;
       
       case ("delTPR"):{ //Borrar tipo de prueba
    	   $('#dialog-label').html("Borrar tipo de prueba");
		   $("#dialog-body").html("¿Está seguro de borrar el tipo de prueba <strong>" + data1 + "</strong>?");
		   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-danger\" onClick=\"submitDialogAction('./configuracion/tipoprueba/borrar?i=" + data2 + "')\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</button>");           
       } break;
       
       case ("ediTPR"): { //Editar tipo de prueba
    	   $('#dialog-label').html("Editar tipo de prueba <small> - " + data1 + "</small>");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./configuracion/tipoprueba/editar/" + data2, function(data, status){
  		      if (status == "success"){
  			    $("#dialog-body").html(data.message);
                collectionHolder = $('#form-collection');
		        collectionHolder.data('index', collectionHolder.find('.subform-row').length);
   		        addAutoComplete(".subform-row input", data.entornos); //scripts.js
  	          } else {
  			     $("#dialog-body").html("Error al cargar datos");
  			  }	
  	       });
       } break;
       
       case ("newPRU"): { //Nueva prueba
    	   $('#dialog-label').html("Agregar prueba");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
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
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
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
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
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
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
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
		   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
		   if (data3 == true){
			   $('#dialog-label').html("Cambiar temporada de caducidad");
			   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");			   
		   } else {
			   $('#dialog-label').html("Caducar categoría");
			   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-danger\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-remove-circle\"></span> Caducar</button>");			   
		   }
    	   $.getJSON("./configuracion/categoria/caducar?i=" + data2, function(data, status){
    		      if (status == "success"){
    			     $("#dialog-body").html(data.message);
    	          } else {
    			     $("#dialog-body").html("Error al cargar datos");
    			  }	
    	       });
	   } break;
	   
	   case ("estATL"): { //Cambiar estado de alta del atleta
		   switch (data3){
		      case "alta": {
		    	  $('#dialog-label').html("Dar de alta");
		    	  $("#dialog-body").html("¿Confirmar el alta del atleta <strong>" + data1 + "</strong>?");
				  $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-primary\" onClick=\"submitDialogAction('" + data2 + "')\"><span class=\"glyphicon glyphicon-ok-sign\"></span> Alta</button>");           
		      } break;
		      case "baja": {
		    	  $('#dialog-label').html("Dar de baja");
		    	  $("#dialog-body").html("¿Confirmar la baja del atleta <strong>" + data1 + "</strong>?");
				  $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-danger\" onClick=\"submitDialogAction('" + data2 + "')\"><span class=\"glyphicon glyphicon-remove-sign\"></span> Baja</button>");           		    	  
		      } break;
		      default: {
		    	  $('#dialog-label').html("");
		    	  $("#dialog-body").html("");
				  $("#dialog-btn").html("");           
		      }
		   }
	   } break;
	   
	   case ("delATL"): { //Borrar atleta
		   $('#dialog-label').html("Confirmar borrado");
		   $bodyHTML = "<p>¿Confirmar borrado del atleta <strong>" + data1 + "</strong>?</p>";
		   if (data3 != null){
			   $bodyHTML = $bodyHTML + "<p><input id=\"cascade-del-usu\" type=\"checkbox\" checked><label for=\"cascade-del-usu\" style=\"font-weight: normal; margin-left: 0.5em\"> Borrar también el usuario asociado \"" + data3 + "\"</span></p>"
			   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-danger\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</button>");
			   $('#dialog-btn').click(function(){
				   window.location.href = data2 + "&cascade=" + $('#cascade-del-usu').is(":checked"); 
			   });
		   } else {
			   $("#dialog-btn").html("<a type=\"button\" class=\"btn btn-danger\" href=\"" + data2 +"\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</a>"); 
		   }
		   $("#dialog-body").html($bodyHTML);
	   } break;
	   
	   case ("newPAR"): { //Confirmar participacion
    	   $('#dialog-label').html(data2);
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-bullhorn\"></span> Confirmar</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   if (data3 == null)
    	         url = "./participar?atl=";
    	   else url = "../competiciones/"+ data3 + "/participar?atl="
    	   $.getJSON(url + data1, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("ediINS"): { //Editar inscripciones
    	   $('#dialog-label').html("Editar inscripciones");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
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
       
	   case ("showINS"): { //Mostrar inscripciones a una prueba
    	   $('#dialog-label').html("Lista de atletas inscritos <small>- " + data2 + "</small>");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-ok\"></span> OK</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./" + data1 + "/listar?pru=" + data3, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("grupINS"): { //Mostrar inscripcion grupal
    	   $('#dialog-label').html("Inscripción grupal " + data1);
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-ok\"></span> OK</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./inscripciones/grupo?cod=" + data1, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("newINT"): { //Registrar nueva marca (intento)
    	   $('#dialog-label').html("Registrar marcas");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("./marcas/nuevo?ron=" + data1 + "&atl=" + data2, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data.message);
   			     $('abbr').tooltip()
    		     collectionHolder = $('#form-collection');
 		         collectionHolder.data('index', collectionHolder.find('.subform-row').length);
 		         if (data3 == true) checkIntentos(); //marcas.js
 		 	     if ($("#intGroup_intentos_0_marca").val() != ""){
 		 	    	 aux = $("#intGroup_intentos_0_marca").val();
 		 	    	 $("#marca-horas").val(Math.floor(aux / 3600));
 		 	    	 aux = aux - (Math.floor(aux / 3600) * 3600);
 		 	    	 $("#marca-minutos").val(Math.floor(aux / 60));
 		 	    	 aux = aux - (Math.floor(aux / 60) * 60);
 		 	    	 $("#marca-segundos").val(Math.floor(aux));
 		 	    	 aux = aux - Math.floor(aux);
 		 	    	 $("#marca-milesimas").val(Math.round(aux * 1000));
 		 	     }
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
	   case ("showINT"): { //Historico de intentos del atleta en una ronda
    	   $('#dialog-label').html("Histórico de intentos");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" data-dismiss=\"modal\"><span class=\"glyphicon glyphicon-ok\"></span> OK</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.get("./resultados/intentos?atl=" + data1 + "&ron=" + data2, function(data, status){
   		      if (status == "success"){
   			     $("#dialog-body").html(data);
   			     $('[data-toggle="popover"]').popover()
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
       
       case ("newUSU"): { //Nuevo usuario
    	   $('#dialog-label').html("Nuevo usuario");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Crear</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON(data1, function(data, status){
    		  if (status == "success"){
    		     $("#dialog-body").html(data.message);
    			 $('[data-toggle="tooltip"]').tooltip()
    			 if (data2 != null){
    				 $("#usu_idAtl").val(data2);
    				 $("#usu_idAtl").attr("readonly", true);
    			 }
    	      } else {
    			 $("#dialog-body").html("Error al cargar datos");
    	      }	
    	   });
       } break;
	   
	   case ("delUSU"): { //Borrar usuario
    	   $('#dialog-label').html("Borrar usuario");
    	   $("#dialog-body").html("¿Está seguro de borrar el usuario <strong>" + data2 + "</strong>? Perderá el acceso a la aplicación.");
		   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-danger\" onClick=\"submitDialogAction('"+ data1 + "?usu=" + data2 + "')\"><span class=\"glyphicon glyphicon-remove\"></span> Borrar</button>");           
	   } break;
	   
       case ("ediUSU"): { //Editar usuario
    	   $('#dialog-label').html("Editar usuario<small> - " + data2 + "</small>");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON(data1 + data2, function(data, status){
    		  if (status == "success"){
    		     $("#dialog-body").html(data.message);
    			 $('[data-toggle="tooltip"]').tooltip()
    			 if (data3 != null){
    				 $("#usu_idAtl").val(data3);
    				 $("#usu_idAtl").attr("readonly", true);
    			 }
    	      } else {
    			 $("#dialog-body").html("Error al cargar datos");
    	      }	
    	   });
       } break;
       
       case ("asigUSU"): { //Asignar usuario a un nuevo atleta desde el formulario de atletas
    	   $('#dialog-label').html(data1);
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"updateForm('atl')\"><span class=\"glyphicon glyphicon-ok\"></span> Aceptar</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");
    	   $.getJSON("./nuevo/usuario", function(data, status){
    		  if (status == "success"){
    		     $("#dialog-body").html(data.message);
    	      } else {
    			 $("#dialog-body").html("Error al cargar datos");
    	      }	
    	   });
       } break;
       
	   default: break;
	}
	
	modal.modal();
}

function autoFocusNextField(id){
	switch (id){
	   case ("marca-minutos"): {
		   if ($("#marca-minutos").val().length == 2){
		      $("#marca-segundos").focus();
		   }
	   } break;
	   case ("marca-segundos"): {
		   if ($("#marca-segundos").val().length == 2){
		      $("#marca-milesimas").focus();
		   }
	   } break;
	}
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
	  form = $('.modal-dialog form');
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
  			   $('[data-toggle="tooltip"]').tooltip();
	    	} else if (data.success == true){
	    	   $("#modal-dismiss").click();
	    	   $(".updater").each(function(){
	    		  if ($(this).is(":disabled") == false){
	    			  $(this).click();
	    		  } 
	    	   });
	    	}
	    }
	  });
}

function submitDialogAction(url){
   alerthtml_pre = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Error: </strong><span>";
   alerthtml_pos = "</span></div>";
   $.ajax({
      type        : "GET",
	  url         : url,
	  success     : function(data) {
	   	if (data.success == false){
	       $('#dialog-body').html(alerthtml_pre + data.message + alerthtml_pos);
	       $("#dialog-btn button").attr("disabled", true);
		} else if (data.success == true){
		   $("#modal-dismiss").click();
    	   $(".updater").each(function(){
	    	  if ($(this).is(":disabled") == false){
	    	     $(this).click();
	    	  } 
	       });
    	}
      }
   });
}

//funcion para modificar datos de un formulario desde otro formulario
function updateForm(formName){
	  var values = {};
	  form = $('.modal-dialog form');
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
	    	} else if (data.success == true){
	    	   switch (formName){
	    		   case("atl"): {
	    	          $('#atl_usu_nombre').val(data.message.nombre);
	    	          $('#atl_usu_contra').val(data.message.contra);
	    	          $('#atl_usu_rol').val(data.message.rol);
	    	          html = data.message.nombre;
	    	          if (data.message.rol === "coordinador"){
		    	          html = html + " <strong class=\"text-info\">Coordinador</strong>";
	    	          }
	    	          $('#usu_nombre_display').html(html);
	    	          $('#clear_usu').removeClass("hidden");
	    		   } break;
	    		   default: break;
	    	   }	
	    	   $("#modal-dismiss").click();
	    	}
	    }
	  });
}




var autoCompleteData;

function goToUrl(path){
	window.location.href = path;
}

function comSearch(temp, query){
	path = "./competiciones";
	if ((temp != null) && (temp != "")){
		if ((query != null) && (query != "")){
			path = path + "?temp=" + temp + "&q=" + query;
		} else {
			path = path + "?temp=" + temp;
		}
	} else {
		if ((query != null) && (query != "")) {
			path = path + "?q=" + query;
		}
	}
	goToUrl(path);
}

function atlSearchParam(cat, query){
	result = '';
	if ((cat != null) && (cat != "")){
		if ((query != null) && (query != "")){
			result = result + "?cat=" + cat + "&q=" + query;
		} else {
			result = result + "?cat=" + cat;
		}
	} else {
		if ((query != null) && (query != "")) {
			result = result + "?q=" + query;
		}
	}
	return result;
}

function atlSearch(url, cat, query){
	path = url;
	path = path + atlSearchParam(cat, query);
	goToUrl(path);
}

function pruSearch(id, cat){
	path = "./" + id;
	if ((cat != null) && (cat != "")) {
	   path = path + "?c=" + cat;
	}
	goToUrl(path);
}

function getQuery(){
	return document.getElementById('search-input').value;
}

function checkEnterKeypress(event){
	if (event.keyCode == 13) {
		document.getElementById("search-button").click();
	}
}

function toggleDropListTable(id, button){
   button.button('toggle');
   dl = $("#droplist-" + id);
   if (dl.css("height") == "0px"){
	   dl.css("height", dl.data("height"));
	   button.parent().closest("tr").attr("class", "info");
   }
   else {
	   dl.css("height", "0px");
	   button.parent().closest("tr").attr("class", "");
   }
}

function addFormRow(){
	collectionHolder = $('#form-collection')
	    
	prototype = collectionHolder.data('prototype');
	index = collectionHolder.data('index');
	collectionHolder.data('index', index + 1);
	newForm = prototype.replace(/__name__/g, index);
	collectionHolder.append(newForm);
	$(".count-td").last().html(index + 1);
}

function removeFormRow(button){
   collectionHolder = $('#form-collection')
   index = collectionHolder.data('index');
   collectionHolder.data('index', index - 1);

   button.parentElement.parentElement.remove();
   
   count = 1;
   $(".count-td").each(function(){
      this.innerHTML = count;
      count++;
   });
}

function toggleAsist(item, idPar){
	itemData = $(item).attr("id").split("-");
    if (itemData[0] == "asistCB"){
       cb = $(item);
       li = $("#asistLI-" + itemData[1]);
    } else if (itemData[0] == "asistLI") {
       cb = $("#asistCB-" + itemData[1]);
       li = $(item);
       cb.prop("checked", !cb.prop("checked"));
    }
    if (cb.prop("checked") == true){
        li.html("<span class=\"glyphicon glyphicon-check\"></span> Asistencia [SI]")
    } else {
        li.html("<span class=\"glyphicon glyphicon-unchecked\"></span> Asistencia [NO]")
    }
	$.ajax({
       type: "post",
	   url: "../asistencia",
	   data: {par: idPar, val: cb.prop("checked")},
	   success: function(data) {
          console.log(data);
	   }
    });
}

function selectEntorno(index){
	$("table").each(function(){
		$(this).addClass("hidden");
	});
	$("#tabla-entorno-" + index).removeClass("hidden");
}

function addAutoComplete(selector, data){
	if (data != null) autoCompleteData = data;
	$(selector).autoComplete({
	    minChars: 1,
	    source: function(term, suggest){
	        term = term.toLowerCase();
	        var choices = autoCompleteData;
	        var matches = [];
	        for (i=0; i<choices.length; i++)
	              if (choices[i].toLowerCase().indexOf(term))
	                    matches.push(choices[i]);
	        suggest(matches);
	    }
    });
}
	
function checkIdAtl(path, id){
	$.getJSON(path + "?id=" + id, function(data, status){
	   if (status == "success"){
	      if (data.success == true){
		     $("#idatl-help").attr("class", "text-info");
	         $("#idatl-help").html("<p style=\"margin-top: 5px\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> " + data.atl + "</p>");
	      } else {
    	     $("#idatl-help").attr("class", "text-danger");
	         $("#idatl-help").html("<p style=\"margin-top: 5px\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> " + data.atl + "</p>");
	      }
	   } else {
	      $("#idatl-help").html("Error");
	   }
	});
}

function clearUsu(){
    $('#atl_usu_nombre').val("");
    $('#atl_usu_contra').val("");
    $('#atl_usu_rol').val("");
    $('#usu_nombre_display').html("<em class=\"text-muted\">Ninguno</em>");
    $('#clear_usu').addClass("hidden");
}

function updateUsuRow(id){
	$.getJSON("./" + id + "/" + "checkusu", function(data, status){
       if (status == "success"){
	      if (data.success == true){
	    	  if (data.usu != null){
	    		  nombreHTML = "<span id=\"nombre_usu\">" + data.usu.nombre + "</span>";
	    		  if (data.usu.rol === "coordinador"){
	    			  nombreHTML = nombreHTML + " <strong class=\"text-info\">Coordinador</strong>";
	    		  }
		    	  $("#usu_nombre_display").html(nombreHTML);
		    	  $("#asig_usu").addClass("hidden");
		    	  $("#del_usu").removeClass("hidden");
		    	  $("#edi_usu").removeClass("hidden");
	    	  } else {
		    	  $("#usu_nombre_display").html("<em class=\"text-muted\">Ninguno</em>");
		    	  $("#asig_usu").removeClass("hidden");
		    	  $("#del_usu").addClass("hidden");
		    	  $("#edi_usu").addClass("hidden");
	    	  } 
	      } else {
	    	  $("#usu_nombre_display").html("No existe el atleta")
	      }
	   } else {
          $("#usu_nombre_display").html("Error");
	   }
	});
}

function toggleIndexBtn(item, label){
	itemData = $(item).attr("id").split("-");
	type = itemData[1];
	id = itemData[2];
	activate = -1;
    if (($(item).hasClass("btn-default")) && (!$(item).hasClass("active"))){ //ON
       $(item).addClass("active");
       activate = 1;
	} else if (!($(item).hasClass("btn-default")) && ($(item).hasClass("active"))) { //OFF
	   $(item).removeClass("active");
	   activate = 0;	
	}
    if (activate == -1) return;
    if (type == "vis"){
       $.getJSON("./competiciones/flags?com=" + id + "&t=vis&v=" + activate, function(data, status){
          ok = false;
          if (status == "success"){
       	     if (data.success == true){
       	     	if (activate == 1){
       	     		html = "<span class=\"glyphicon glyphicon-eye-open\" aria-hidden=\"true\"></span>";
       	     		if (label) html = html + " <strong>SI</strong>";
       	    		$(item).html(html);
       	    		$(item).attr("title", "Visible");
       	    		$(item).tooltip('fixTitle');
       	   	        $(item).removeClass("btn-default");
       	      		$(item).addClass("btn-info");
       	      	} else {
       	     		html = "<span class=\"glyphicon glyphicon-eye-close\" aria-hidden=\"true\"></span>";
       	     		if (label) html = html + " <strong>NO</strong>";
       	      		$(item).html(html);
       	    		$(item).attr("title", "Oculto");
       	    		$(item).tooltip('fixTitle');
       	      		$(item).removeClass("btn-info");
       	      		$(item).addClass("btn-default");       	 
       	       	}
       	     	
       	      	ok = true;
       	     }
          }
          if (!ok){
             if (activate == 1) $(item).removeClass("active");
             else $(item).addClass("active");    	   
          }
       });
    } else if (type == "ins"){
       $.getJSON("./competiciones/flags?com=" + id + "&t=ins&v=" + activate, function(data, status){
    	  ok = false;
          if (status == "success"){
      	     if (data.success == true){
            	if (activate == 1){
       	     		html = "<span class=\"glyphicon glyphicon-thumbs-up\" aria-hidden=\"true\"></span>";
       	     		if (label) html = html + " <strong>Abie.</strong>";
            		$(item).html(html);
       	    		$(item).attr("title", "Inscripciones abiertas");
       	    		$(item).tooltip('fixTitle');
          	        $(item).removeClass("btn-default");
            		$(item).addClass("btn-info");
            	} else {
       	     		html = "<span class=\"glyphicon glyphicon-lock\" aria-hidden=\"true\"></span>";
       	     		if (label) html = html + " <strong>Cerr.</strong>";
            		$(item).html(html);
       	    		$(item).attr("title", "Inscripciones cerradas");
       	    		$(item).tooltip('fixTitle');
            		$(item).removeClass("btn-info");
            		$(item).addClass("btn-default");       	        		
            	}
            	ok = true;
      	     }
          }
          if (!ok){
             if (activate == 1) $(item).removeClass("active");
          	 else $(item).addClass("active");    	   
          }
       });
    }
}

function checkboxGroup(item){
	itemData = $(item).attr("id").split("-");
	type = itemData[0];
	id = itemData[1];
	if (type == "cball"){
		$(".group-"+id).prop('checked', $(item).is(":checked"));
	} else {
		groupData = $(item).attr("class").split("-");
        group = groupData[1];
		$("#cball-"+group).prop('checked', true);
		$(".group-"+group).each(function (){
			if ($(this).is(":checked") == false){
				$("#cball-"+group).prop('checked', false);
				return;
			}
		});
	}
}

var selIns = [];
function selectIns(){
	selIns = [];
	$(".droplist :checkbox").each(function(){
		itemData = $(this).attr("id").split("-");
		id = itemData[1];
		if ($(this).is(":checked")){
			selIns.push(id);			
		}
	});
	$(".paybtn").prop("disabled", (selIns.length == 0));
}

function sendPaidIns(){
	$.ajax({
	   type: "post",
	   url: "./pagos/marcar",
	   data: {selIns: selIns},
	   success: function(data) {
	      console.log(data);
	      location.reload();
	   }
	});
}

function toggleHistView(view){
	btnExten = $('#btn-exten');
	btnCompa = $('#btn-compa');
	divExten = $('#exten-view');
	divCompa = $('#compa-view');
	switch(view){
	   case 'exten': {
		   $(btnExten).removeClass('btn-default');
		   $(btnExten).addClass('btn-info');
		   $(btnCompa).removeClass('btn-info');
		   $(btnCompa).addClass('btn-default');
		   $(btnExten).addClass("active");
		   $(btnCompa).removeClass("active");
		   $(divCompa).css("display", "none");
		   $(divExten).css("display", "");
	   } break;
	   case 'compa': {
		   $(btnCompa).removeClass('btn-default');
		   $(btnCompa).addClass('btn-info');
		   $(btnExten).removeClass('btn-info');
		   $(btnExten).addClass('btn-default');
		   $(btnCompa).addClass("active");
		   $(btnExten).removeClass("active");
		   $(divExten).css("display", "none");
		   $(divCompa).css("display", "");		   
	   } break;
	   default: break;
	}
}

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		  $('abbr').tooltip()
          $('.dropdown-toggle').dropdown()
    });
	$('.droplist').each(function (){
		$(this).data("height", $(this).height());
		$(this).css("height", "0px");
	});
	/*$(window).keydown(function(event){
	   if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
       }
	});*/
	$('input:checkbox').each(function(){
		$(this).prop("checked", this.hasAttribute("checked"));
	})
})

$(window).resize(function(){
   $('.droplist').each(function (){
	  if ($(this).css("height") == "0px") hide = true;
	  else hide = false;
      $(this).css("height", "auto");
      $(this).data("height", $(this).height());
	  if (hide){
         $(this).css("height", "0px");
	  }
	});
})


