<!doctype html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=9">
    
	<title><?= $this->config->item('app_name') ?></title>

	<link rel="stylesheet" href="<?=base_url()?>assets/css/app.css" />
  	<link rel="stylesheet" href="<?=base_url()?>assets/css/jquery.mobile-1.3.2.min.css" />
 
    <script src="<?=base_url()?>assets/js/jquery-1.10.2.min.js"></script>
    <script src="<?=base_url()?>assets/js/jquery.mobile-1.3.2.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script> 

  	<script>
 		var lang = '<?=current_lang()?>';
 		var verification_interval = <?=ci_config('verification_interval')?>;
 		var app_country = '<?=ci_config('app_country')?>';
 		//var app_country ='';
 		
 		var searching_msg = '<h1><?=lang('dashboard.searching')?></h1>';
 		var msg_cancel_service = '<?=lang('dashboard.cancel_service')?>';
 		var msg_nomenclature = '<?=lang('dashboard.nomenclature')?>';
 		var msg_nomenclature_empty = '<?=lang('dashboard.nomenclature_empty')?>';
 		var msg_configure_device = '<?=lang('dashboard.configure_device')?>';
 		var msg_error_attempts = '<?=lang('dashboard.error_attempts')?>';
 		var msg_address_not_found = '<?=lang('dashboard.address_not_found')?>';
 		var msg_error_geolocation = '<?=lang('dashboard.error_geolocation')?>';
 		var msg_error_share_position = '<?=lang('dashboard.error_share_position')?>';
 		var msg_error_current_position = '<?=lang('dashboard.error_current_position')?>';
 		var msg_error_exceeded_timeout = '<?=lang('dashboard.error_exceeded_timeout')?>';
 		var average = 'CALL';
 		var uuid 	= '';
 		var idcall  = '<?=$idcall?>';

 	</script>

 	<script src="<?=base_url()?>assets/js/toribio.js"> </script>
</head>
 
<body>


<div data-role="page" id="page1">
    <div data-theme="e" data-role="header">
    	<a id="btn-localizame" data-role="button"  data-theme="a" class="ui-btn-left"  ><?=lang('dashboard.localizame')?></a>
    	
    	<h3><?= $this->config->item('app_name') ?></h3>
        
    	<a id="agent-call" data-role="button" data-theme="a" href="#call-modal" class="ui-btn-right" data-rel="dialog" data-transition="pop" ><?=lang('dashboard.calltaxi')?></a>
    	
       	<?= form_open('api/call', array('id' => 'call-form', 'class' => '')) ?>
       		<input id="idlocation" 	name="idlocation" 	type="hidden" value="">
       		<input id="idclient" 	name="idclient" 	type="hidden" value="">
			<input id="lat" 		name="lat" 			type="hidden" value="">
			<input id="lng" 		name="lng" 			type="hidden" value="">
			<input id="zone" 		name="zone" 		type="hidden" value="">
			<input id="city" 		name="city" 		type="hidden" value="">
			<input id="state_c" 	name="state_c" 		type="hidden" value="">
			<input id="country" 	name="country" 		type="hidden" value="">
            
            <div data-role="fieldcontain">
            	<table border=0 width="100%"><tbody>
            	<tr>
	        		<td >
	        		   <input name="phone-client" id="phone-client" value="" type="text" data-mini="true" placeholder="Buscar por número tefónico" onkeydown="return searchUserTelephone(event)" >
					</td>
	            	<td >
						<input name="name-client" id="name-client" value="" type="text" data-mini="true" placeholder="Nombre cliente">
	            	</td>
	            	<td >
						<input name="cell-client" id="cell-client" value="" type="text" data-mini="true" placeholder="Celular cliente">
	            	</td>
	            	<td >
	            	
	            	<select name="select-location" id="select-location"  data-native-menu="false" onchange="centerCustLocation(this.value,'S')" > 
				        <option value="-1">Todas</option>
				    </select>
				    
				    </td>
	            </tr>
	            </tbody></table>
	            <hr>
            	<table border=0 width="100%"><tbody>
            	<tr>
	        		<td width="75%">
	        			<input name="address" id="address" value="" type="text" data-mini="true" onkeydown="return validarEnter(event)">
	        		</td>
	            	<td width="25%">
						<a href="#" id='btn-address-search'  align="left" data-role="button" data-icon="search" data-iconpos="notext" data-theme="c" data-inline="true">Buscar por dirección</a>
	        			<a href="#" id='btn-add'  align="left" data-role="button" data-icon="plus" data-iconpos="notext" data-theme="c" data-inline="true">Adiccionar</a>
	        			<a href="#" id='btn-save'  align="left" data-role="button" data-icon="check" data-iconpos="notext" data-theme="c" data-inline="true">Grabar</a>
	        			<a href="#" id='btn-del'  align="left" data-role="button" data-icon="delete" data-iconpos="notext" data-theme="c" data-inline="true">Borrar</a>	                	
			   		</td>
	               
	            </tr>
	            </tbody></table>

	            <table border=0 width="100%"><tbody>
	            <tr>
	        		<td width="75%">
						<select name="select-unidad" id="select-unidad"  data-native-menu="true"  > 
		                    <option value="-1">Unidades</option>
		                </select>
		                <input name="time-agent" id="time-agent" value="" type="text" data-mini="true" placeholder="Tiempo de llegada">
	            	</td>
	            	
	                <td  width="25%">
	                	<a id="btn-send-sms" data-role="button" data-theme="a" href="#" >Enviar SMS</a>
	                </td>
	            </tr>

                </tbody></table>
            </div>    		
    	 </form>        
    </div>
    
    <div data-role="content" class="padding-0">
         <div id="map_canvas"></div>

    </div>
  
        
    
    <div id="sound_"></div>    
