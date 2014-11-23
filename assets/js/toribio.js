var http = location.protocol;
var slashes = http.concat("//");
var server = slashes.concat(window.location.hostname);

var directionsService = new google.maps.DirectionsService();

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
var latitud;
var longitud;
var latitudOriginal;
var longitudOriginal;
var geocoder = new google.maps.Geocoder();
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var page_state  = 'dashboard';



var markersArray = [];

//$(document).keypress(function(e){
 // if (e.which === 49) {
  	//hola();
//  }
//});

$(document).ready(function() {
    
   get_all_units('-1');
   

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

    $('#btn-del').click(function (e){
        e.preventDefault();
        deleteClientLoc();
    });


    $('#btn-back').click(function(e){
        e.preventDefault();
        clearInterval(verifyServiceStatus);
        reset_modal();
        $("#call-modal").dialog('close');

    });

    $('#waiting-msg, #agent-wrapper, #agent-call2-wrapper').hide();
    
    localizame(); /*Cuando cargue la pÃ¡gina, cargamos nuestra posiciÃ³n*/ 
    
    $('#address').change(function(e){
        
        $('#show-address').html($(this).val());
    
    });
    
    $('#calling-agent').click(function (e){
        e.preventDefault();
    });
    
    $('#agent-confirmation').click(function(e){
        $.ajax({
            type : "GET",
            url : server + '/' + lang + '/api/agent_accept',           
            dataType : "json",
            data : {
                queryId : queryId
            }
        }).done(function(response){
            reset_modal();
        });
        
    });
    
    $('#call-cancelation').click(function (e){
        cancel_service();
    });
    
	$('#query-cancelation').click(function (e){
        if (confirm(msg_cancel_service))
        {
            cancel_service();
        }
    });
    
    function cancel_service(){
        if(!queryId){
            reset_modal();
            return true;
        }
            
        $.mobile.loading("hide");
        clearInterval(demonId);
        clearInterval(verifyServiceStatus);
        
        reset_modal();
        
        if(taxiMarker){
            taxiMarker.setMap(null);
            taxiMarker = null;
        }
                
        $.ajax({
            type : "GET",
            url : server + lang + '/api/request_cancel',           
            dataType : "json",
            data : {
                queryId : queryId
            }
        }).done(function(response){});

    }


    function trim(myString)
    {
        return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
    }
    
    $('#call-confirmation').click(function(e){
       
        if ($('input[name="address"]').val()!=''){  

                if ( ($('input[name="lat"]').val()!='') && ($('input[name="lat"]').val()!='0') ){   
               
                    page_state  = 'call';
                    $.mobile.loading("show");
                    $('#call-confirmation, #confirmation-msg').hide();
                    $('#waiting-msg').show();
                  
                    $.ajax({
                        type : "GET",
                        url : server + '/' + lang + '/api/call',        
                        dataType : "json",
                        data : {
                            hms1 	: $('input[name="hms1"]').val(),
                            address : $('input[name="address"]').val(),
                            lat 	: $('input[name="lat"]').val(),
                            lng 	: $('input[name="lng"]').val(),
                            zone 	: $('input[name="zone"]').val(),
                            city 	: $('input[name="city"]').val(),
                            country : $('input[name="country"]').val(),
                            state_c : $('input[name="state_c"]').val(),
                            name    : $('input[name="name-client"]').val(),
                            phone   : $('input[name="phone-client"]').val(),
                            cell    : $('input[name="cell-client"]').val(),
                         	average : average,
                         	uuid    : '',
                            idcall  : idcall   
                        }
                    }).done(function(response){
                        if(response.queryId > 0){
                            queryId = response.queryId;
                            demonId = setInterval(verifyCall, verification_interval);
                        }else{
                            page_state  = 'dashboard';
                            alert(msg_error_attempts);
                        }
                    });
                    
            }else{
                alert(msg_configure_device);
            }
        
      }else{
        alert(msg_nomenclature_empty);
      }
    });
    
    $('#btn-localizame').click(function(e){
        e.preventDefault();
        setUserIcon(latitudOriginal, longitudOriginal);
    });    
    
    $('#agent-call').click(function(e){
        clearInterval(taxiLocationDemonId);
    });
    
    
    $('#btn-address-search').click(function(e){
        e.preventDefault();
        address_search();
    });

    $('#btn-send-sms').click(function(e){
        e.preventDefault();
        send_sms($('#select-unidad').val(),$('#time-agent').val(),'ALL');
    });

    $('#select-unidad').click(function(e){
        e.preventDefault();
        $('#time-agent').val('');
    });

});


