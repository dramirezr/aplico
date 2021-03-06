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
 		var average = '<?=$average?>';
 		var uuid = '<?=$uuid?>';
 		var model = '<?=$model?>';
 		var platform = '<?=$platform?>';
 		var version = '<?=$version?>';
 	</script>
 	
 	<script src="<?=base_url()?>assets/js/mitrapana.js"></script>

</head>
 
<body>

<div data-role="page" id="page1"  >
    <div data-theme="e" data-role="header">
    	<a id="btn-data-user" data-role="button" data-theme="a" href="#user-modal" data-rel="dialog" data-transition="pop" data-icon="grid" ><?=lang('dashboard.your_data')?></a>
    	 <!-- 
    	<a id="btn-localizame1" data-role="button"  data-theme="a" style="display: none;" ><?=lang('dashboard.localizame')?></a>
    	-->
        <h3><?= $this->config->item('app_name') ?></h3>
        <div id="agent-call-wrapper">
    		<a id="agent-call" data-role="button" data-theme="a" href="#" class="ui-btn-right"><?=lang('dashboard.calltaxi')?></a>
    	</div>
    	<div id="agent-call2-wrapper">
    		<a id="agent-call2" data-role="button" data-theme="b" href="#call-modal" class="ui-btn-right" data-rel="dialog" data-transition="pop" ><?=lang('dashboard.showtaxi')?></a>
    	</div>   
    	<?= form_open('api/call', array('id' => 'call-form', 'class' => '')) ?>
			<input id="lat" name="lat" type="hidden" value="">
			<input id="lng" name="lng" type="hidden" value="">
			<input id="zone" name="zone" type="hidden" value="">
			<input id="city" name="city" type="hidden" value="">
			<input id="state_c" name="state_c" type="hidden" value="">
			<input id="country" name="country" type="hidden" value="">
            <div data-role="fieldcontain">
            	<table border=0 width="100%"><tbody>
        		<tr><td >
        			<?=lang('dashboard.enter_address')?>
                	<input name="address" id="address" value="" type="text" data-mini="true" onkeydown="return validarEnter(event)">
            	</td><td >
                	<a href="#" id='btn-address-search'  align="left" data-role="button" data-icon="search" data-iconpos="notext" data-theme="a" data-inline="true"><?=lang('dashboard.search')?></a>
                </td>
                <td >
                	<a href="#" id='btn-localizame'  align="left" data-role="button" data-icon="home" data-iconpos="notext" data-theme="a" data-inline="true"><?=lang('dashboard.localizame')?></a>
                </td>
				</tr>
                </tbody></table>
            </div>    		
    	 </form>   

    </div>
    
   <!-- Mapa -->
    <div data-role="content" class="padding-0">
         <div id="map_canvas"></div>
   </div>
  

    <div data-theme="e" data-role="footer" data-position="fixed" align="center">
    	<!-- Publicidad -->
    	<div align="right" id="banner-wrapper" style="">
    		<label id="banner-label"></label>
			<a href="#" id='btn_banner_close'  align="right" data-role="button" data-icon="delete" data-iconpos="notext" data-theme="a" data-inline="true"><?=lang('dashboard.exit_banner')?></a>				
		</div>
	
	   	<a href="<?= $this->config->item('app_link') ?>" ><?= $this->config->item('copyright') ?></a>
    </div>

    <div id="sound_"></div>    
    <a href="#user-modal" data-role="button" id="show-user" style="display: none;" data-rel="dialog" data-transition="pop">Show user</a>
    <a href="#call-modal" data-role="button" id="show-call" style="display: none;" data-rel="dialog" data-transition="pop">Show call</a>
    
</div>

