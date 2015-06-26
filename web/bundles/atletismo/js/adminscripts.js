/**
 * 
 */

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

});