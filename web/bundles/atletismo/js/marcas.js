
//FORMULARIO DE REGISTRO DE MARCAS

function loadPru(com){
    $.get("./marcas/getpru?com="+com, function(data, status){           
        if (status == "success"){
           $("#select-pru").html(data);
           $("#select-pru").attr("disabled", false);
        } else {
           $("#select-pru").html("Error al cargar datos");
        }
     }); 
}


$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		  $('abbr').tooltip()
          $('.dropdown-toggle').dropdown()
    });
	if ($("#select-com").selectedIndex != 0){
		loadPru($("#select-com").find(":selected").attr("value"));
	}
})
