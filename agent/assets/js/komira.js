var http = location.protocol;
var slashes = http.concat("//");
//var server = slashes.concat(window.location.hostname) + '/aplico/es/';
var server = slashes.concat(window.location.hostname) + '/es/';

var lat = lng = deslat = destlng = 0;
var scode = null;
var user = null;
var localizationDemonId;
var updateLocationDemonId;
var verification_interval = null;
var updatelocation_interval = null;
var verifyServiceDemonId;
var verifyServiceStateDemonId;
var geoOptions = { timeout: verification_interval };
var ubicacionServicio = null;
var request_id = null;
var username = null;
var password = null;
var switchBgDemon = null;
var lat_user = null;
var lng_user = null;

var styles = [
                  {
                        "featureType": "poi",
                        "stylers": [
                          { "visibility": "off" }
                        ]
                      },{
                        "featureType": "transit",
                        "stylers": [
                          { "visibility": "off" }
                        ]
                      },{
                        "featureType": "landscape.man_made",
                        "stylers": [
                          { "visibility": "off" }
                        ]
                      }
                    ];
    
var map;
//var latitud;
//var longitud;
//var geocoder = new google.maps.Geocoder();


$(document).ready(function() {
    
    $.mobile.loading( "show" );
    
    $("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap, #btn-llego-wrap").hide();
    
    init();
    
    $('#do-login').click(function(e){
        
        username = $('#username').val();
        password = $('#password').val();
        login(username, password);

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
    
    
    
});


$( document ).bind( "pageshow", function( event, data ){
google.maps.event.trigger(map_canvas, 'resize');
});

$(document).on('pagebeforeshow', '#maps-modal', function(){ 
    $('#map_canvas').css('width', '100%');
    $('#map_canvas').css('height', '300px');
    //console.log('cargando mapa');
    cargarMapa();
 });


function play_sound(element) {
        document.getElementById(element).play();
}

function login(id, key){

    clearInterval(localizationDemonId);
    clearInterval(updateLocationDemonId);
    clearInterval(verifyServiceDemonId);
    
        $.ajax({
            type : "GET",
            url : server + 'api/login',        
            dataType : "json",
            data : {
                username : id,
                password : key,
                hms1: scode
            }
        }).done(function(response){
            
            if(response.state=='ok'){
                $("#show-dashboard").trigger('click');
                user = response.data
                $('#agent-name').html(user.nombre);
                $('#agent-photo').attr('src', "../assets/images/agents/" + user.foto) ;
                
                localizame();
				updateLocation();
				verifyService();
                localizationDemonId = setInterval(localizame, verification_interval);
                updateLocationDemonId = setInterval(updateLocation, verification_interval);
                verifyServiceDemonId = setInterval(verifyService, verification_interval);
                
                $('#service-addr').val('');
            }else{
                alert(response.msg);
                //$('#popupBasic').html();
                //$('#popupBasic').popup();             
            }
        });     
}

function arrival_confirmation(){
    $.ajax({
        type : "GET",
        url : server + 'agent/arrival_confirmation',        
        dataType : "json",
        data : {
            request_id : request_id,
            hms1: scode
        }
    }).done(function(response){
        play_sound('pito');
    });  
    
}

function verifyService(){
    $.ajax({
        type : "GET",
        url : server + 'agent/get_service',        
        dataType : "json",
        data : {
            demonId : verifyServiceDemonId,
            lat : lat,
            lng : lng,
            hms1: scode
        },
        success: function(response){        
        	if(response.state == 'ok'){
            	request_id = response.request;
            	//destlat = response.latitud;
            	//destlng = response.longitud;
                lat_user = response.latitud;
                lng_user = response.longitud;
            	ubicacionServicio = response.ubicacion;
            
            	$('#service-addr').val(response.sector);
            	$('#btn-aplico-wrap').show();
            	$('#btn-cancelar-wrap').show();
            
            	clearInterval(verifyServiceDemonId);
            
            	//$.playSound('/assets/audio/ring.mp3');
                play_sound('ring'); 
            	switchBgDemon = setInterval(switchServiceAddrBg, 1000);
            	$('#service-addr').css('background-color', 'red');
            	
        	}
    	},
    	error: function (xhr, ajaxOptions, thrownError) {
        	//console.log(xhr);
        	login(username, password);
      }
    });
}

var bgColor = null;
function switchServiceAddrBg(){
	if(bgColor != 'red'){
		bgColor = 'red';
		$('#service-addr').css('background-color', 'red');
		$('#service-addr').css('color', 'white');
	}else{
		bgColor = 'white';
		$('#service-addr').css('background-color', 'white');
		$('#service-addr').css('color', 'black');		
	}
}

function switchToFree(){
    request_id = null;
    
    $('#service-addr').html('');
    $('#verificacion-cod').html('');
    
    $("#btn-aplico-wrap, #btn-entregado-wrap, #btn-cancelar-wrap, #btn-llego-wrap").hide();
        
    verifyServiceDemonId = setInterval(verifyService, verification_interval);
    clearInterval(verifyServiceStateDemonId);
    
    $.ajax({
        type : "GET",
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
    resetSrvAddrBg();
     
    $.ajax({
        type : "GET",
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
        type : "GET",
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

function resetSrvAddrBg(){
    clearInterval(switchBgDemon);
	bgColor = 'white';
	$('#service-addr').css('background-color', 'white');
	$('#service-addr').css('color', 'black');
	$('#service-addr').val('');
}

function confirm_service(){
    
    $('#confirm-service').hide();
    resetSrvAddrBg();
    
    $.ajax({
        type : "GET",
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
            $('#verificacion-cod').html('Su Código de Verificación: ' + request_id);
            $('#service-addr').val(ubicacionServicio);
            
            verifyServiceStateDemonId = setInterval(verifyServiceState, verification_interval);
            //$.playSound('assets/audio/yes.mp3');
             play_sound('yes'); 
        } else {
            //taken by other one
            //$.playSound('assets/audio/not.mp3');
             play_sound('not'); 
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
            //$.playSound('assets/audio/not.mp3');
            play_sound('not'); 
            alert(response.msg);
            switchToFree();
        }
    }); 
}

function updateLocation(){
	
	$('#current-position').parent().css('background-color', 'yellow');
	
    $.ajax({
        type : "GET",
        url : server + 'agent/update_location',        
        dataType : "json",
        timeout : 5000,
        data : {
            lat : lat,
            lng : lng
        },
        
    }).done(function(response){
    		$('#position-state').attr('src','assets/images/green_dot.png');
    		$('#current-position').parent().css('background-color', '#FFFFFF');
    		
            if(response.state != 'ok'){
                $('#current-position').val('-------------------------');
            }else{
            	$('#current-position').val('Latitud: ' + lat + ' Longitud: ' + lng);
            }
            
     }).fail(function(jqXHR, textStatus, errorThrown){
    	 $('#current-position').val('======= Error de conexión =======');
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
}

function cargarMapa() {
    var directionsDisplay = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();
    var latlon = new google.maps.LatLng(lat,lng); /* Creamos un punto con nuestras coordenadas */
    var myOptions = {
        zoom: 15,
        center: latlon, /* Definimos la posicion del mapa con el punto */
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL}, 
        mapTypeControl: true, 
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles : styles

    };/* HYBRID  Configuramos una serie de opciones como el zoom del mapa y el tipo.*/
    map = new google.maps.Map($("#map_canvas").get(0), myOptions); /*Creamos el mapa y lo situamos en su capa */
    
    /*Creamos un marcador AGENTE*/   
    agentMarker = new google.maps.Marker({
            position: new google.maps.LatLng( lat, lng ),
            map: map,
            icon : 'assets/images/taxi.png'
    });
    /*Creamos un marcador USUARIO*/   
    userMarker = new google.maps.Marker({
            position: new google.maps.LatLng( lat_user, lng_user ),
            map: map,
            icon : 'assets/images/male.png'
    });

    var rendererOptions = {
      map: map,
      suppressMarkers : true
    }
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions)

    var request = {
      origin:  new google.maps.LatLng( lat, lng ),
      destination:new google.maps.LatLng( lat_user, lng_user),
      
      travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        }
    });
    
}



function errores(err) {
    /*Controlamos los posibles errores */
    if (err.code == 0) {
        $('#current-position').val("Error en la geolocalización.");
    }
    if (err.code == 1) {
        $('#current-position').val("Para utilizar esta aplicación por favor aceptar compartir tu posición gegrafica.");
    }
    if (err.code == 2) {
        $('#current-position').val("No se puede obtener la posición actual desde tu dispositivo.");
    }
    if (err.code == 3) {
        $('#current-position').val("Hemos superado el tiempo de espera. Vuelve a intentarlo.");
    }
}


function init(){
    $.ajax({
        type : "GET",
        url : server + 'api/agent_init',        
        dataType : "json",
        data : {}
    }).done(function(response){
        $.mobile.loading( "hide" );
        if(response.state == 'ok'){
            scode = response.code;
            verification_interval = response.verification_interval;
            updatelocation_interval = response.updatelocation_interval;

        }else{
            $('#popupBasic').html('No hay conexión al servidor, intente de nuevo mas tarde.');
            $('#popupBasic').popup();
        }
    }); 
    
}