<!-- Start of third page: #popup -->
<div data-role="page" id="call-modal" data-close-btn="none">
	<div id="confirm-wrapper">
		<div data-role="header" data-theme="e" align="center">
			<b><?=lang('dashboard.callconfirm.title')?></b>
		</div><!-- /header -->
			<div data-role="content" data-theme="d">	
				<!--<p><?=lang('dashboard.callconfirm.msg')?></p>-->	
			    <div class="ui-grid-a">
			      <div class="ui-block-a">
			        <input name="call-name" id="call-name" placeholder="<?=lang('dashboard.user_name')?>" value="" type="text">
			      </div>
			      <div class="ui-block-b">
			       <input name="call-phone" id="call-phone" placeholder="<?=lang('dashboard.user_phone')?>" value="" type="text">        
			      </div>
			    </div>

				<!--<?=lang('dashboard.callconfirm.you_addrees')?>-->
				<input name="address-calle" id="address-calle" value="" type="text">
				
				<div class="ui-grid-a">
					<div class="ui-block-a">
			        	<input name="address-numero" id="address-numero" placeholder="<?=lang('dashboard.callconfirm.number')?>" value="" type="text">       
			      	</div>
			      	<div class="ui-block-b">
			       		<input name="address-alterna" id="address-alterna" placeholder="<?=lang('dashboard.callconfirm.address-alternating')?>" value="" type="text">
			      	</div>
				</div>
			    
			    <input name="address-reference" id="address-reference" placeholder="<?=lang('dashboard.callconfirm.address-reference')?>" value="" type="text">


				<!--<div id="confirmation-msg"><?=lang('dashboard.callconfirm.content')?>?</div>-->	

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
			<p>
			<?=lang('dashboard.agentphone')?>: <span id="agent-phone"></span>
			<a href="#" id='btn-phone'  data-role="button" data-icon="grid" data-theme="a" data-inline="true"><?=lang('dashboard.call')?></a>
			</p>
				
			<!--<p><div data-role="collapsible">
					<h2><?=lang('dashboard.infoshare')?>:</h2>
					<ul data-role="listview" data-split-icon="gear" data-split-theme="d">
						<li><span id="share-twitter"></span></li>
						<li><span id="share-facebook"></span></li>
					</ul>
				</div>
			</p>-->
			
		</div><!-- /content -->
				
		<p>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="show-taxi"><?=lang('dashboard.showtaxi')?></a>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="query-cancelation"><?=lang('dashboard.cancel')?></a>
		</p>
	</div>
</div><!-- /page popup -->

<div data-role="page" id="user-modal" >
	<div id="user-wrapper">
		<div data-role="header" data-theme="e">
			<h1><?=lang('dashboard.personal_data')?></h1>
		</div><!-- /header -->
	
		<div data-role="content" data-theme="d">	
			<table border=0 width="100%"><tbody>
	            <tr>
	            <td ><label for="user-name"><?=lang('dashboard.user_name')?>:</label></td>
	            <td ><input name="user-name" id="user-name" placeholder="<?=lang('dashboard.user_name')?>" value="" type="text"></td>
	            </tr>
	            <tr>
	            <td ><label for="user-phone"><?=lang('dashboard.user_phone')?>:</label></td>
	            <td ><input name="user-phone" id="user-phone" placeholder="<?=lang('dashboard.user_phone')?>:" value="" type="tel"></td>
	            </tr>
	            <tr>
	            <td ><label for="user-email"><?=lang('dashboard.user_email')?>:</label></td>
	            <td ><input name="user-email" id="user-email" placeholder="<?=lang('dashboard.user_email')?>" value="" type="email"></td>
	            </tr>
	            </tbody>
        	</table>

        	<div id="i-agree-wrapper" data-role="fieldcontain">
 				<a id="show-tyc"   href="#tyc-modal" data-rel="dialog" data-transition="pop" ><?=lang('dashboard.agree_terms')?></a>
 				<fieldset data-role="controlgroup">
    				<input type="checkbox" name="ck-i-agree" id="ck-i-agree"  />
					<label for="ck-i-agree"><?=lang('dashboard.i_agree')?></label>
				</fieldset>
			</div>

    	</div><!-- /content -->
		<p>
			<div id="btn_user_save-wrapper">
				<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="btn_user_back"><?=lang('dashboard.back')?></a>
				<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="btn_user_save"><?=lang('dashboard.save')?></a>
			</div>
		</p>
	</div>

</div><!-- /page popup -->

<div data-role="page" id="tyc-modal" >
	<div data-role="header" data-theme="e">
		<h1><?=lang('dashboard.tyc_msj')?></h1>
	</div><!-- /header -->
	
	<div data-role="content" data-theme="d">	
		<div>
			<label id='tyc-msj'></label>
		</div>
   	</div><!-- /content -->
		
	<p>
		<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="btn_tyc_exit"><?=lang('dashboard.back')?></a>
	</p>
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

    	<!-- <div>
    		<a href="#popupPanel" data-rel="popup" data-transition="slide" data-position-to="window" data-role="button">Open panel</a>
			<div data-role="popup" id="popupPanel" data-corners="false" data-theme="none" data-shadow="false" data-tolerance="0,0">
		    <button data-theme="a" data-icon="back" data-mini="true">Back</button>
		    <button data-theme="a" data-icon="grid" data-mini="true">Menu</button>
		    <button data-theme="a" data-icon="search" data-mini="true">Search</button>
			</div>

    	</div>

    	#popupPanel-popup {
    right: 0 !important;
    left: auto !important;
}
#popupPanel {
    width: 200px;
    border: 1px solid #000;
    border-right: none;
    background: rgba(0,0,0,.5);
    margin: -1px 0;
}
#popupPanel .ui-btn {
    margin: 2em 15px;
}
 -->