/**
 * 
 */


function toggleContent(tab){
    $("#navtab-tp").removeClass("active");
    $("#tabcontent-tp").css("display", "none");
    $("#navtab-oa").removeClass("active");
    $("#tabcontent-oa").css("display", "none");
	switch (tab){
	   case ('tp'): {
		    $("#navtab-tp").addClass("active");
		    $("#tabcontent-tp").css("display", "inline");
	   }; break;
       case ('oa'): {
			$("#navtab-oa").addClass("active");
			$("#tabcontent-oa").css("display", "inline");
	   }; break;
	   default:;
	}
}

alerthtml_preok = "<div class=\"alert alert-info alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <strong>Hecho: </strong><span>";
alerthtml_preerr = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <strong>Error: </strong><span>";
alerthtml_pos = "</span></div>";


function sendAction(path, icon){
   $.getJSON(path, function(data, status){
	     if (status = "success"){
	    	 if (data.success == true){
				 $("#alert_div").append(alerthtml_preok + data.message + alerthtml_pos);
	    	 } else {
				 $("#alert_div").append(alerthtml_preerr + data.message + alerthtml_pos);
	    	 }
	     } else {
			 $("#alert_div").append(alerthtml_preerr + 'ERROR' + alerthtml_pos);
	     }
		 icon.removeClass("spinning");
   });
}

function loadViews(){
	icon = $("#btn_tprf-refresh").find("span");
	icon.addClass("spinning");
	
	$.get("./tipoprueba", function(data, status){
		if (status = "success"){
			 $("#tprf-table").html(data);
		} else {
			 $("#tabcontent-tp").html("Error al cargar datos");
		}
		
    });
	
    icon.removeClass("spinning");
}

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
    })
	
	$("#btn_poblarbd").click(function(){
	   icon = $(this).find("span");
	   icon.addClass("spinning");
   	   sendAction("./poblarbd", icon);
	});

    $("#btn_borrarbd").click(function(){
 	   icon = $(this).find("span");
	   icon.addClass("spinning");
    	sendAction("./borrarbd", icon);
    });
	
    $("#btn_rehacerbd").click(function(){
 	   icon = $(this).find("span");
	   icon.addClass("spinning");
    	sendAction("./rehacerbd", icon);
    });
    
    $("#btn_limpiarcache").click(function(){
 	   icon = $(this).find("span");
	   icon.addClass("spinning");
    	sendAction("./limpiarcache", icon);
    });
    
    $("#btn_asseticdump").click(function(){
 	   //icon = $(this).find("span");
	   //icon.addClass("spinning");
	   //sendAction("./asseticdump", icon);
    });
    
    loadViews();

});