function send_sms(idagent,arrival_time,destination){
    if ((idagent>0)&&(!arrival_time=='')){  
        $.ajax({
            type : "GET",
            url : server + '/' + lang + '/api/send_sms' ,        
            dataType : "json",
            data : {
                idagent : idagent,
                time    : arrival_time,
                address : $('#address').val(),
                name    : $('#name-client').val(),
                phone   : $('#phone-client').val(),
                cell    : $('#cell-client').val(),
                destination : destination,
                cachehora : (new Date()).getTime()
            }
        }).done(function(response){
            if(response.state == 'ok'){
                alert('Enviado menasje...');
            }else
                alert('ERROR al enviar le mensaje. Por favor intentelo de nuevo.');

        });
    }else{
        alert('Para poder enviar el mensaje debe seleccionar la unidad y el tiempo de llegada. Por favor intente de nuevo');
    }
  
}


function get_all_units(idsucursal){
    $('option', '#select-unidad').remove();
    $("option","#select-unidad" ).empty();

    $.ajax({
            type : "GET",
            url : server + '/' + lang + '/api/get_all_units' ,        
            dataType : "json",
            data : {
                idsucursal : idsucursal
            }
    }).done(function(response){
        if(response.state == 'ok'){
            $('#select-unidad').append('<option value="-1">Unidades</option>');
            for(var i in response.result){
                $('#select-unidad').append('<option value="'+response.result[i].id+'" >'+response.result[i].unidad+' '+response.result[i].nombre+'</option>');
            }
          
        }
    });
  
}

function searchUserTelephone(e) {
    if (window.event) {
        keyval=e.keyCode
    } else 
        if (e.which) {
            keyval=e.which
        } 
    if (keyval=="13") {
        e.preventDefault();
        resetCall(); 
        getSelectCustLocation();
    } 
}

function resetCall(){
    if(!queryId){
        reset_modal();
        return true;
    }
            
    $.mobile.loading("hide");
    clearInterval(demonId);
    clearInterval(verifyServiceStatus);
        
    reset_modal();
        
    if(taxiMarker){
        taxiMarker.setMap(null);
        taxiMarker = null;
    }
}


function deleteClientLoc(){
    
    if ($('#idlocation').val()!=''){  
        if (confirm('Esta seguro que desea borrar la ubicación ' + $('#address').val() + '?'))
        {

            $.ajax({
                type : "GET",
                url  : server + '/' + lang + '/api/deleteClientLoc',        
                dataType : "json",
                data : {
                    id      : $('input[name="idlocation"]').val()
                }
            }).done(function(response){
                if(response.state == 'ok'){
                        
                    alert('Ubicación del usuario se borró con exito.');
                    
                    resetCall(); 
                    getSelectCustLocation();
                }
            }).fail(function(jqXHR, textStatus, errorThrown){
                    alert('Error al intentar borrar la ubicación, por favor intente de nuevo. '+errorThrown);
            }); 
        }
        
    }else{
            alert("Debe seleccionar primero una ubicación del cliente.");
            $('#phone-client').focus();
    }

};

