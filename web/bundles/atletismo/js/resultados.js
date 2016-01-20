
//PANTALLA DE RESULTADOS

var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		  $('abbr').tooltip()
          $('.dropdown-toggle').dropdown()
          showComs();
    });
})

function showComs(){
	temp = $("#select-temp").val();
	html = ""
	for (i = 0; i < comData[temp].length; i++){
        html = html + "<option value=" + comData[temp][i]['sidCom'] + ">" + comData[temp][i]['nombre'] + "</option>";
	}
    $('#select-com').html(html);
    loadComData();
    loadCartel();
}

function loadCartel(){
	sidCom = $("#select-com").val();
    $("#pic-link").attr("href", "");
    $("#pic-img").attr("src", "");
    $("#pic-img").attr("alt", "...");
	$.ajax({
	    type: "get",
		url: "./resultados/getcartel?com=" + sidCom,
		success: function(data, status) {
	        if (status == "success"){
	            $("#pic-link").attr("href", data['cartel']);
	            $("#pic-img").attr("src", data['cartel']);
	            $("#pic-img").attr("alt", data['nombre']);
	        }
	    }
	});
}

function loadComData(){
	sidCom = $("#select-com").val();
    $("#select-pru").attr("disabled", true);
    $("#select-pru").html("");
    $("#select-cat").attr("disabled", true);
    $("#select-cat").html("");
    $("#select-ron").attr("disabled", true);
    $("#select-ron").html("");
    $("#data-table").html("");
	$.ajax({
	    type: "get",
		url: "./resultados/getpru?com=" + sidCom,
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

function showCats(pru){
    $("#select-ron").attr("disabled", true);
    $("#select-ron").html("");
    $("#data-table").html("");
	if ((pru == null) || (pru == "")){
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

function loadRons(){
	sidPru = $("#select-cat").val();
	$("#data-table").html("");
	if ((sidPru == null) || (sidPru == "")){
        $("#select-ron").html("");
        $("#select-ron").attr("disabled", true);
	} else {
		$.ajax({
		    type: "get",
			url: "./resultados/getron?pru=" + sidPru,
			success: function(data, status) {
		        if (status == "success"){
		            ronData = data['result'];
		            html = "<option value=\"\"> </option>";
		            for (i = 0; i < ronData.length; i++){
		               html = html + "<option value=\"" + ronData[i]['sid'] + "\">Ronda " + ronData[i]['num'];
		               if ((ronData[i]['nombre'] != null) && (ronData[i]['nombre'] != "")){
		            	   html = html + " - " + ronData[i]['nombre'];
		               } else {
		            	   html = html + " (id: " + ronData[i]['id'] + ")";
		               }
		               html = html + "</option>";
		            }
		            $("#select-ron").html(html);
		            $("#select-ron").attr("disabled", false);
		        }
		    }
		});
	} 
}

function getTable(ron){
    $("#data-table").html(loadingIcon);
	if ((ron == null) || (ron == "")){
        $("#data-table").html("");
	} else {
		$.ajax({
		    type: "get",
			url: "./resultados/tabla?ron="+ron,
			success: function(data, status) {
		        if (status == "success"){
		            $("#data-table").html(data);
		  		    $('abbr').tooltip()
		        }
		    }
		});			
	}
}
