

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

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
    });
})
