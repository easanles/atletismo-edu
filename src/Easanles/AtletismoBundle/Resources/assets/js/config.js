
var usuPath = "./configuracion/usuario";

function toggleContent(tab){   
    $("#config-nav li").removeClass("active");
    $(".tabcontent").css("display", "none");
    
    $("#navtab-" + tab).addClass("active");
    $("#tabcontent-" + tab).css("display", "inline");
    $(".updater").attr("disabled", true);
    if ((tab == "usu") ||(tab == "tp") || (tab == "cat")){
        $("#updater-"+tab).attr("disabled", false);
    	loadViews(tab);
    }
}

function loadViews(content){
   icon = $(".updater");
   icon.removeClass("hidden");

   switch (content){
      case "usu": {
         $.get(usuPath, function(data, status){
            if (status == "success"){
               $("#usu-table").html(data);
       		   $('[data-toggle="tooltip"]').tooltip()
               $('abbr').tooltip()
       		   $('.dropdown-toggle').dropdown()
             } else {
                $("#tabcontent-usu").html("Error al cargar datos");
             }
             icon.addClass("hidden");      
         });      
      } break;
      case "tp": {
        $.get("./configuracion/tipoprueba", function(data, status){
           if (status == "success"){
              $("#tpr-table").html(data);
          	  $('.droplist').each(function (){
        		$(this).data("height", $(this).height());
        		$(this).css("height", "0px");
        	  });
      		  $('[data-toggle="tooltip"]').tooltip()
              $('abbr').tooltip()
    		  $('.dropdown-toggle').dropdown()
           } else {
              $("#tabcontent-tp").html("Error al cargar datos");
           }
           icon.addClass("hidden");      
        });      
      } break;
      case "cat": {
    	 route = routeCatData($("#cat-showOutdated").is(':checked'));
         $.get(route, function(data, status){
            if (status == "success"){
                $("#cat-table").html(data);
                $('[data-toggle="tooltip"]').tooltip()
                $('abbr').tooltip()
                $('.dropdown-toggle').dropdown()
             } else {
                $("#tabcontent-cat").html("Error al cargar datos");
            }
            icon.addClass("hidden");      
          });break;
         
      } break;
      default: break;
   }
}

//USUARIOS
function usuSearch(str){
	if ((str != null) && (str != "")) {
	   usuPath = "./configuracion/usuario?q=" + str;
	} else {
	   usuPath = "./configuracion/usuario";
	}
	loadViews("usu");
}


//CATEGORIAS
function routeCatData(outdated){
	if (outdated == true) return "./configuracion/categoria?outd=true";
	else return "./configuracion/categoria?outd=false";
}


//AJUSTES
function changeSettings(){
	  icon = $("#ajSendBtn").find("span");
      icon.removeClass("glyphicon-save");
      icon.addClass("glyphicon-refresh spinning");
	  var values = {};
	  form = $('#cfg-form');
	  $.each( form.serializeArray(), function(i, field) {
	    values[field.name] = field.value;
	  });
	  $.ajax({
	    type        : form.attr( 'method' ),
	    url         : form.attr( 'action' ),
	    data        : values,
	    success     : function(data) {
	  	    $('#tabcontent-aj').html(data.message);
	  	    $("#cfg_jumbotron").click(function(){
	  		   $("#cfg_jumbolin1").prop("disabled", !$(this).is(":checked"));
	  		   $("#cfg_jumbolin2").prop("disabled", !$(this).is(":checked"));
	  	    });	
	        icon.removeClass("glyphicon-refresh spinning");
	        icon.addClass("glyphicon-save");
	    }
	  });
}

//COMANDOS
function sendAction(path, icon){
	   alerthtml_preok = "<div class=\"alert alert-info alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <strong>Hecho: </strong><span>";
	   alerthtml_preerr = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <strong>Error: </strong><span>";
	   alerthtml_pos = "</span></div>";
	   $.getJSON(path, function(data, status){
	        if (status == "success"){
	           if (data.success == true){
	             $("#alert-div-comm").append(alerthtml_preok + data.message + alerthtml_pos);
	           } else {
	             $("#alert-div-comm").append(alerthtml_preerr + data.message + alerthtml_pos);
	           }
	        } else {
	          $("#alert-div-comm").append(alerthtml_preerr + 'ERROR' + alerthtml_pos);
	        }
	       icon.removeClass("spinning");
	   });
	}


$(document).ready(function(){
   $("#cfg_jumbotron").click(function(){
	   $("#cfg_jumbolin1").prop("disabled", !$(this).is(":checked"));
	   $("#cfg_jumbolin2").prop("disabled", !$(this).is(":checked"));
   });	

   $("#btn_poblarbd").click(function(){
	  if (confirm("¿Rellenar la base de datos con datos de prueba?")){
	      icon = $(this).find("span");
	      icon.addClass("spinning");
	      sendAction("./configuracion/poblarbd", icon);		  
	  }
   });

   $("#btn_borrarbd").click(function(){
	  if (confirm("¿Borrar todos los datos de la base de datos?\n" +
	  		"¡ATENCIÓN!: Esta operación no se puede deshacer\n" +
	  		"Se creará el usuario por defecto \"admin\" con la contraseña \"adminpass\"")){
         icon = $(this).find("span");
         icon.addClass("spinning");
         sendAction("./configuracion/borrarbd", icon);
         location.reload();
	  }
   });
   
   $("#btn_rehacerbd").click(function(){
      if (confirm("¿Destruir y rehacer la base de datos?\n" +
      		"¡ATENCIÓN!: Esta operación borrará todos los datos almacenados actualmente y no se puede deshacer\n" + 
      		"Se creará el usuario por defecto \"admin\" con la contraseña \"adminpass\"")){
         icon = $(this).find("span");
         icon.addClass("spinning");
         sendAction("./configuracion/rehacerbd", icon);
         location.reload();
      }
   });
    
   $("#btn_limpiarcache").click(function(){
      if (confirm("¿Confirmar la operación de borrado de la cache? (cache:clear)")){
         icon = $(this).find("span");
         icon.addClass("spinning");
         sendAction("./configuracion/limpiarcache", icon);
      }
   });
    
   $("#btn_asseticdump").click(function(){
      if (confirm("¿Confirmar la operación assetic:dump?")){
         icon = $(this).find("span");
         icon.addClass("spinning");
         sendAction("./configuracion/asseticdump", icon);
      }
   });
});
