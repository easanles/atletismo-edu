
var autoCompleteData;

function goToUrl(path){
	window.location.href = path;
}

function comSearch(temp, query){
	path = "./competiciones";
	if ((temp != null) && (temp != "")){
		if ((query != null) && (query != "")){
			path = path + "?temp=" + temp + "&q=" + query;
		} else {
			path = path + "?temp=" + temp;
		}
	} else {
		if ((query != null) && (query != "")) {
			path = path + "?q=" + query;
		}
	}
	goToUrl(path);
}

function atlSearchParam(cat, query){
	result = '';
	if ((cat != null) && (cat != "")){
		if ((query != null) && (query != "")){
			result = result + "?cat=" + cat + "&q=" + query;
		} else {
			result = result + "?cat=" + cat;
		}
	} else {
		if ((query != null) && (query != "")) {
			result = result + "?q=" + query;
		}
	}
	return result;
}

function atlSearch(url, cat, query){
	path = url;
	path = path + atlSearchParam(cat, query);
	goToUrl(path);
}

function pruSearch(id, cat){
	path = "./" + id;
	if ((cat != null) && (cat != "")) {
	   path = path + "?c=" + cat;
	}
	goToUrl(path);
}

function getQuery(){
	return document.getElementById('search-input').value;
}

function checkEnterKeypress(event){
	if (event.keyCode == 13) {
		document.getElementById("search-button").click();
	}
}

function toggleDropListTable(id, button){
   button.button('toggle');
   dl = $("#droplist-" + id);
   if (dl.css("height") == "0px"){
	   dl.css("height", dl.data("height"));
	   button.parent().closest("tr").attr("class", "info");
   }
   else {
	   dl.css("height", "0px");
	   button.parent().closest("tr").attr("class", "");
   }
}

function addFormRow(){
	collectionHolder = $('#form-collection')
	    
	prototype = collectionHolder.data('prototype');
	index = collectionHolder.data('index');
	collectionHolder.data('index', index + 1);
	newForm = prototype.replace(/__name__/g, index);
	collectionHolder.append(newForm);
	$(".count-td").last().html(index + 1);
}

function removeFormRow(button){
   collectionHolder = $('#form-collection')
   index = collectionHolder.data('index');
   collectionHolder.data('index', index - 1);

   button.parentElement.parentElement.remove();
   
   count = 1;
   $(".count-td").each(function(){
      this.innerHTML = count;
      count++;
   });
}

function toggleAsist(item, idPar){
	itemData = $(item).attr("id").split("-");
    if (itemData[0] == "asistCB"){
       cb = $(item);
       li = $("#asistLI-" + itemData[1]);
    } else if (itemData[0] == "asistLI") {
       cb = $("#asistCB-" + itemData[1]);
       li = $(item);
       cb.prop("checked", !cb.prop("checked"));
    }
    if (cb.prop("checked") == true){
        li.html("<span class=\"glyphicon glyphicon-check\"></span> Asistencia [SI]")
    } else {
        li.html("<span class=\"glyphicon glyphicon-unchecked\"></span> Asistencia [NO]")
    }
	$.ajax({
       type: "post",
	   url: "./asistencia",
	   data: {par: idPar, val: cb.prop("checked")},
	   success: function(data) {
          console.log(data);
	   }
    });
}

function selectEntorno(index){
	$("table").each(function(){
		$(this).addClass("hidden");
	});
	$("#tabla-entorno-" + index).removeClass("hidden");
}

function addAutoComplete(selector, data){
	if (data != null) autoCompleteData = data;
	$(selector).autoComplete({
	    minChars: 1,
	    source: function(term, suggest){
	        term = term.toLowerCase();
	        var choices = autoCompleteData;
	        var matches = [];
	        for (i=0; i<choices.length; i++)
	              if (~choices[i].toLowerCase().indexOf(term))
	                    matches.push(choices[i]);
	        suggest(matches);
	    }
    });
}
	
function checkIdAtl(path, id){
	$.getJSON(path + "?id=" + id, function(data, status){
	   if (status == "success"){
	      if (data.success == true){
		     $("#idatl-help").attr("class", "text-info");
	         $("#idatl-help").html("<p style=\"margin-top: 5px\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> " + data.atl + "</p>");
	      } else {
    	     $("#idatl-help").attr("class", "text-danger");
	         $("#idatl-help").html("<p style=\"margin-top: 5px\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> " + data.atl + "</p>");
	      }
	   } else {
	      $("#idatl-help").html("Error");
	   }
	});
}

function clearUsu(){
    $('#atl_usu_nombre').val("");
    $('#atl_usu_contra').val("");
    $('#atl_usu_rol').val("");
    $('#usu_nombre_display').html("<em class=\"text-muted\">Ninguno</em>");
    $('#clear_usu').addClass("hidden");
}

function updateUsuRow(id){
	$.getJSON("./" + id + "/" + "checkusu", function(data, status){
       if (status == "success"){
	      if (data.success == true){
	    	  if (data.usu != null){
	    		  nombreHTML = data.usu.nombre;
	    		  if (data.usu.rol === "coordinador"){
	    			  nombreHTML = nombreHTML + " <strong class=\"text-info\">Coordinador</strong>";
	    		  }
		    	  $("#usu_nombre_display").html(nombreHTML);
		    	  $("#asig_usu").addClass("hidden");
		    	  $("#del_usu").removeClass("hidden");
		    	  $("#edi_usu").removeClass("hidden");
	    	  } else {
		    	  $("#usu_nombre_display").html("<em class=\"text-muted\">Ninguno</em>");
		    	  $("#asig_usu").removeClass("hidden");
		    	  $("#del_usu").addClass("hidden");
		    	  $("#edi_usu").addClass("hidden");
	    	  } 
	      } else {
	    	  $("#usu_nombre_display").html("No existe el atleta")
	      }
	   } else {
          $("#usu_nombre_display").html("Error");
	   }
	});
}

$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		  $('abbr').tooltip()
          $('.dropdown-toggle').dropdown()
    });
	$('.droplist').each(function (){
		$(this).data("height", $(this).height());
		$(this).css("height", "0px");
	});
	$(window).keydown(function(event){
	   if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
       }
	});
	$('input:checkbox').each(function(){
		$(this).prop("checked", this.hasAttribute("checked"));
	})
})

$(window).resize(function(){
   $('.droplist').each(function (){
	  if ($(this).css("height") == "0px") hide = true;
	  else hide = false;
      $(this).css("height", "auto");
      $(this).data("height", $(this).height());
	  if (hide){
         $(this).css("height", "0px");
	  }
	});
})