function saveClientLoc(){
 	if ($('input[name="phone-client"]').val()!=''){  

 		if ($('input[name="address"]').val()!=''){  

    		$.ajax({
            	type : "GET",
                url  : server + '/' + lang + '/api/saveClientLoc',        
                dataType : "json",
                data : {
                	id 		: $('input[name="idlocation"]').val(),
                	idclient: $('input[name="idclient"]').val(),
                    name 	: $('input[name="name-client"]').val(),
                    phone 	: $('input[name="phone-client"]').val(),
                    cell 	: $('input[name="cell-client"]').val(),
                    address : $('input[name="address"]').val(),
                    lat 	: $('input[name="lat"]').val(),
                    lng 	: $('input[name="lng"]').val(),
                    zone 	: $('input[name="zone"]').val(),
                    city 	: $('input[name="city"]').val(),
                    country : $('input[name="country"]').val(),
                    state_c : $('input[name="state_c"]').val()
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
			$('#show-address').html(markersArray[i].title);
			latitud = markersArray[i].latitud;
    		longitud = markersArray[i].longitud;
    		$('#lat').val(markersArray[i].latitud);
    		$('#lng').val(markersArray[i].longitud);
    		$('#idlocation').val(markersArray[i].id);
    		$('#idclient').val(markersArray[i].idcliente);
    		$('#zone').val(markersArray[i].sector);
            $('#city').val(markersArray[i].ciudad);
            $('#state_c').val(markersArray[i].departamento);
            $('#country').val(markersArray[i].pais);
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
    	sector   : result.sector,
    	ciudad   : result.ciudad,
    	departamento   : result.departamento,
    	pais   : result.pais,
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

//-----------------------------

var demonId;
var queryId;
var verifyServiceStatus;
var taxiLocationDemonId;
var agentId;
var taxiMarker;
var userMarker;

function play_sound(element) {
        document.getElementById(element).play();
}
   

function validarEnter(e) {
    if (window.event) {
        keyval=e.keyCode
    } else 
        if (e.which) {
            keyval=e.which
        } 
    if (keyval=="13") {
        e.preventDefault();
        address_search();
    } 
}

function getTaxiLocation(){
       $.ajax({
            type : "GET",
            url : server + '/' + lang + '/api/get_taxi_location',        
            dataType : "json",
            data : {
                agent_id : agentId,
                queryId  : queryId,
                cachehora : (new Date()).getTime()
            }
        }).done(function(response){
            if(response.state == 'ok'){
                setTaxiIcon(response.lat, response.lng);
            }
        });
       
}

function setTaxiIcon(lat, lng){
    if(taxiMarker){
        taxiMarker.setPosition( new google.maps.LatLng( lat, lng ) );
    }else{
        taxiMarker = new google.maps.Marker({
            position: new google.maps.LatLng( lat, lng ),
            map: map,
            icon : server +'/assets/images/taxi.png'
        });
        
        tracerRoute(lat, lng, latitud, longitud);
    }
    

}

function tracerRoute(lat, lng, lat2, lng2){
    //para el calculo de la ruta
    var rendererOptions = {
          map: map,
          suppressMarkers : true
        }
    
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
    directionsDisplay.setMap(map);
 
    var request = {
      origin:  new google.maps.LatLng(lat2, lng2),
      destination:new google.maps.LatLng(lat, lng),
      
      travelMode: google.maps.DirectionsTravelMode.DRIVING
    };

    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        }
    });

}


function setUserIcon(lat, lng){
    var latlon = new google.maps.LatLng(lat, lng);
    codeLatLng(lat, lng);
    userMarker.setPosition(latlon);
    map.setCenter(latlon); 

    latitud = lat;
    longitud = lng;
    $('#lat').val(lat);
    $('#lng').val(lng);
    
}

function reset_modal(){
    $('#confirm-wrapper').show();
    $('#waiting-msg').html(searching_msg);
    $('#waiting-msg').hide();
    $('#call-confirmation').show();
    
    $('#confirmation-msg').show();
    $('#agent-wrapper').hide();

    $('#agent-call2-wrapper').hide();
    $('#agent-call-wrapper').show();

}

function verifyCall(){
    $.ajax({
        type : "GET",
        url : server + '/' + lang + '/api/verify_call',        
        dataType : "json",
        data : {
            queryId : queryId,
            demonId : demonId,
            cachehora : (new Date()).getTime()
        }
    }).done(function(response){
        
        if(response.state == 'error'){
            page_state  = 'dashboard';
            $.mobile.loading("hide");
            clearInterval(demonId);
            $('#waiting-msg').html(response.msg);
        }
         
        if(response.state == '1'){
            page_state  = 'call';
            $('#agent-photo').html('<img  style="imagenagent" src="' + response.agent.foto + '"/>');
            $('#agent-name').html(response.agent.nombre);
            agentId = response.agent.id
            $('#agent-id').html(response.agent.codigo);
            $('#agent-phone').html(response.agent.telefono);
            $('#confirmation-code').html('<span style="color: red; font-weight:bold;">' + queryId + '</span>');
            $('#agent-placa').html(response.agent.placa);
            $('#agent-unidad').html(response.agent.unidad);
            
            $('#confirm-wrapper').hide();
            $('#agent-wrapper').show();
            
            $.mobile.loading("hide");
            
            play_sound('yes'); 

            clearInterval(demonId);
            verifyServiceStatus = setInterval(verifyServiceState, verification_interval);
            
            send_sms(response.agent.id,'5 min.','USER');
            
        }
    });
}

function verifyServiceState(){
    $.ajax({
        type : "GET",
        url : server + '/' + lang + '/api/verify_service_status',        
        dataType : "json",
        data : {
            queryId : queryId,
            demonId : verifyServiceStatus,
            cachehora : (new Date()).getTime()
        }
    }).done(function(response){
        
        if(response.state == 'error'){
            page_state  = 'dashboard';
            clearInterval(verifyServiceStatus);
            alert(response.msg);
            reset_modal();
            $("#call-modal").dialog('close');
        }
        
        if(response.state == 'arrival'){
            //$.playSound('/assets/audio/ring.mp3');
            play_sound('pito'); 
			updateStatusArribo();
            alert(response.msg);
            
        }

        if(response.state == 'delivered'){
            //hacer llamado a la pantalla de encuesta
            page_state  = 'dashboard';
            clearInterval(verifyServiceStatus);
            clearInterval(taxiLocationDemonId);
            reset_modal();
            $("#call-modal").dialog('close');
            if(taxiMarker){
                        taxiMarker.setMap(null);
                        taxiMarker = null;
            }      
            if(directionsDisplay != null) { 
                        directionsDisplay.setMap(null);
                        directionsDisplay = null; 
            }          
        }

    }); 
}



function updateStatusArribo(){
    $.ajax({
        type : "GET",
        url : server + '/' + lang + '/api/updateStatusArribo',        
        dataType : "json",
        data : {
            queryId : queryId,
            demonId : verifyServiceStatus,
            cachehora : (new Date()).getTime()
        }
    }).done(function(response){
      
    }); 
}


function localizame() {
    if (navigator.geolocation) { /* Si el navegador tiene geolocalizacion */
        navigator.geolocation.getCurrentPosition(coordenadas, errores);
    }else{
        alert(msg_error_geolocation);
    }
}

function coordenadas(position) {
    latitud = position.coords.latitude; /*Guardamos nuestra latitud*/
    longitud = position.coords.longitude; /*Guardamos nuestra longitud*/
    latitudOriginal  = latitud;
    longitudOriginal = longitud;
    
    codeLatLng(latitud, longitud);

    $('#lat').val(latitud);
    $('#lng').val(longitud);
    
    cargarMapa();
}



function errores(err) {
    /*Controlamos los posibles errores */
    if (err.code == 0) {
      alert(msg_error_geolocation);
    }
    if (err.code == 1) {
      alert(msg_error_share_position);
    }
    if (err.code == 2) {
      alert(msg_error_current_position);
    }
    if (err.code == 3) {
      alert(msg_error_exceeded_timeout);
    }
}
 

function address_search() {
 var address = app_country+','+document.getElementById("address").value;
 geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
                
        latitud=results[0].geometry.location.lat();
        longitud=results[0].geometry.location.lng();
        
        codeLatLng(latitud, longitud);
       
        $('#lat').val(latitud);
        $('#lng').val(longitud);
        
        cargarMapa();

    } else {
        alert(msg_error_geolocation);
    }
 });
}

