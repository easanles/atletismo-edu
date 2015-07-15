

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

$('#modalDialog').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) // Button that triggered the modal
	  var data1 = button.data('data1')
	  var data2 = button.data('data2')
	  var data3 = button.data('data3')
	  var modal = $(this)
	  modal.find('#data1').text(data1)
	  modal.find('#data2').text(data2)
	  modal.find('#confirmbutton').attr("href", "./competiciones/borrar?i=" + data3)
	})