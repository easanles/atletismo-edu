
var selAtl = {};

//FORMULARIO DE INSCRIPCION

function toggleInsPages(tab){
	if (!$("#inspill-" + tab).hasClass('disabled')){
       $(".nav-pills li").removeClass("active");
       $(".tabcontent").css("display", "none");
    
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
            } else {
               $("#inspage-atl").html("Error al cargar datos");
            }
            //icon.addClass("hidden");      
         });      
      } break;
      default: break;
   }
   $('.dropdown-toggle').dropdown();
   $('[data-toggle="tooltip"]').tooltip();
   $('abbr').tooltip();
}

function insAtlSearch(cat, query){
    $.get("./inscribir/atl" + atlSearchParam(cat, query), function(data, status){           
        if (status = "success"){
           $("#inspage-atl").html(data);
        } else {
           $("#inspage-atl").html("Error al cargar datos");
        }
    });     
}

function toggleCheckButton(item){
	data = item.id.split("-");
	if ($(item).hasClass("btn-default")){ //ON
		$(item).removeClass("btn-default");
		$(item).addClass("btn-info");
		$(item).html("<span class=\"glyphicon glyphicon-check\"></span> <strong>SI</strong>");
		
	} else { //OFF
		$(item).removeClass("btn-info");
		$(item).addClass("btn-default");
		$(item).html("<span class=\"glyphicon glyphicon-unchecked\"></span> NO");
	}
}