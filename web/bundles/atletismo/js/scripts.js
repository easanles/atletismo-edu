

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
}

function removeFormRow(button){
   button.parentElement.parentElement.remove();
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


