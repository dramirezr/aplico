var lat = lng = deslat = destlng = 0;
var directionsService = new google.maps.DirectionsService();
var geocoder = new google.maps.Geocoder();
var updateLocationDemonId;
var verifyServiceDemonId;
var localizationDemonId;
var running_service = null;
var request_id = null;

function localizame() {
    if (navigator.geolocation) { /* Si el navegador tiene geolocalizacion */
        navigator.geolocation.getCurrentPosition(coordenadas, errores);
    }else{
    	$('#current-position').html('Oops! no hay soporte para la geolocalización.');
    }
}

function coordenadas(position) {
    lat = position.coords.latitude; /*Guardamos nuestra latitud*/
    lng = position.coords.longitude; /*Guardamos nuestra longitud*/   
    
    $('#current-position').html('Latitud: ' + lat + ' Longitud: ' + lng);
    
}

function errores(err) {
    /*Controlamos los posibles errores */
    if (err.code == 0) {
    	$('#current-position').html("Oops! Algo ha salido mal");
    }
    if (err.code == 1) {
    	$('#current-position').html("Oops! No has aceptado compartir tu posición");
    }
    if (err.code == 2) {
    	$('#current-position').html("Oops! No se puede obtener la posición actual");
    }
    if (err.code == 3) {
    	$('#current-position').html("Oops! Hemos superado el tiempo de espera");
    }
}

function codeLatLng(lat, lng) {

	var latlng = new google.maps.LatLng(lat, lng);

	geocoder.geocode({'latLng': latlng}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			if (results[1]) {
				//formatted address
				$('#current-position').html(results[0].formatted_address);
			} else {
				$('#current-position').html('No encontró una dirección asociada a las coordenadas.');
			}
			
		} else {
			$('#current-position').html("Fallo en las Appis de Google : "+ status);
		}
	});
}

$(document).ready(function() {
	localizame();
	
	localizationDemonId = setInterval(localizame, verification_interval);
	updateLocationDemonId = setInterval(updateLocation, verification_interval);
	verifyServiceDemonId = setInterval(verifyService, verification_interval);
	
	$('#confirm-service').click(function(e){
		e.preventDefault();
		
		confirm_service();
	});
	
	$('#cancel-service').click(function(e){
		e.preventDefault();
		
		cancel_service();
	});
	
	$('#delivered-service').click(function(e){
		e.preventDefault();
		
		service_delivered();
	});
	
});

function updateLocation(){
    $.ajax({
        type : "GET",
        url : 'agent/update_location',        
        dataType : "json",
        data : {
        	lat : lat,
        	lng : lng
        }
    }).done(function(response){        

    });	
}

function verifyService(){
    $.ajax({
        type : "GET",
        url : 'agent/get_service',        
        dataType : "json",
        data : {
        	demonId : verifyServiceDemonId,
        	lat : lat,
        	lng : lng
        }
    }).done(function(response){        
        if(response.state == 'ok'){
        	request_id = response.request;
        	destlat = response.latitud;
        	destlng = response.longitud;
        	
        	$('#service-addr').html(response.ubicacion)
        	$('#confirm-service').show();
        	$('#cancel-service').show();
        	
        	clearInterval(verifyServiceDemonId);
        }
    });
}

function service_delivered(){

    $.ajax({
        type : "GET",
        url : 'agent/delivered_service',        
        dataType : "json",
        data : {
        	request_id : request_id
        }
    }).done(function(response){});	

	cancel_service();
}

function cancel_service(){
	
	request_id = null;
	
	$('#service-addr').html('')
	$('#confirm-service').hide();
	$('#cancel-service').hide();
	$('#alert-msg').html('');
	$('#alert-msg-wrapper').hide();
	$('#delivered-service').hide();
	
	verifyServiceDemonId = setInterval(verifyService, verification_interval);
	
    $.ajax({
        type : "GET",
        url : 'agent/switch_to_free',        
        dataType : "json",
        data : {}
    }).done(function(response){});	
	
}

function confirm_service(){
	
	$('#confirm-service').hide();
	
    $.ajax({
        type : "GET",
        url : 'agent/confirm',        
        dataType : "json",
        data : {
        	request_id : request_id 
        }
    }).done(function(response){        
    	if(response.state == 'ok'){
    		//assigned
    		$('#confirm-service').hide();
    		$('#alert-msg').html('Servicio en curso');
    		$('#alert-msg-wrapper').show();
    		$('#delivered-service').show();
    	} else {
    		//taken by other one
    		cancel_service();
    	}
    });	
}
