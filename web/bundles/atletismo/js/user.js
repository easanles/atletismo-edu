

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
	   default: break;
	}
	modal.modal();
}

function submitDialogAction(url){
	alerthtml_pre = "<div class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-remove\"></span> <span>";
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
