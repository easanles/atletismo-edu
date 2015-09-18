
function toggleContent(tab){
    $("#navtab-tp").removeClass("active");
    $("#tabcontent-tp").css("display", "none");
    $("#navtab-cat").removeClass("active");
    $("#tabcontent-cat").css("display", "none");
    $("#navtab-comm").removeClass("active");
    $("#tabcontent-comm").css("display", "none");
   switch (tab){
      case ('tp'): {
          $("#navtab-tp").addClass("active");
          $("#tabcontent-tp").css("display", "inline");
          loadViews("tp");
      }; break;
       case ('cat'): {
          $("#navtab-cat").addClass("active");
          $("#tabcontent-cat").css("display", "inline");
          loadViews("cat");
      }; break;
       case ('comm'): {
         $("#navtab-comm").addClass("active");
         $("#tabcontent-comm").css("display", "inline");
      }; break;
      default:;
   }
}

function loadViews(content){
   icon = $(".updater");
   icon.removeClass("hidden")

   switch (content){
      case "tp": {
        $.get("./tipoprueba", function(data, status){
           if (status = "success"){
              $("#tpr-table").html(data);
           } else {
              $("#tabcontent-tp").html("Error al cargar datos");
           }
           icon.addClass("hidden");      
        });      
      } break;
      case "cat": {
    	 route = routeCatData($("#cat-showOutdated").is(':checked'));
         $.get(route, function(data, status){
            if (status = "success"){
                $("#cat-table").html(data);
                $('.dropdown-toggle').dropdown()
             } else {
                $("#tabcontent-cat").html("Error al cargar datos");
            }
            icon.addClass("hidden");      
          });break;
         
      } break;
      default: break;
      $('.dropdown-toggle').dropdown()
   }
}

//TIPOS DE PRUEBA

function toggleTprmTable(id, button){
   button.button('toggle');
   table = $("#tprm-table-" + id);
   if (table.css("display") == "none") table.css("display", "table-row");
   else table.css("display", "none");
}

function addFormRow(){
   collectionHolder = $('#form-collection')
    
    prototype = collectionHolder.data('prototype');
    index = collectionHolder.data('index');
    collectionHolder.data('index', index + 1);
    newForm = prototype.replace(/__name__/g, index);

    collectionHolder.append(newForm);
}

function removeFormRow(button){
   button.parentElement.parentElement.remove();
}

//CATEGORIAS

function routeCatData(outdated){
	if (outdated == true) return "./categoria?outd=true";
	else return "./categoria?outd=false";
}

//COMANDOS

function sendAction(path, icon){
	   alerthtml_preok = "<div class=\"alert alert-info alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <strong>Hecho: </strong><span>";
	   alerthtml_preerr = "<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button> <strong>Error: </strong><span>";
	   alerthtml_pos = "</span></div>";
	   $.getJSON(path, function(data, status){
	        if (status = "success"){
	           if (data.success == true){
	             $("#alert_div").append(alerthtml_preok + data.message + alerthtml_pos);
	           } else {
	             $("#alert_div").append(alerthtml_preerr + data.message + alerthtml_pos);
	           }
	        } else {
	          $("#alert_div").append(alerthtml_preerr + 'ERROR' + alerthtml_pos);
	        }
	       icon.removeClass("spinning");
	   });
	}


$(document).ready(function(){   
   $("#btn_poblarbd").click(function(){
      icon = $(this).find("span");
      icon.addClass("spinning");
         sendAction("./poblarbd", icon);
   });

    $("#btn_borrarbd").click(function(){
       icon = $(this).find("span");
      icon.addClass("spinning");
       sendAction("./borrarbd", icon);
    });
   
    $("#btn_rehacerbd").click(function(){
       icon = $(this).find("span");
      icon.addClass("spinning");
       sendAction("./rehacerbd", icon);
    });
    
    $("#btn_limpiarcache").click(function(){
       icon = $(this).find("span");
      icon.addClass("spinning");
       sendAction("./limpiarcache", icon);
    });
    
    $("#btn_asseticdump").click(function(){
       //icon = $(this).find("span");
      //icon.addClass("spinning");
      //sendAction("./asseticdump", icon);
    });
    
    loadViews();

});