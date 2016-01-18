
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
		    			loadAtls(autoSelectPru);
		    			autoSelectPru = null;
		    		}
		        }
		    }
		});	
	}	
}

function showCats(pru){
    $("#select-atl").html("");
    selectedPru = null;
    $("#select-ron").html("");	

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
    			if ((typeof autoSelectAtl !== 'undefined') && (autoSelectAtl != null)){
    				toggleRadioButton($('#sel-atl-' + autoSelectAtl));
    				autoSelectAtl = null;
    			}
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

function checkIntentos(){
	button = $("#btn-addInt");
	button.prop("disabled", false);
	maxNumInt = parseInt($('#max-num-int').html());
	inputGroups = $('.input-group');
	$('.has-success').removeClass("has-success");
	checkboxes = $('input:checkbox');
	countInvalidos = 0;
	for (i = 0; i < checkboxes.length; i++){
		if ($(checkboxes[i]).is(":checked")){
			$('.has-success').removeClass("has-success");
			$(inputGroups[i]).addClass("has-success");
			countInvalidos = 0;
		} else countInvalidos++;
		if (countInvalidos >= maxNumInt){
			button.prop("disabled", true);
			break;
		}
	}
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
