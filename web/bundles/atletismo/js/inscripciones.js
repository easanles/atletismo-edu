
var selAtl = [];
var btnON  = "<span class=\"glyphicon glyphicon-check\"></span> <strong>SI</strong>";
var btnOFF = "<span class=\"glyphicon glyphicon-unchecked\"></span> NO";

//FORMULARIO DE INSCRIPCION

function toggleInsPages(tab){
	if (!$("#inspill-" + tab).hasClass('disabled')){
       $(".nav-pills li").removeClass("active");
       $(".pagecontent").css("display", "none");
    
       $("#inspill-" + tab).addClass("active");
       $("#inspage-" + tab).css("display", "inline");
       if ((tab == "atl") || (tab == "pru")){
          loadViews(tab);
       }
	}
}

function loadViews(tab){
   //icon = $(".working");
   //icon.removeClass("hidden");
   switch (tab){
      case "atl": {
         $.get("./inscribir/atl", function(data, status){           
            if (status = "success"){
               $("#inspage-atl").html(data);
               checkSelectedButtons();
            } else {
               $("#inspage-atl").html("Error al cargar datos");
            }
            //icon.addClass("hidden");      
         });      
      } break;
      case "pru": {
         $.ajax({
    	    type: "post",
    		url: "./inscribir/pru",
    		data: {selAtl: selAtl},
    		success: function(data, status) {
                if (status = "success"){
                    $("#inspage-pru").html(data);
                    toggleRadioButton($(".sel-pruatl")[0]);
                 } else {
                    $("#inspage-pru").html("Error al cargar datos");
                 }
                 //icon.addClass("hidden");      
    	    }
    	 });
      } break;
      default: break;
   }
   //$('.dropdown-toggle').dropdown();
   $('[data-toggle="tooltip"]').tooltip();
   $('abbr').tooltip();
}

function insAtlSearch(cat, query){
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
	atlIdArray = [];
	for(i = 0; i < selAtl.length; i++){
		atlIdArray.push(selAtl[i][0]);
	}
	selButtons = $(".sel-btn");
	$(selButtons).each(function(){
		data = this.id.split("-");
		if ($.inArray(data[2], atlIdArray) != -1){
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
		} else if (type == pru){
			
		}
	}
	if (selAtl.length != 0){
		$("#inspill-pru").removeClass("disabled");
		$("#btn-next").attr("disabled", false);
	} else {
		$("#inspill-pru").addClass("disabled");
		$("#btn-next").attr("disabled", true);		
	}
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
        $.get("./inscribir/pru/"+idAtl, function(data, status){
            if (status = "success"){
               $("#pru-for-atl").html(data);
               checkSelectedButtons();
            } else {
               $("#pru-for-atl").html("Error al cargar datos");
            }
         }); 
	}
}
