
//FORMULARIO DE REGISTRO DE MARCAS

var pruData = null;
var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";


function loadPru(com){
	if ((com == null) || com == ""){
        $("#select-pru").html("");
        $("#select-pru").attr("disabled", true);		
	} else {
		$.ajax({
		    type: "get",
			url: "./marcas/getpru?com="+com,
			success: function(data, status) {
		        if (status == "success"){
		            pruData = data['result'];
		            html = "<option value=\"\"> </option>";
		            for (i = 0; i < pruData.length; i++){
		               html = html + "<option value=\"" + i + "\">" + pruData[i]['tprm'] + "</option>";
		            }
		            $("#select-pru").html(html);
		            $("#select-pru").attr("disabled", false);
		        }
		    }
		});	
	}	
}

function showCats(pru){
	if ((pru == null) || pru == ""){
        $("#select-cat").html("");
        $("#select-cat").attr("disabled", true);		
	} else {
        html = "<option value=\"\"> </option>";
        for (i = 0; i < pruData[pru]['cats'].length; i++){
           html = html + "<option value=\"" + pruData[pru]['cats'][i]['sid'] + "\">" + pruData[pru]['cats'][i]['nombre'] + "</option>";
        }
        $("#select-cat").html(html);
        $("#select-cat").attr("disabled", false);		
	}
}

function loadAtls(pru){
    $("#select-atl").html(loadingIcon);	
	$.ajax({
	    type: "get",
		url: "./marcas/getatl?pru="+pru,
		success: function(data, status) {
	        if (status == "success"){
	            $("#select-atl").html(data);
	        }
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
