<!doctype html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 
	<title><?= $this->config->item('app_name') ?></title>

	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
	<link rel="stylesheet" href="<?=base_url()?>assets/css/app.css" />
	
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script> 
    
    <script src="<?=base_url()?>assets/js/mitrapana.js"></script>
    <script src="<?=base_url()?>assets/js/jquery.playSound.js"></script>
 	
 	<script>
 		var lang = '<?=current_lang()?>';
 		var verification_interval = <?=ci_config('verification_interval')?>;
 		var searching_msg = '<h1><?=lang('dashboard.searching')?></h1>';
 	</script>
</head>
 
<body>


<div data-role="page" id="page1">
    <div data-theme="e" data-role="header">
    	<a data-role="button" data-theme="a" href="#page1" class="ui-btn-left" id="btn-localizame"><?=lang('dashboard.localizame')?></a>
        <h3><?= $this->config->item('app_name') ?></h3>
        <a data-role="button" data-theme="a" href="#call-modal" class="ui-btn-right" data-rel="dialog" data-transition="pop" id="agent-call"><?=lang('dashboard.calltaxi')?></a>
       	<?= form_open('api/call', array('id' => 'call-form', 'class' => '')) ?>
			<input id="lat" name="lat" type="hidden" value="">
			<input id="lng" name="lng" type="hidden" value="">
			<input id="zone" name="zone" type="hidden" value="">
			<input id="city" name="city" type="hidden" value="">
			<input id="state_c" name="state_c" type="hidden" value="">
			<input id="country" name="country" type="hidden" value="">
            <div data-role="fieldcontain">
                <input name="address" id="address" value="" type="text" data-mini="true" >
            </div>    		
    	 </form>        
    </div>
    
    <div data-role="content" class="padding-0">
         <div id="map_canvas"></div>
    </div>

    <div data-theme="e" data-role="footer" data-position="fixed">
        <h3>
            Â© 2013 <?= $this->config->item('app_name') ?>
        </h3>
    </div>
        
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
			<p><?=lang('dashboard.confimationcode')?>: <span id="confirmation-code"><span></p>
			<p id="agent-photo"></p>
			<p id="agent-name"></p>
			<!--<p><?=lang('dashboard.agentid')?>: <span id="agent-id"><span></p>-->
			<p><?=lang('dashboard.agentcode2')?>: <span id="agent-code2"><span></p>
			<p><?=lang('dashboard.agentphone')?>: <span id="agent-phone"></span></p>
		
		</div><!-- /content -->
		
		<p>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="show-taxi"><?=lang('dashboard.showtaxi')?></a>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="query-cancelation"><?=lang('dashboard.cancel')?></a>
		</p>
	</div>
</div><!-- /page popup -->



</body>
</html>