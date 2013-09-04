<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />

<style type='text/css'>
body
{
	font-family: Arial;
	font-size: 14px;
}
a {
    color: blue;
    text-decoration: none;
    font-size: 14px;
}
a:hover
{
	text-decoration: underline;
}
</style>


    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 
	<title><?= $this->config->item('app_name') ?></title>

</head>
<body>
	<div>
		<a href='<?php echo site_url('admin/callService') ?>'>Pedir taxi</a> |
		<a href='<?php echo site_url('admin/showAgent') ?>'>Ver Taxistas</a> |
		<a href='<?php echo site_url('admin/close')?>'>Salida segura</a> |
	</div> 

	<div style='height:20px;'></div>  
	<?php  $url = site_url('').$op; ?>
	<iframe id="targetFrame" src="<?php echo $url;?>" width="100%" height="500px"  frameborder="0" ></iframe>

	<div style='height:100%;'>
		

    </div>
</body>
</html>
