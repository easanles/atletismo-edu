
alerthtml_pre = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> <span>";
alerthtml_pos = "</span></div>";
loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\" aria-hidden=\"true\"></span>"

//**********PORTADA***********	
	
function changeTemp(temp){
	window.location.href = "./mis-competiciones?temp=" + temp;
}

function toggleView(view){
	btnGrid = $('#btn-grid');
	btnList = $('#btn-list');
	divGrid = $('#coms-grid');
	divList = $('#coms-list');
	switch(view){
	   case 'grid': {
		   $(btnGrid).removeClass('btn-default');
		   $(btnGrid).addClass('btn-info');
		   $(btnList).removeClass('btn-info');
		   $(btnList).addClass('btn-default');
		   $(btnGrid).addClass("active");
		   $(btnList).removeClass("active");
		   $(divList).css("display", "none");
		   $(divGrid).css("display", "");
	   } break;
	   case 'list': {
		   $(btnList).removeClass('btn-default');
		   $(btnList).addClass('btn-info');
		   $(btnGrid).removeClass('btn-info');
		   $(btnGrid).addClass('btn-default');
		   $(btnList).addClass("active");
		   $(btnGrid).removeClass("active");
		   $(divGrid).css("display", "none");
		   $(divList).css("display", "");		   
	   } break;
	   default: break;
	}
}


//**********INSCRIPCIONES**********

function toggleIns(item){
	itemData = $(item).attr("id").split("-");
	id = itemData[2];
	activate = -1;
    if (($(item).hasClass("btn-default")) && (!$(item).hasClass("active"))){ //ON
       $(item).addClass("active");
       url = "../inscribirprueba?pru="+id
       $(item).html(loadingIcon);
       activate = 1;
 	} else if (!($(item).hasClass("btn-default")) && ($(item).hasClass("active"))) { //OFF
 	   $(item).removeClass("active");
       url = "../desinscribirprueba?pru="+id
       $(item).html(loadingIcon);
 	   activate = 0;
 	}
    if (activate == -1) return;
    $.getJSON(url, function(data, status){
        ok = false;
        if (status == "success"){
     	    if (data.success == true){
     	     	if (activate == 1){
     	    		$(item).html("Inscrito");
     	   	        $(item).removeClass("btn-default");
     	      		$(item).addClass("btn-info");
     	      		$("#btn-int-"+id).attr("disabled", false);
     	      	} else {
     	      		$(item).html("Inscribirse");
     	      		$(item).removeClass("btn-info");
     	      		$(item).addClass("btn-default");       	 
     	      		$("#btn-int-"+id).attr("disabled", true);
     	       	}
     	    } else {
     	       $(item).removeClass("btn-default btn-info");
	      	   $(item).addClass("btn-danger");
	      	   $(item).attr("disabled", true);
     	       $(item).html("<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>");
	           $("#alert-div").append(alerthtml_pre + data.message + alerthtml_pos);
     	    }
     	    ok = true;
        }
        if (!ok){
            if (activate == 1) $(item).removeClass("active");
            else $(item).addClass("active");    	   
        }
    });
}

//***********MARCAS***********

function loadRonsTable(){
	pru = $('#select-pru').val();
	$("#select-ron").html(loadingIcon);
    $.get("../rondas?pru="+ pru, function(data, status){
        if (status == "success"){
           $("#select-ron").html(data);
 		   $('abbr').tooltip()
        } else {
           $("#select-ron").html("Error al cargar datos");
        }
    }); 
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

//*********MODALES*********

function showModal(type, data1, data2){
	var modal = $('#modalDialog');
	switch(type){
	   case ("inscrib"): {
    	   $('#dialog-label').html("Inscripción");
    	   $("#dialog-body").html("¿Quieres inscribirte en <strong>" + data1 + "</strong>?");
		   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-primary\" onClick=\"submitDialogAction('./mis-competiciones/inscribirprueba?com=" + data2 + "')\"><span class=\"glyphicon glyphicon-ok\"></span> Inscribirse</button>");           
	   } break;
	   case ("desinscrib"): {
    	   $('#dialog-label').html("Eliminar inscripción");
    	   $("#dialog-body").html("¿Quieres desinscribirte de <strong>" + data1 + "</strong>?");
		   $("#dialog-btn").html("<button type=\"button\" class=\"btn btn-danger\" onClick=\"submitDialogAction('./mis-competiciones/desinscribirprueba?com=" + data2 + "')\"><span class=\"glyphicon glyphicon-remove\"></span> Desinscribirse</button>");           
	   } break;
	   case ("marca"): {
    	   $('#dialog-label').html("Registrar marcas");
    	   $('#dialog-btn').html("<button class=\"btn btn-primary\" onClick=\"submitDialogForm()\"><span class=\"glyphicon glyphicon-save\"></span> Guardar</button>");
    	   $("#dialog-body").html("<span class=\"glyphicon glyphicon-refresh spinning pull-center\"></span>");   
    	   $.getJSON("../marcas/nuevo?ron=" + data1, function(data, status){
   		      if (status == "success"){
   		    	 if (data.preErr == false){
   	   			     $("#dialog-body").html(data.message);
   	   			     $('abbr').tooltip()
   	    		     collectionHolder = $('#form-collection');
   	 		         collectionHolder.data('index', collectionHolder.find('.subform-row').length);
   	 		         if (data2 == true) checkIntentos();
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
   		    		$("#dialog-body").html("<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> " + data.message + "</div>")
   		    		$('#dialog-btn button').attr("disabled", true);
   		    	 }
   	          } else {
   			     $("#dialog-body").html("Error al cargar datos");
   			  }	
   	       });
       } break;
	   case ("intentos"): {
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

	   default: break;
	}
	modal.modal();
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
	$.ajax({
	   type        : "GET",
	   url         : url,
	   success     : function(data) {
          if (data.success == false){
		     $('#dialog-body').html(alerthtml_pre + data.message + alerthtml_pos);
		     $("#dialog-btn button").attr("disabled", true);
	      } else if (data.success == true){
			 $("#modal-dismiss").click();
			 location.reload();
	      }
	   }
	});
}

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
    });
})
