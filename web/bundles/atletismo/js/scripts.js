

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

function atlSearch(cat, query){
	path = "./atletas";
	if ((cat != null) && (cat != "")){
		if ((query != null) && (query != "")){
			path = path + "?cat=" + cat + "&q=" + query;
		} else {
			path = path + "?cat=" + cat;
		}
	} else {
		if ((query != null) && (query != "")) {
			path = path + "?q=" + query;
		}
	}
	goToUrl(path);
}

function pruSearch(id, tpr, cat){
	path = "./" + id;
	if ((tpr != null) && (tpr != "")){
		if ((cat != null) && (cat != "")){
			path = path + "?tpr=" + tpr + "&c=" + cat;
		} else {
			path = path + "?tpr=" + tpr;
		}
	} else {
		if ((cat != null) && (cat != "")) {
			path = path + "?c=" + cat;
		}
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
   }
   else {
	   dl.css("height", "0px");
   }
}
	
$(document).ready(function(){
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
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
})


