
function toggleContent(tab){   
    $("#config-nav li").removeClass("active");
    $(".tabcontent").css("display", "none");
    
    $("#navtab-" + tab).addClass("active");
    $("#tabcontent-" + tab).css("display", "inline");
    if ((tab == "usu") ||(tab == "tp") || (tab == "cat")){
    	loadViews(tab);
    }
}

function loadViews(content){
   icon = $(".updater");
   icon.removeClass("hidden");

   switch (content){
      case "usu": {
         $.get("./configuracion/usuario", function(data, status){
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
   $("#btn_poblarbd").click(function(){
      icon = $(this).find("span");
      icon.addClass("spinning");
         sendAction("./configuracion/poblarbd", icon);
   });

    $("#btn_borrarbd").click(function(){
       icon = $(this).find("span");
      icon.addClass("spinning");
       sendAction("./configuracion/borrarbd", icon);
    });
   
    $("#btn_rehacerbd").click(function(){
       icon = $(this).find("span");
      icon.addClass("spinning");
       sendAction("./configuracion/rehacerbd", icon);
    });
    
    $("#btn_limpiarcache").click(function(){
       icon = $(this).find("span");
      icon.addClass("spinning");
       sendAction("./configuracion/limpiarcache", icon);
    });
    
    $("#btn_asseticdump").click(function(){
       //icon = $(this).find("span");
      //icon.addClass("spinning");
      //sendAction("./configuracion/asseticdump", icon);
    });
});
