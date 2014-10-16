<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
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
		<a href='<?php echo site_url('admin/user_managervehicle')?>'>Dueños de Taxis</a> |
		<a href='<?php echo site_url('admin/vehicle_management')?>'>Vehiculos</a> |
		<a href='<?php echo site_url('admin/agent_management')?>'>Taxitas</a> |
		<a href='<?php echo site_url('admin/tabletShowAgent') ?>'>Seguimiento Vehiculos</a> |
		<a href='<?php echo site_url('admin/close')?>'>Salida segura</a> |
	</div> 

	<div style='height:20px;'></div>  
	<?php 
		$url = site_url('').$op; 
		if ($op=='/admin/dashboardCall'){
			echo "<iframe id='targetFrame' src='".$url."' width='100%' height='600px'  frameborder='0' ></iframe>";
		}else{
			if ($op=='/admin/tabletCallAgent'){
				echo "<iframe id='targetFrame' src='".$url."' width='100%' height='600px'  frameborder='0' ></iframe>";
			}else{
			
				echo $output;

			}
		}
	?>

	<div style='height:100%;'>
		

    </div>
</body>
</html>
