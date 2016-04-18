
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
    
	if ((typeof autoSelectCom !== 'undefined') && (autoSelectCom != null)){
		$('#select-com').val(autoSelectCom);
		autoSelectCom = null;
	}
    if ($("#select-pru").length){
    	loadComData();
    } else {
    	getAsistTable($("#select-com").val());
    }
    loadCartel();
}

function loadCartel(){
	sidCom = $("#select-com").val();
    $("#pic-link").attr("href", "#");
    $("#pic-img").attr("src", "");
    $("#pic-img").attr("alt", "...");
	$.ajax({
	    type: "get",
		url: "./resultados/getcartel?com=" + sidCom,
		success: function(data, status) {
	        if (status == "success"){
	        	if (data['cartel'] == null){
	        		$('#pic-div').css("display", "none");
	        		$('#no-pic-div').css("display", "block");
	        	} else {
		            $("#pic-link").attr("href", data['cartel']);
		            $("#pic-img").attr("src", data['cartel']);
		            $("#pic-img").attr("alt", data['nombre']);
	        		$('#pic-div').css("display", "block");
	        		$('#no-pic-div').css("display", "none");
	        	}

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
	            
	    		if ((typeof autoSelectPru !== 'undefined') && (autoSelectPru != null)){
	    			ok = false;
	    			for(i = 0; i < pruData.length; i++){
	    				for(j = 0; j < pruData[i]['cats'].length; j++ ){
	    					if (pruData[i]['cats'][j]['sid'] == autoSelectPru){
	    						$('#select-pru').val(i);
	    						showCats(i);
	    						$('#select-cat').val(autoSelectPru);
	    						ok = true;
	    						break;
	    					}
	    				}
	    				if (ok) break;
	    			}
	    			loadRons();
	    			autoSelectPru = null;
	    		}
	        }
	    }
	});
}

function showCats(pru){
    $("#select-ron").attr("disabled", true);
    $("#select-ron").html("");
    $("#data-table").html("");
	if ((pru == null) || (pru === "")){
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
	if ((sidPru == null) || (sidPru === "")){
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
		            
		    		if ((typeof autoSelectRon !== 'undefined') && (autoSelectRon != null)){
		    			$('#select-ron').val(autoSelectRon);
		    			getTable(autoSelectRon);
		    			autoSelectRon = null;
		    		}
		        }
		    }
		});
	} 
}

function getTable(ron){
    $("#data-table").html(loadingIcon);
	if ((ron == null) || (ron === "")){
        $("#data-table").html("");
	} else {
		$.ajax({
		    type: "get",
			url: "./resultados/tabla?ron="+ron,
			success: function(data, status) {
		        if (status == "success"){
		            $("#data-table").html(data);
		  		    $('abbr').tooltip()
		  		    $('[data-toggle="tooltip"]').tooltip()
		        }
		    }
		});			
	}
}

//Exclusivo de informes de asistencia
function getAsistTable(com){
    $("#data-tables").html(loadingIcon);
	if ((com == null) || (com === "")){
        $("#data-tables").html("");
	} else {
		$.ajax({
		    type: "get",
			url: "./asistencia/participaciones?com="+com,
			success: function(data, status) {
		        if (status == "success"){
		            $("#data-tables").html(data);
		            $("#atl-asist").html($(".row-par .btn-info").length);		            
		        }
		    }
		});			
	}
}

function togglePar(item){
	itemData = $(item).attr("id").split("-");
	id = itemData[2];
	activate = -1;
    if (($(item).hasClass("btn-default")) && (!$(item).hasClass("active"))){ //ON
       $(item).addClass("active");
       val = true;
       $(item).html(loadingIcon);
       activate = 1;
 	} else if (!($(item).hasClass("btn-default")) && ($(item).hasClass("active"))) { //OFF
 	   $(item).removeClass("active");
       val = false;
       $(item).html(loadingIcon);
 	   activate = 0;
 	}
    if (activate == -1) return;
	$.ajax({
	      type: "post",
		  url: "../competiciones/asistencia",
		  data: {par: id, val: val},
		  success: function() {
    	     if (activate == 1){
     	        $(item).html("<span class=\"glyphicon glyphicon-check\"></span> <strong>SI</strong>");
     	   	    $(item).removeClass("btn-default");
     	        $(item).addClass("btn-info");
     	     } else {
     	        $(item).html("<span class=\"glyphicon glyphicon-unchecked\"></span> <strong>NO</strong>");
     	      	$(item).removeClass("btn-info");
     	      	$(item).addClass("btn-default");       	 
     	     }
	         $("#atl-asist").html($(".row-par .btn-info").length);
		  },
		  error: function(data){
	         if (activate == 1) $(item).removeClass("active");
	         else $(item).addClass("active");    	   
		  }
	});
}