function cargarMapa() {
    var latlon = new google.maps.LatLng(latitud,longitud); /* Creamos un punto con nuestras coordenadas */
    var myOptions = {
        zoom: 15,
        center: latlon, /* Definimos la posicion del mapa con el punto */
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL}, 
        mapTypeControl: true, 
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles : styles

    };/* HYBRID  Configuramos una serie de opciones como el zoom del mapa y el tipo.*/

    map = new google.maps.Map($("#map_canvas").get(0), myOptions); /*Creamos el mapa y lo situamos en su capa */

    
    var coorMarcador = new google.maps.LatLng(latitud,longitud); /*Un nuevo punto con nuestras coordenadas para el marcador (flecha) */

    /*Creamos un marcador*/             
    userMarker = new google.maps.Marker({
        position: coorMarcador, /*Lo situamos en nuestro punto */
        map: map, /* Lo vinculamos a nuestro mapa */
        animation: google.maps.Animation.DROP, 
        draggable: true,
        icon : server + '/assets/images/male.png'
    });

    google.maps.event.addListener(userMarker, "dragend", function(evento) {
       
        latitud = evento.latLng.lat();
        longitud = evento.latLng.lng();
            
        codeLatLng(evento.latLng.lat(), evento.latLng.lng());
       
       // console.log(coorMarcador);
        $('#lat').val(evento.latLng.lat());
        $('#lng').val(evento.latLng.lng());
    }); 


}

