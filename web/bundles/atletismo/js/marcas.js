
//FORMULARIO DE REGISTRO DE MARCAS

var pruData = null;
var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";
var selectedPru = null;


function loadPru(com){
    $("#select-cat").html("");
    $("#select-cat").attr("disabled", true);		
    $("#select-atl").html("");	
    selectedPru = null;
    $("#select-ron").html("");	

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
    $("#select-atl").html("");
    selectedPru = null;
    $("#select-ron").html("");	

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
    $("#select-ron").html("");
    $("#select-atl").html(loadingIcon);
	$.ajax({
	    type: "get",
		url: "./marcas/getatl?pru="+pru,
		success: function(data, status) {
	        if (status == "success"){
	            $("#select-atl").html(data);
	            selectedPru = pru;
	        }
	    }
	});	
}

function loadRons(){
	item = $('.btn-info')[0];
	data = item.id.split("-");
	type = data[1];
	idAtl = data[2];
	$("#select-ron").html(loadingIcon);
    $.get("./marcas/getron?pru="+ selectedPru + "&atl=" + idAtl, function(data, status){
        if (status == "success"){
           $("#select-ron").html(data);
        } else {
           $("#select-ron").html("Error al cargar datos");
        }
     }); 
}

function toggleRadioButton(item){
	if ($(item).hasClass("btn-default")){
		$(".sel-atl").each(function(){
			$(this).removeClass("btn-info");
			$(this).removeClass("updater");
			$(this).addClass("btn-default");
			$(this).attr("aria-pressed", false);
			$(this).parent().closest("tr").attr("class", "");
		})
		$(item).removeClass("btn-default");
		$(item).addClass("btn-info");
		$(item).addClass("updater");
		$(item).attr("aria-pressed", true);
		$(item).parent().closest("tr").attr("class", "info");
	}
	loadRons();
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
