
//FORMULARIO DE INSCRIPCION

var selAtl = [];
var btnON  = "<span class=\"glyphicon glyphicon-check\"></span> <strong>SI</strong>";
var btnOFF = "<span class=\"glyphicon glyphicon-unchecked\"></span> NO";
var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";
var idAtlPruList = null;
var selPru = [];


function toggleInsPages(tab){
	if (!$("#inspill-" + tab).hasClass('disabled')){
       $(".nav-pills li").removeClass("active");
       if (tab != "confirm"){
           $("#inspill-confirm").addClass("disabled");
       }
       $(".pagecontent").css("display", "none");
       $(".pagecontent").html("");
       $("#inspill-" + tab).addClass("active");
       $("#inspage-" + tab).css("display", "inline");
       checkPillStatus();
       loadViews(tab);
	}
}

function nextPill(){
	if ($("#inspill-atl").hasClass("active")){
		$('#inspill-pru a').click();
	} else if ($("#inspill-pru").hasClass("active")){
		$('#inspill-confirm a').click();
	}
}

function checkPillStatus(){
	if ($("#inspill-atl").hasClass('active')){
		if (selAtl.length == 0){
			$("#inspill-pru").addClass("disabled");
			$("#inspill-confirm").addClass("disabled");
            $("#btn-next").attr("disabled", true);		
		} else {
			$("#inspill-pru").removeClass("disabled");
		    $("#btn-next").attr("disabled", false);
		    if (selPru.length != 0){
				$("#inspill-confirm").removeClass("disabled");
		    }
		}
	} else if ($("#inspill-pru").hasClass('active')){
		if (selPru.length == 0){
			$("#inspill-confirm").addClass("disabled");
            $("#btn-next").attr("disabled", true);
		} else {
			$("#inspill-confirm").removeClass("disabled");
		    $("#btn-next").attr("disabled", false);
		}
	} 
	if ($("#inspill-confirm").hasClass('active')){
        $("#btn-next").attr("disabled", false);
        $("#btn-next").html("<span class=\"glyphicon glyphicon-save\"></span> Inscribir");
	} else {
        $("#btn-next").html("Siguiente <span class=\"glyphicon glyphicon-arrow-right\"></span>");
	}
}

function loadViews(tab){
   switch (tab){
      case "atl": {
    	 $("#inspage-atl").html(loadingIcon);
         $.get("./inscribir/atl", function(data, status){           
            if (status = "success"){
               $("#inspage-atl").html(data);
               checkSelectedButtons();
            } else {
               $("#inspage-atl").html("Error al cargar datos");
            }
         });      
      } break;
      case "pru": {
     	 $("#inspage-pru").html(loadingIcon);
         $.ajax({
    	    type: "post",
    		url: "./inscribir/pru",
    		data: {selAtl: selAtl},
    		success: function(data, status) {
                if (status = "success"){
                    $("#inspage-pru").html(data);
                    toggleRadioButton($(".sel-pruatl")[0]);
                    for (i = 0; i < selPru.length; i++){
                    	countDiv = $("#count-pru-"+selPru[i][0]);
                    	countDiv.html(parseInt(countDiv.html()) + 1);
                    }
                 } else {
                    $("#inspage-pru").html("Error al cargar datos");
                 }
    	    }
    	 });
      } break;
      default: break;
   }
   $('[data-toggle="tooltip"]').tooltip();
   $('abbr').tooltip();
}

function insAtlSearch(cat, query){
	$("#inspage-atl").html(loadingIcon);
    $.get("./inscribir/atl" + atlSearchParam(cat, query), function(data, status){           
        if (status = "success"){
           $("#inspage-atl").html(data);
           checkSelectedButtons();
        } else {
           $("#inspage-atl").html("Error al cargar datos");
        }
    });     
}

function checkSelectedButtons(){
	selButtons = $(".sel-btn");
	$(selButtons).each(function(){
		doToggle = false;
		data = this.id.split("-");
		type = data[1];
		id = data[2];
		if (type == "atl"){
			for (i = 0; i < selAtl.length; i++){
				if (selAtl[i][0] == id){
					doToggle = true;
					break;
				}
			}
		} else if (type == "pru"){
			for (i = 0; i < selPru.length; i++){
				if ((selPru[i][0] == idAtlPruList) && ((selPru[i][1] == id))){
					doToggle = true;
					break;
				}
			}
		}
		if (doToggle == true){
			$(this).removeClass("btn-default");
			$(this).addClass("btn-info");
			$(this).html(btnON);
			$(this).button('toggle');
		}
	});
}

function toggleCheckButton(item){
	data = item.id.split("-");
	type = data[1];
	id = data[2];
	nombre = $(item).data("nombre");
	catnombre = $(item).data("catnombre");
	if ($(item).hasClass("btn-default")){ //ON
		$(item).removeClass("btn-default");
		$(item).addClass("btn-info");
		$(item).html(btnON);
        if (type == "atl"){
    		selAtl.push([id, nombre, catnombre]);
        } else if (type == "pru"){
        	countDiv = $("#count-pru-"+idAtlPruList);
        	countDiv.html(parseInt(countDiv.html()) + 1);
        	selPru.push([idAtlPruList, id]);
        }
	} else { //OFF
		$(item).removeClass("btn-info");
		$(item).addClass("btn-default");
		$(item).html(btnOFF);
		if (type == "atl"){
			for (i = 0; i < selAtl.length; i++){
				if (selAtl[i][0] == id){
					selAtl.splice(i, 1);
					break;
				}
			}
		} else if (type == "pru"){
        	countDiv = $("#count-pru-"+idAtlPruList);
        	countDiv.html(parseInt(countDiv.html()) - 1);
			for (i = 0; i < selPru.length; i++){
				if ((selPru[i][0] == idAtlPruList) && ((selPru[i][1] == id))){
					selPru.splice(i, 1);
					break;
				}
			}
		}
	}
    checkPillStatus();
}

function toggleRadioButton(item){
	if ($(item).hasClass("btn-default")){
		data = item.id.split("-");
		type = data[1];
		idAtl = data[2];
		$(".sel-pruatl").each(function(){
			$(this).removeClass("btn-info");
			$(this).addClass("btn-default");
			$(this).attr("aria-pressed", false);
		})
		$(item).removeClass("btn-default");
		$(item).addClass("btn-info");
		$(item).attr("aria-pressed", true);
    	$("#pru-for-atl").html(loadingIcon);
        $.get("./inscribir/pru/"+idAtl, function(data, status){
            if (status = "success"){
               $("#pru-for-atl").html(data);
               idAtlPruList = idAtl;
               checkSelectedButtons();
            } else {
               $("#pru-for-atl").html("Error al cargar datos");
            }
         }); 
	}
}
