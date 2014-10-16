var markersArray = [];

$(document).keypress(function(e){
  if (e.which === 49) {
  	//hola();
  }
});

$(document).ready(function() {
//----

	$('#btn-add').click(function (e){
        e.preventDefault();
        var coordenadas =  new google.maps.LatLng(latitud,longitud);             		
        var result = {id:"-1", latitud:latitud, longitud:longitud, direccion:$('input[name="address"]').val(), observacion:''}; 
        setIcons(coordenadas, result); 
        $('#idlocation').val("-1");
    });

    $('#btn-save').click(function (e){
        e.preventDefault();
        saveClientLoc();
    });
    
});

function searchUserTelephone(e) {
    if (window.event) {
        keyval=e.keyCode
    } else 
        if (e.which) {
            keyval=e.which
        } 
    if (keyval=="13") {
        e.preventDefault();
        getSelectCustLocation();
    } 
}


function saveClientLoc(){
 	if ($('input[name="phone-client"]').val()!=''){  

 		if ($('input[name="address"]').val()!=''){  

    		$.ajax({
            	type : "GET",
                url : server + '/' + lang + '/api/saveClientLoc',        
                dataType : "json",
                data : {
                	id 		: $('input[name="idlocation"]').val(),
                	idclient: $('input[name="idclient"]').val(),
                    name 	: $('input[name="name-client"]').val(),
                    phone 	: $('input[name="phone-client"]').val(),
                    cell 	: $('input[name="cell-client"]').val(),
                    address : $('input[name="address"]').val(),
                    lat 	: $('input[name="lat"]').val(),
                    lng 	: $('input[name="lng"]').val()
                    
                 }
       		}).done(function(response){
            	if(response.idlocation > 0){
                	$('#idlocation').val(response.idlocation); 
                	$('#idclient').val(response.idclient); 
                	alert('Ubicacion del usuario guardada con exito.');
                }
        	}).fail(function(jqXHR, textStatus, errorThrown){
         		alert('Error:'+errorThrown);
     		}); 
	
    	}else{
        	alert("El cliente debe tener una dirección.");
        	$('#address').focus();
    	}
        
    }else{
    	alert("El cliente debe tener un número de teléfono.");
    	$('#phone-client').focus();
    }

 };
    


function getSelectCustLocation(){
	$('option', '#select-location').remove();
	$("option","#select-location" ).empty();
	$('#address').val("");
	$('#idclient').val("-1");
	$('#idlocation').val("-1");
    deleteOverlays();
    var nombre='';
    var celular='';
    $('#name-client').val(nombre);
    $.ajax({
            type : "GET",
            url : server + '/' + lang + '/api/get_cust_location' ,        
            dataType : "json",
            data : {
            	phone : $('input[name="phone-client"]').val(),
            }
    }).done(function(response){
      		
    	if(response.state == 'ok'){
      		var bounds = new google.maps.LatLngBounds();
            $('#select-location').append('<option value="-1" class="dropDownBlk">Todas</option>');
            for(var i in response.result){
                
            	$('#select-location').append('<option value="'+response.result[i].id+'" class="dropDownBlk"  >'+response.result[i].direccion+'</option>');
             	nombre = response.result[i].nombre;
             	celular = response.result[i].celular;
             	$('#idclient').val(response.result[i].idcliente);
				coordenadas =  new google.maps.LatLng( response.result[i].latitud, response.result[i].longitud);             		
             	setIcons(coordenadas, response.result[i]);
             	bounds.extend(coordenadas);
             	//$("#location option[value='test1']").attr("selected", "selected");
            }
            if(nombre!=''){
	        	$("#select-location option[value='-1']").attr("selected", "selected");
	          	$('#select-location').selectmenu("refresh", true);
	  			$('#select-location').selectmenu('open');
	            $('#select-location').focus();
	            map.setCenter(bounds.getCenter());
	        }
                
        }
        $('#name-client').val(nombre);
        $('#cell-client').val(celular);
        //$('#select-location').focus();
    });
       
}

function centerCustLocation(id,center){
	for(var i in markersArray){
		
        if(markersArray[i].id===id){
        	userMarker.setPosition(markersArray[i].position);
			if (center==='S')
				map.setCenter(markersArray[i].position);
			$('#address').val(markersArray[i].title);
			latitud = markersArray[i].latitud;
    		longitud = markersArray[i].longitud;
    		$('#lat').val(markersArray[i].latitud);
    		$('#lng').val(markersArray[i].longitud);
    		$('#idlocation').val(markersArray[i].id);
    		$('#idclient').val(markersArray[i].idcliente);
    		//console.log('---'+markersArray[i].position[0]);
        }
    }
	
}


function setIcons(coordenadas, result){
    iconMarker = new google.maps.Marker({
    	id       : result.id,
    	idcliente: result.idcliente,
    	latitud  : result.latitud,
    	longitud : result.longitud,
        position : coordenadas,
        map      : map,
        animation: google.maps.Animation.DROP, 
        draggable: true,
        icon     : server +'/assets/images/casa.png',
        title    : result.direccion
    });
    markersArray.push(iconMarker);
     
    google.maps.event.addListener(iconMarker, 'click', function(evento){
   		centerCustLocation(result.id,'N');
    });    

    google.maps.event.addListener(iconMarker, "dragend", function(evento) {
    	$('#idlocation').val(result.id);
        $('#lat').val(evento.latLng.lat());
        $('#lng').val(evento.latLng.lng());
        codeLatLng(evento.latLng.lat(), evento.latLng.lng());
    });
    
}

function clearOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
  }
}

// Shows any overlays currently in the array
function showOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(map);
    }
  }
}

function deleteOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
    markersArray.length = 0;
  }
}



