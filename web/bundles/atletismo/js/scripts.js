

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
	    	   $("#btn_tprf-refresh").click();
	    	}
	    }
	  });
}

/*function showModal(data1, data2, okbtn, action){
	var modal = $('#modalDialog');
	modal.find('#data1').text(data1);
	modal.find('#data2').text(data2);
	modal.find('#confirmbutton').attr("href", okbtn);
	modal.modal();
	
    if (action != null){
	  $.getJSON(action, function(data, status){
	     if (status = "success"){
		   $("#dialog-body").html(data.message);
         } else {
		    $("#dialog-body").html("Error al cargar datos");
		 }	
      });
   }
}*/

/*$('#modalDialog').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var data1 = button.data('data1')
	  var data2 = button.data('data2')
	  var okbtn = button.data('okbtn')
	  var modal = $(this)
	  modal.find('#data1').text(data1)
	  modal.find('#data2').text(data2)
	  modal.find('#confirmbutton').attr("href", okbtn)

	  var action = button.data('action')
	  if ((typeof action != "undefined") && (action != "")){
	     $.getJSON(action, function(data, status){
		    if (status = "success"){
			   $("#dialog-body").html(data.message);
			} else {
			   $("#dialog-body").html("Error al cargar datos");
			}	
         });
	  }
})*/
	
$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
    });
})