var server = 'http://aplico.dev/es/';

var lat = lng = deslat = destlng = 0;
var scode = null;
var user = null;
var localizationDemonId;
var updateLocationDemonId;
var verification_interval = 5000;
var verifyServiceDemonId;
var geoOptions = { timeout: verification_interval };
var ubicacionServicio = null;
var request_id = null;

$(document).ready(function() {
	
	$.mobile.loading( "show" );
	
	$("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap").hide();
	
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
	
});


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
        }
    });
}

function cancel_service(){
	
	request_id = null;
	
	$('#service-addr').html('');
	$('#verificacion-cod').html('');
	
	$("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap").hide();
		
	verifyServiceDemonId = setInterval(verifyService, verification_interval);
	
    $.ajax({
        type : "POST",
        url : server + 'agent/switch_to_free',        
        dataType : "json",
        data : {
        	hms1: scode
        }
    }).done(function(response){});	
	
}

function service_delivered(){

    $.ajax({
        type : "POST",
        url : server + 'agent/delivered_service',        
        dataType : "json",
        data : {
        	request_id : request_id,
        	hms1: scode
        }
    }).done(function(response){});	

	cancel_service();
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
    		$('#verificacion-cod').html('Servicio en curso, Codigo Verificación: ' + request_id);
    	} else {
    		//taken by other one
    		cancel_service();
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
        },
        success: function(response){},
        error: function (e, state, msg){
        	$('#current-position').val("Sin datos");
        }
    });
}


function localizame() {
    if (navigator.geolocation) { 
        navigator.geolocation.getCurrentPosition(coords, errores);
    }else{
    	$('#current-position').val('Oops! no hay soporte para la geolocalización.');
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
    	$('#current-position').val("Oops! Algo ha salido mal");
    }
    if (err.code == 1) {
    	$('#current-position').val("Oops! No has aceptado compartir tu posición");
    }
    if (err.code == 2) {
    	$('#current-position').val("Oops! No se puede obtener la posición actual");
    }
    if (err.code == 3) {
    	$('#current-position').val("Oops! Hemos superado el tiempo de espera");
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