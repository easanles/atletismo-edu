
//FORMULARIO DE INSCRIPCION

var selAtl = [];
var btnON  = "<span class=\"glyphicon glyphicon-check\"></span> <strong>SI</strong>";
var btnOFF = "<span class=\"glyphicon glyphicon-unchecked\"></span> NO";
var loadingIcon = "<span class=\"glyphicon glyphicon-refresh spinning\"></span>";
var idAtlPruList = null;
var selPru = [];
var countPru = 0;


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
		if ($("#inspill-pru").hasClass("hidden")){
		   $('#inspill-confirm a').click();
		} else {			
		   $('#inspill-pru a').click();
		}
	} else if ($("#inspill-pru").hasClass("active")){
		$('#inspill-confirm a').click();
	} else if ($("#inspill-confirm").hasClass("active")){
		submitData();
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
		if (countPru == 0){
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
            if (status == "success"){
               $("#inspage-atl").html(data);
               $('abbr').tooltip();
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
                if (status == "success"){
                    $("#inspage-pru").html(data);
                    toggleRadioButton($(".sel-pruatl")[0]);
                    countPru = 0;
                    for (i = 0; i < selPru.length; i++){
                 		 for (j = 0; j < selAtl.length; j++){
                   			if (selPru[i][0] == selAtl[j][0]){
                            	countPru++;
                            	countDiv = $("#count-pru-"+selPru[i][0]);
                            	countDiv.html(parseInt(countDiv.html()) + 1);
                   			} 
                   		 }
                    }
                 } else {
                    $("#inspage-pru").html("Error al cargar datos");
                 }
    	    }
    	 });
      } break;
      case "confirm": {
      	 $("#inspage-confirm").html(loadingIcon);
      	 data = [];
      	 for (i = 0; i < selPru.length; i++){
      		 for (j = 0; j < selAtl.length; j++){
      			if (selPru[i][0] == selAtl[j][0]){
         	      	 data.push(selPru[i]);
                     break;
      			} 
      		 }
      	 }
         $.ajax({
    	    type: "post",
    		url: "./inscribir/confirm",
    		data: {data: data},
    		success: function(data, status) {
                if (status == "success"){
                    $("#inspage-confirm").html(data);
                    $('abbr').tooltip();
                 } else {
                    $("#inspage-confirm").html("Error al cargar datos");
                 }
    	    }
    	 });    	  
      } break;
      default: break;
   }
   $('[data-toggle="tooltip"]').tooltip();
   $('abbr').tooltip();
}

function insAtlSearch(cat, query, from){
	$("#inspage-atl").html(loadingIcon);
    $.get("./inscribir/atl" + atlSearchParam(cat, query, from), function(data, status){           
        if (status == "success"){
           $("#inspage-atl").html(data);
           checkSelectedButtons();
           $('abbr').tooltip();
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

function toggleCheckButton(item, cuotaPru){
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
    		if (cuotaPru != null){
    			selPru.push([id, cuotaPru]);
    		}
        } else if (type == "pru"){
        	countPru++;
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
			countPru--;
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
			$(this).parent().closest("tr").attr("class", "");
		})
		$(item).removeClass("btn-default");
		$(item).addClass("btn-info");
		$(item).attr("aria-pressed", true);
		$(item).parent().closest("tr").attr("class", "info");
    	$("#pru-for-atl").html(loadingIcon);
        $.get("./inscribir/pru/"+idAtl, function(data, status){
            if (status == "success"){
               $("#pru-for-atl").html(data);
               idAtlPruList = idAtl;
               checkSelectedButtons();
            } else {
               $("#pru-for-atl").html("Error al cargar datos");
            }
         }); 
	}
}

function toggleConfRow(cb){
	idAtl = cb.id.split("-")[1];
	sidPru = cb.id.split("-")[2];
	if ($(cb).is(":checked")){
		$("#tr-" + idAtl + "-" + sidPru).removeClass("warning");
		$("#tr-" + idAtl + "-" + sidPru + " td").removeClass("text-muted");
		$("#coste-" + idAtl + "-" + sidPru).attr("disabled", false);
	} else {
		$("#tr-" + idAtl + "-" + sidPru).addClass("warning");
		$("#tr-" + idAtl + "-" + sidPru + " td").addClass("text-muted");
		$("#coste-" + idAtl + "-" + sidPru).attr("disabled", true);
	}
	updateCosteTotal();
}

function updateCosteTotal(){
	costeTotal = 0.00;
	$("#btn-next").attr("disabled", false);
	$("#btn-inscribir").attr("disabled", false);
	$(".coste-ins").each(function(){
		idAtl = this.id.split("-")[1];
		sidPru = this.id.split("-")[2];
		if ($("#cb-" + idAtl + "-" + sidPru).is(":checked") == true){
        	if (isNaN(parseFloat(this.value)) == true){
				$(this).parent().addClass("has-error");
				$("#btn-next").attr("disabled", true);
				$("#btn-inscribir").attr("disabled", true);
			} else {
				$(this).parent().removeClass("has-error");					
				costeTotal += parseFloat(this.value);				
			}
		}
	});
	$("#coste-total").html(costeTotal.toFixed(2).replace('.', ',') + "€");
}

function submitData(){
	 data = [];
	 for(i = 0; i < selPru.length; i++){
		 idAtl = selPru[i][0];
		 sidPru = selPru[i][1];
		 if ($("#cb-" + idAtl + "-" + sidPru).is(":checked")){
			 elem = [idAtl, sidPru, $("#coste-" + idAtl + "-" + sidPru).val()];
			 data.push(elem);
		 }
	 }
     $.ajax({
	    type: "post",
		url: "./inscribir/submit",
		data: {data: data},
		success: function(data, status) {
           if (status == "success"){
              window.location.href = "./inscripciones";
           }
	    }
	 });
}
