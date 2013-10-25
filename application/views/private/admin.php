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

<!--	
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
-->

</head>
<body>
	<div>
		<a href='<?php echo site_url('admin/user_management')?>'>Usuarios del sistema</a> |
		<a href='<?php echo site_url('admin/user_callcenter')?>'>Centro de atención</a> |
		<a href='<?php echo site_url('admin/user_managervehicle')?>'>Dueños de Taxis</a> |
		<a href='<?php echo site_url('admin/vehicle_management')?>'>Vehiculos</a> |
		<a href='<?php echo site_url('admin/agent_management')?>'>Taxitas</a> |
		<a href='<?php echo site_url('admin/solicitude_management')?>'>Solicitudes</a> |
		<a href='<?php echo site_url('admin/service_agent')?>'>Servicios X Taxista</a> |
		<a href='<?php echo site_url('admin/close')?>'>Salida segura</a> |
	</div> 

	<div style='height:20px;'></div>  
	<?php if( ($op=="solicitude_management") or ($op=="service_agent") ){ ?>
	<div>
		<form action='<?php echo site_url('admin')."/$op";?>' method='GET' > 
			Fecha inicial : <input type='text' name='fechaini' value='<?php echo $fechaini; ?>' MAXLENGTH=20 />
			Fecha final : <input type='text' name='fechafin' value='<?php echo $fechafin; ?>' MAXLENGTH=20 />
			<input type='submit'  value="Consultar" name='btn_consultar' class="submit" />
		</form> 
    </div>
	<?php } ?>
	<div>
		<?php echo $output; ?>
    </div>
</body>
</html>
