var server = 'http://aplico.dev/es/';

var lat = lng = deslat = destlng = 0;
var scode = null;
var user = null;
var localizationDemonId;
var updateLocationDemonId;
var verification_interval = 5000;
var verifyServiceDemonId;
var verifyServiceStateDemonId;
var geoOptions = { timeout: verification_interval };
var ubicacionServicio = null;
var request_id = null;

$(document).ready(function() {
	
	$.mobile.loading( "show" );
	
	$("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap, #btn-llego-wrap").hide();
	
	get_sec_code();
	
	$('#do-login').click(function(e){
		
		var username = $('#username').val();
		var password = $('#password').val();
		
	    $.ajax({
	        type : "POST",
	        url : server + 'api/login',        
	        dataType : "json",
	        data : {
	        	username : username,
	        	password : password,
	        	hms1: scode
	        }
	    }).done(function(response){
	    	
	    	if(response.state=='ok'){
	    		$("#show-dashboard").trigger('click');
	    		user = response.data
	    		$('#agent-name').html(user.nombre);
	    		$('#agent-photo').attr('src', "/assets/images/agents/" + user.foto) ;
	    		
	    		localizame();
	    		localizationDemonId = setInterval(localizame, verification_interval);
	    		updateLocationDemonId = setInterval(updateLocation, verification_interval);
	    		verifyServiceDemonId = setInterval(verifyService, verification_interval);
	    		
	    	}else{
	    		alert(response.msg);
	    		//$('#popupBasic').html();
	    		//$('#popupBasic').popup();	    		
	    	}
	    });	

	});

	
	$('#btn-cancelar').click(function(e){
		e.preventDefault();
		cancel_service();
	});
	
	$('#btn-aplico').click(function(e){
		e.preventDefault();
		confirm_service();
	});

	$('#btn-entregado').click(function(e){
		e.preventDefault();
		service_delivered();
	});
	
	$('#btn-llego').click(function(e){
		e.preventDefault();
		arrival_confirmation();
	});
	
	$('#btn-salir').click(function(e){
		clearInterval(localizationDemonId);
		clearInterval(updateLocationDemonId);
		clearInterval(verifyServiceDemonId);
		
		
	
	});
	
});

function arrival_confirmation(){
    $.ajax({
        type : "POST",
        url : server + 'agent/arrival_confirmation',        
        dataType : "json",
        data : {
        	request_id : request_id,
        	hms1: scode
        }
    }).done(function(response){});	
	
}

function verifyService(){
    $.ajax({
        type : "POST",
        url : server + 'agent/get_service',        
        dataType : "json",
        data : {
        	demonId : verifyServiceDemonId,
        	lat : lat,
        	lng : lng,
        	hms1: scode
        }
    }).done(function(response){        
        if(response.state == 'ok'){
        	request_id = response.request;
        	destlat = response.latitud;
        	destlng = response.longitud;
        	ubicacionServicio = response.ubicacion;
        	
        	$('#service-addr').html(response.ubicacion_corta);
        	$('#btn-aplico-wrap').show();
        	$('#btn-cancelar-wrap').show();
        	
        	clearInterval(verifyServiceDemonId);
        	
        	$.playSound('/assets/audio/ring.mp3');
        }
    });
}

function switchToFree(){
	request_id = null;
	
	$('#service-addr').html('');
	$('#verificacion-cod').html('');
	
	$("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap, #btn-llego-wrap").hide();
		
	verifyServiceDemonId = setInterval(verifyService, verification_interval);
	clearInterval(verifyServiceStateDemonId);
	
    $.ajax({
        type : "POST",
        url : server + 'agent/switch_to_free',        
        dataType : "json",
        data : {
        	hms1: scode
        }
    }).done(function(response){});		
}

function cancel_service(){ 
	
	$('#service-addr').html('');
	$('#verificacion-cod').html('');
	
	$("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap, #btn-llego-wrap").hide();
		
	verifyServiceDemonId = setInterval(verifyService, verification_interval);
	clearInterval(verifyServiceStateDemonId);
	
    $.ajax({
        type : "POST",
        url : server + 'agent/cancel_service',        
        dataType : "json",
        data : {
        	hms1: scode,
        	request_id: request_id
        }
    }).done(function(response){
    	request_id = null;
    });	
	
}

function service_delivered(){

    $.ajax({
        type : "POST",
        url : server + 'agent/delivered_service',        
        dataType : "json",
        data : {
        	request_id : request_id,
        	lat : lat,
        	lng : lng,
        	hms1: scode
        }
    }).done(function(response){});	

    switchToFree();
}

function confirm_service(){
	
	$('#confirm-service').hide();
	
    $.ajax({
        type : "POST",
        url : server + 'agent/confirm',        
        dataType : "json",
        data : {
        	request_id : request_id,
        	hms1: scode
        }
    }).done(function(response){        
    	if(response.state == 'ok'){
    		//assigned
    		$('#btn-aplico-wrap').hide();
    		$('#btn-entregado-wrap').show();
    		$('#btn-llego-wrap').show();
    		$('#verificacion-cod').html('Servicio en curso, Codigo Verificación: ' + request_id);
    		
    		verifyServiceStateDemonId = setInterval(verifyServiceState, verification_interval);
    		$.playSound('assets/audio/yes.mp3');
    	} else {
    		//taken by other one
    		$.playSound('assets/audio/not.mp3');
    		switchToFree();
    	}
    });	
}

function verifyServiceState(){
    $.ajax({
        type : "GET",
        url : server + 'api/verify_service_status',        
        dataType : "json",
        data : {
        	queryId : request_id,
        	demonId : verifyServiceStateDemonId
        }
    }).done(function(response){
        if(response.state == 'error'){
        	clearInterval(verifyServiceStateDemonId);
        	$.playSound('assets/audio/not.mp3');
        	alert(response.msg);
        	switchToFree();
        }        
    });	
}

function updateLocation(){
    $.ajax({
        type : "GET",
        url : server + 'agent/update_location',        
        dataType : "json",
        data : {
        	lat : lat,
        	lng : lng
        } 
    } ).done(function(response){
            if(response.state != 'ok'){
                $('#current-position').val('Si actualizar coordenadas');
            }
        }); 
}


function localizame() {
    if (navigator.geolocation) { 
        navigator.geolocation.getCurrentPosition(coords, errores);
    }else{
    	$('#current-position').val('No hay soporte para la geolocalización.');
    }
}

function coords(position) {
    lat = position.coords.latitude;
    lng = position.coords.longitude;   
    $('#current-position').val('Latitud: ' + lat + ' Longitud: ' + lng);
}

function errores(err) {
    /*Controlamos los posibles errores */
    if (err.code == 0) {
    	$('#current-position').val("Error en la geolocalización");
    }
    if (err.code == 1) {
    	$('#current-position').val("Para utilizar esta aplicación por favor aceptar compartir tu posición gegrafica.");
    }
    if (err.code == 2) {
    	$('#current-position').val("No se puede obtener la posición actual desde tu dispositivo");
    }
    if (err.code == 3) {
    	$('#current-position').val("Hemos superado el tiempo de espera. Vuelve a intentarlo");
    }
}


function get_sec_code(){
    $.ajax({
        type : "GET",
        url : server + 'api/scode',        
        dataType : "json",
        data : {}
    }).done(function(response){
    	$.mobile.loading( "hide" );
    	if(response.state == 'ok'){
    		scode = response.code;
    	}else{
    		$('#popupBasic').html('No hay conexión al servidor, intente de nuevo mas tarde.');
    		$('#popupBasic').popup();
    	}
    });	
    
}