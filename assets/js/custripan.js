//var directionsService = new google.maps.DirectionsService();

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
var geocoder = new google.maps.Geocoder();
var markersArray = [];
var limits = new google.maps.LatLngBounds();

$(document).ready(function() {
    getSelectSucursal();
    localizame(); /*Cuando cargue la pÃ¡gina, cargamos nuestra posiciÃ³n*/ 

    $('#btn-cosultar').click(function (e){
        e.preventDefault();
        deleteOverlays()
        clearInterval(taxiLocationDemonId);
        taxiLocationDemonId = setInterval(getTaxiLocation, 10000);
        getTaxiLocation();
    });

   

});

var taxiLocationDemonId;
var taxiMarker;
   
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

function getSelectSucursal(){
    $('option', '#select-sucursal').remove();
    $("option","#select-sucursal" ).empty();

    $('option', '#select-unidad').remove();
    $("option","#select-unidad" ).empty();
    $('option', '#select-placa').remove();
    $("option","#select-placa" ).empty();
    $('option', '#select-taxista').remove();
    $("option","#select-taxista" ).empty();

    $.ajax({
            type : "GET",
            url : lang + '../../../api/select_sucursales' ,        
            dataType : "json",
            data : {}
    }).done(function(response){
        if(response.state == 'ok'){
            $('#select-sucursal').append('<option value="-1">Todas</option>');
            for(var i in response.result){
                $('#select-sucursal').append('<option value="'+response.result[i].id+'" >'+response.result[i].nombre+'</option>');
            }
                
        }
    });
       
}

function get_all_units(idsucursal){
    $('option', '#select-unidad').remove();
    $("option","#select-unidad" ).empty();
    $('option', '#select-placa').remove();
    $("option","#select-placa" ).empty();
    $('option', '#select-taxista').remove();
    $("option","#select-taxista" ).empty();
   
    $.ajax({
            type : "GET",
            url : lang + '../../../api/get_all_units' ,        
            dataType : "json",
            data : {
                idsucursal :  idsucursal
            }
    }).done(function(response){
        if(response.state == 'ok'){
            $('#select-unidad').append('<option value="-1">Todas</option>');
            $('#select-placa').append('<option value="-1">Todas</option>');
            $('#select-taxista').append('<option value="-1">Todas</option>');
            for(var i in response.result){
                $('#select-unidad').append('<option value="'+response.result[i].unidad+'" >'+response.result[i].unidad+'</option>');
                $('#select-placa').append('<option value="'+response.result[i].placa+'" >'+response.result[i].placa+'</option>');
                $('#select-taxista').append('<option value="'+response.result[i].id+'" >'+response.result[i].nombre+'</option>');
            }
                
        }
    });

  
}



function getTaxiLocation(){
     
   $('#select-unidad').val(),$('#select-placa').val(),$('#select-taxista').val()
     
   $.ajax({
        type : "GET",
        url : lang + '../../../api/get_agets_location',        
        dataType : "json",
        data : {
            office : $('#select-sucursal').val(),
            unit   : $('#select-unidad').val(),
            plate  : $('#select-placa').val(),
            agent  : $('#select-taxista').val()
        }
    }).done(function(response){

        if(response.state == 'ok'){
            
            var coordenadas;
            var estadoagent;

            deleteOverlays();

            for(var i in response.agent){
              
                if(response.agent[i].fecha_localizacion>response.agent[i].datesytem)
                    estadoagent = 0
                else
                    estadoagent = 1
                coordenadas =  new google.maps.LatLng( response.agent[i].latitud, response.agent[i].longitud);

                setTaxiIcon(coordenadas, response.agent[i],estadoagent );

                // limits.extend(coordenadas);
            }
            
        }
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

function setTaxiIcon(coordenadas, agent, estadoagent){
    var popup;
    var icon_taxi
   
    if(estadoagent==1)
        icon_taxi =  app_path + 'assets/images/taxi2.png';
    else
        icon_taxi =  app_path + 'assets/images/taxi.png';

    taxiMarker = new google.maps.Marker({
        position:coordenadas,
        map: map,
        icon : icon_taxi
    });
    markersArray.push(taxiMarker);
            
    google.maps.event.addListener(taxiMarker, 'click', function(){
            if(!popup){
                popup = new google.maps.InfoWindow();
            }
            var note = 'Placa : ' + agent.placa + '<br> Taxista : ' + agent.nombre + 
                        '<br> Telefono : ' + agent.telefono + '<br> Estado : ' + agent.estado_servicio +
                        '<br> Ultima Act. : ' + agent.fecha_localizacion;
            popup.setContent(note);
            popup.open(map, this);
        });    
      
}



function localizame() {
    if (navigator.geolocation) { /* Si el navegador tiene geolocalizacion */
        navigator.geolocation.getCurrentPosition(coordenadas, errores);
    }else{
        alert('No hay soporte para la geolocalización.');
    }
}

function coordenadas(position) {
    latitud = position.coords.latitude; /*Guardamos nuestra latitud*/
    longitud = position.coords.longitude; /*Guardamos nuestra longitud*/
    cargarMapa();
}



function errores(err) {
    /*Controlamos los posibles errores */
    if (err.code == 0) {
      alert("Error en la geolocalización.");
    }
    if (err.code == 1) {
      alert("No has aceptado compartir tu posición.");
    }
    if (err.code == 2) {
      alert("No se puede obtener la posición actual.");
    }
    if (err.code == 3) {
      alert("Hemos superado el tiempo de espera. Vuelve a intentarlo.");
    }
}
 


function cargarMapa() {
    var latlon = new google.maps.LatLng(latitud,longitud); /* Creamos un punto con nuestras coordenadas */
    var myOptions = {
        zoom: 12,
        center: latlon, /* Definimos la posicion del mapa con el punto */
        navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL}, 
        mapTypeControl: true, 
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles : styles

    };/* HYBRID  Configuramos una serie de opciones como el zoom del mapa y el tipo.*/

    map = new google.maps.Map($("#map_canvas").get(0), myOptions); /*Creamos el mapa y lo situamos en su capa */


}

