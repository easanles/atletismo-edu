

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

function pruSearch(id, tpr, cat){
	path = "./" + id;
	if ((tpr != null) && (tpr != "")){
		if ((cat != null) && (cat != "")){
			path = path + "?tpr=" + tpr + "&c=" + cat;
		} else {
			path = path + "?tpr=" + tpr;
		}
	} else {
		if ((cat != null) && (cat != "")) {
			path = path + "?c=" + cat;
		}
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

function submitDialogForm(){	
	  var values = {};
	  form = $('form');
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
	    	   $("#modal-dismiss").click();
	    	   $(".updater").click();
	    	}
	    }
	  });
}
	
$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
          $('.dropdown-toggle').dropdown()
    });
})