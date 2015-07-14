

function goToUrl(path){
	window.location.href = path;
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