</div>

<!-- Start of third page: #popup -->
<div data-role="page" id="call-modal" data-close-btn="none">
	<div id="confirm-wrapper">
		<div data-role="header" data-theme="e">
			<h1><?=lang('dashboard.callconfirm.title')?></h1>
		</div><!-- /header -->
	
		<div data-role="content" data-theme="d">	
			<div id="confirmation-msg"><p><?=lang('dashboard.callconfirm.content')?>: <span id="show-address"></span></p></div>	
			<div id="waiting-msg"><h1><?=lang('dashboard.searching')?></h1></div>
		</div><!-- /content -->
		
		<p>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="call-cancelation"><?=lang('dashboard.cancel')?></a>
		    <a href="#" data-role="button" data-mini="true" data-inline="true" data-icon="check" data-theme="b" id="call-confirmation"><?=lang('dashboard.confirm')?></a>
		</p>	
	</div>
	
	<div id="agent-wrapper">
		<div data-role="header" data-theme="e">
			<h1><?=lang('dashboard.assinged')?></h1>
		</div><!-- /header -->
	
		<div data-role="content" data-theme="d">	
			<p><?=lang('dashboard.confimationcode')?>: <span id="confirmation-code"></span></p>
			<p id="agent-photo"></p>
			<p id="agent-name"></p>
			
			<p>
			<?=lang('dashboard.agentcode2')?>: <span id="agent-placa"></span>,&nbsp;
			<?=lang('dashboard.unidad')?>: <span id="agent-unidad"></span>
			</p>
			<p><?=lang('dashboard.agentphone')?>: <span id="agent-phone"></span></p>
			
		</div><!-- /content -->
		
		<p>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="btn-back">Regresar</a>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="query-cancelation"><?=lang('dashboard.cancel')?></a>
		</p>
	</div>
</div><!-- /page popup -->


<!-- 
<audio id="yes" src="assets/audio/yes.mp3" preload="auto"></audio>
<audio id="not" src="assets/audio/not.mp3" preload="auto"></audio>
<audio id="ring" src="assets/audio/ring.mp3" preload="auto"></audio>
 -->
<audio id="pito" src="<?=base_url()?>assets/audio/pito.mp3" preload="auto"></audio>
<audio id="yes" src="<?=base_url()?>assets/audio/yes.mp3" preload="auto"></audio>



</body>
</html>