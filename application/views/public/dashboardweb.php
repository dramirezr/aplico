<!doctype html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=9">

	<link rel="stylesheet" href="<?=base_url()?>assets/css/app.css" />
  	<link rel="stylesheet" href="<?=base_url()?>assets/css/jquery.mobile-1.3.2.min.css" />
    <script src="<?=base_url()?>assets/js/jquery-1.10.2.min.js"></script>
    <script src="<?=base_url()?>assets/js/jquery.mobile-1.3.2.min.js"></script>

    <script language="Javascript">
        document.oncontextmenu = function(){return false}
    </script>

	<title><?= $this->config->item('app_name') ?></title>


</head>
 
<body>

<div data-role="page" id="page1">
    <div data-theme="e" data-role="header" align="center">
        <?= $this->config->item('app_name') ?>
    </div>
    

    <div data-role="content" class="padding-0" align="center">
    	<div id="phone-iframe-v" style="background:url(<?=base_url()?>assets/images/phone.png)">
			<iframe src="<?=base_url()?>es/dashboard/web_dashboard/" scrolling="no" ></iframe>
    	</div>
    </div>
  
        
    <div data-theme="e" data-role="footer" data-position="fixed" align="center">
        <?= $this->config->item('copyright') ?>
    </div>
</div>


</body>
</html>