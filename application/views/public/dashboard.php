<!doctype html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 
	<title><?= $this->config->item('app_name') ?></title>

	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
	<link rel="stylesheet" href="<?=base_url()?>assets/css/app.css" />
	
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script> 
    
    <script src="<?=base_url()?>assets/js/mitrapana.js"></script>
 	
 	<script>
 		var lang = '<?=current_lang()?>';
 		var verification_interval = <?=ci_config('verification_interval')?>;
 	</script>
</head>
 
<body>


<div data-role="page" id="page1">
    <div data-theme="e" data-role="header">
        <h3><?= $this->config->item('app_name') ?></h3>
        <a data-role="button" class="ui-btn-right" href="#call-modal" data-rel="dialog" data-transition="pop" id="call-agent"><?=lang('dashboard.calltaxi')?></a>
    </div>
    
    <div data-role="content" class="padding-0">
    
       	<?= form_open('api/call', array('id' => 'call-form', 'class' => '')) ?>
			<input id="lat" name="lat" type="hidden" value="">
			<input id="lng" name="lng" type="hidden" value="">
            <div data-role="fieldcontain" class="black-bar margin-0 ">
                <input name="address" id="address" value="" type="text" data-mini="true" >
            </div>
    		
    	 </form>
         <div id="map_canvas"></div>
    </div>
</div>

<!-- Start of third page: #popup -->
<div data-role="page" id="call-modal" data-close-btn="none">
	<div id="confirm-wrapper">
		<div data-role="header" data-theme="e">
			<h1><?=lang('dashboard.callconfirm.title')?></h1>
		</div><!-- /header -->
	
		<div data-role="content" data-theme="d">	
			<p id="confirmation-msg"><?=lang('dashboard.callconfirm.content')?>: <span id="show-address"></span></p>	
			<h1 id="waiting-msg"><?=lang('dashboard.searching')?></h1>
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
			<p id="agent-photo"></p>
			<p id="agent-name"></p>
			<p><?=lang('dashboard.agentid')?>: <span id="agent-id"><span></p>
			<p><?=lang('dashboard.agentphone')?>: <span id="agent-phone"></span> <a href="#" data-icon="forward" data-role="button" data-mini="true" data-inline="true" id="calling-agent"><?=lang('dashboard.call')?></a></p>
		</div><!-- /content -->
		
		<p>
			<a href="#" data-role="button" data-mini="true" data-inline="true" data-rel="back" id="call-cancelation"><?=lang('dashboard.cancel')?></a>
		    <a href="#" data-role="button" data-mini="true" data-inline="true" data-icon="check" data-theme="b" id="agent-confirmation"><?=lang('dashboard.confirm')?></a>
		</p>
	</div>
</div><!-- /page popup -->



</body>
</html>