var calle = '';
var ruta = '';
var sector = null;
var ciudad = '';
var pais = '';
var depto = ''; 
var formatted_addr = '';

function codeLatLng(lat, lng) {

    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                //var tam = results[0].address_components.length;
                sector = results[0].address_components[2] ;
                
                for (var i = 0; i < results[0].address_components.length; i++)
                {
                    var addr = results[0].address_components[i];
                    if (addr.types[0] == 'country') 
                      pais = addr.long_name;
                    if (addr.types[0] == 'administrative_area_level_1') 
                      depto = addr.long_name;
                    if (addr.types[0] == 'locality') 
                      ciudad = addr.long_name;
                    //if (addr.types[0] == 'sublocality_level_1') 
                      //sector = addr.long_name;
                    if (addr.types[0] == 'route') 
                      ruta = addr.long_name;
                    if (addr.types[0] == 'street_number') 
                      calle = addr.long_name;
                    //console.log('address: '+addr.types[0]+' - '+addr.long_name)
                }
                
                formatted_addr = sector.long_name + ', ' + results[0].formatted_address;
                var guion = formatted_addr.indexOf("-");
                if (guion>0) {
                    formatted_addr = formatted_addr.substring(0, guion) + ' - ';
                } else{
                    formatted_addr = sector.long_name + ', ' + results[0].address_components[1].long_name + ' # ' +results[0].address_components[0].long_name;
                }

                $('#address').val(formatted_addr);
                $('#show-address').html(formatted_addr);
                $('#zone').val(sector.long_name);
                $('#city').val(ciudad);
                $('#state_c').val(depto);
                $('#country').val(pais);
                    
            } else {
                $('#address').val(msg_address_not_found);
            }
            
        } else {
            //$('#address').val("Fallo en las Appis de Google : "+ status);
        }
    });
}


