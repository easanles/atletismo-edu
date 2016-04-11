
//PANTALLA DE RESULTADOS

var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		  $('abbr').tooltip()
          $('.dropdown-toggle').dropdown()
          showComs();
    });
})

function showComs(){
	temp = $("#select-temp").val();
	html = ""
	for (i = 0; i < comData[temp].length; i++){
        html = html + "<option value=" + comData[temp][i]['sidCom'] + ">" + comData[temp][i]['nombre'] + "</option>";
	}
    $('#select-com').html(html);
    
	if ((typeof autoSelectCom !== 'undefined') && (autoSelectCom != null)){
		$('#select-com').val(autoSelectCom);
		autoSelectCom = null;
	}
    if ($("#select-pru").length){
    	loadComData();
    } else {
    	getAsistTable($("#select-com").val());
    }
    loadCartel();
}

function loadCartel(){
	sidCom = $("#select-com").val();
    $("#pic-link").attr("href", "#");
    $("#pic-img").attr("src", "");
    $("#pic-img").attr("alt", "...");
	$.ajax({
	    type: "get",
		url: "./resultados/getcartel?com=" + sidCom,
		success: function(data, status) {
	        if (status == "success"){
	        	if (data['cartel'] == null){
	        		$('#pic-div').css("display", "none");
	        		$('#no-pic-div').css("display", "block");
	        	} else {
		            $("#pic-link").attr("href", data['cartel']);
		            $("#pic-img").attr("src", data['cartel']);
		            $("#pic-img").attr("alt", data['nombre']);
	        		$('#pic-div').css("display", "block");
	        		$('#no-pic-div').css("display", "none");
	        	}

	        }
	    }
	});
}

function loadComData(){
	sidCom = $("#select-com").val();
    $("#select-pru").attr("disabled", true);
    $("#select-pru").html("");
    $("#select-cat").attr("disabled", true);
    $("#select-cat").html("");
    $("#select-ron").attr("disabled", true);
    $("#select-ron").html("");
    $("#data-table").html("");
	$.ajax({
	    type: "get",
		url: "./resultados/getpru?com=" + sidCom,
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
	    			loadRons();
	    			autoSelectPru = null;
	    		}
	        }
	    }
	});
}

function showCats(pru){
    $("#select-ron").attr("disabled", true);
    $("#select-ron").html("");
    $("#data-table").html("");
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

function loadRons(){
	sidPru = $("#select-cat").val();
	$("#data-table").html("");
	if ((sidPru == null) || (sidPru === "")){
        $("#select-ron").html("");
        $("#select-ron").attr("disabled", true);
	} else {
		$.ajax({
		    type: "get",
			url: "./resultados/getron?pru=" + sidPru,
			success: function(data, status) {
		        if (status == "success"){
		            ronData = data['result'];
		            html = "<option value=\"\"> </option>";
		            for (i = 0; i < ronData.length; i++){
		               html = html + "<option value=\"" + ronData[i]['sid'] + "\">Ronda " + ronData[i]['num'];
		               if ((ronData[i]['nombre'] != null) && (ronData[i]['nombre'] != "")){
		            	   html = html + " - " + ronData[i]['nombre'];
		               } else {
		            	   html = html + " (id: " + ronData[i]['id'] + ")";
		               }
		               html = html + "</option>";
		            }
		            $("#select-ron").html(html);
		            $("#select-ron").attr("disabled", false);
		            
		    		if ((typeof autoSelectRon !== 'undefined') && (autoSelectRon != null)){
		    			$('#select-ron').val(autoSelectRon);
		    			getTable(autoSelectRon);
		    			autoSelectRon = null;
		    		}
		        }
		    }
		});
	} 
}

function getTable(ron){
    $("#data-table").html(loadingIcon);
	if ((ron == null) || (ron === "")){
        $("#data-table").html("");
	} else {
		$.ajax({
		    type: "get",
			url: "./resultados/tabla?ron="+ron,
			success: function(data, status) {
		        if (status == "success"){
		            $("#data-table").html(data);
		  		    $('abbr').tooltip()
		  		    $('[data-toggle="tooltip"]').tooltip()
		        }
		    }
		});			
	}
}

//Exclusivo de informes de asistencia
function getAsistTable(com){
    $("#data-tables").html(loadingIcon);
	if ((com == null) || (com === "")){
        $("#data-tables").html("");
	} else {
		$.ajax({
		    type: "get",
			url: "./asistencia/participaciones?com="+com,
			success: function(data, status) {
		        if (status == "success"){
		            $("#data-tables").html(data);
		            $("#atl-asist").html($(".row-par .btn-info").length);		            
		        }
		    }
		});			
	}
}

function togglePar(item){
	itemData = $(item).attr("id").split("-");
	id = itemData[2];
	activate = -1;
    if (($(item).hasClass("btn-default")) && (!$(item).hasClass("active"))){ //ON
       $(item).addClass("active");
       val = true;
       $(item).html(loadingIcon);
       activate = 1;
 	} else if (!($(item).hasClass("btn-default")) && ($(item).hasClass("active"))) { //OFF
 	   $(item).removeClass("active");
       val = false;
       $(item).html(loadingIcon);
 	   activate = 0;
 	}
    if (activate == -1) return;
	$.ajax({
	      type: "post",
		  url: "../competiciones/asistencia",
		  data: {par: id, val: val},
		  success: function() {
    	     if (activate == 1){
     	        $(item).html("<span class=\"glyphicon glyphicon-check\"></span> <strong>SI</strong>");
     	   	    $(item).removeClass("btn-default");
     	        $(item).addClass("btn-info");
     	     } else {
     	        $(item).html("<span class=\"glyphicon glyphicon-unchecked\"></span> <strong>NO</strong>");
     	      	$(item).removeClass("btn-info");
     	      	$(item).addClass("btn-default");       	 
     	     }
	         $("#atl-asist").html($(".row-par .btn-info").length);
		  },
		  error: function(data){
	         if (activate == 1) $(item).removeClass("active");
	         else $(item).addClass("active");    	   
		  }
	});
}


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


