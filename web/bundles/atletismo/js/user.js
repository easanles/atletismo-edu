
alerthtml_pre = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> <span>";
alerthtml_pos = "</span></div>";

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
	   default: break;
	}
	modal.modal();
}

function toggleIns(item){
	itemData = $(item).attr("id").split("-");
	id = itemData[2];
	activate = -1;
    if (($(item).hasClass("btn-default")) && (!$(item).hasClass("active"))){ //ON
       $(item).addClass("active");
       url = "../inscribirprueba?pru="+id
       $(item).html("<span class=\"glyphicon glyphicon-refresh spinning\" aria-hidden=\"true\"></span>");
       activate = 1;
 	} else if (!($(item).hasClass("btn-default")) && ($(item).hasClass("active"))) { //OFF
 	   $(item).removeClass("active");
       url = "../desinscribirprueba?pru="+id
       $(item).html("<span class=\"glyphicon glyphicon-refresh spinning\" aria-hidden=\"true\"></span>");
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
