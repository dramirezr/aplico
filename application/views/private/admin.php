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
	<link type="text/css" rel="stylesheet" href="<?=base_url()?>assets/grocery_crud/css/jquery_plugins/jquery.ui.datetime.css" />
	<link type="text/css" rel="stylesheet" href="<?=base_url()?>assets/grocery_crud/css/jquery_plugins/jquery-ui-timepicker-addon.css" />
	<script src="<?=base_url()?>assets/grocery_crud/js/jquery_plugins/jquery-ui-timepicker-addon.min.js"></script>
	<script src="<?=base_url()?>assets/grocery_crud/js/jquery_plugins/ui/i18n/datepicker/jquery.ui.datepicker-es.js"></script>
	<script src="<?=base_url()?>assets/grocery_crud/js/jquery_plugins/ui/i18n/timepicker/jquery-ui-timepicker-es.js"></script>
	<script src="<?=base_url()?>assets/grocery_crud/js/jquery_plugins/config/jquery-ui-timepicker-addon.config.js"></script>
	
</head>
<body>
	<div>
	<?php
	if($this->userconfig->perfil=='ADMIN'){ ?>
		<a href='<?php echo site_url('admin/office_management')?>'>Sucursales</a> |
		<a href='<?php echo site_url('admin/user_management')?>'>Usuarios del sistema</a> |
		<a href='<?php echo site_url('admin/user_callcenter')?>'>Centro de atención</a> |
		<a href='<?php echo site_url('admin/user_managervehicle')?>'>Dueños de Taxis</a> |
		<a href='<?php echo site_url('admin/vehicle_management')?>'>Vehiculos</a> |
		<a href='<?php echo site_url('admin/agent_management')?>'>Taxistas</a> |
		<a href='<?php echo site_url('admin/solicitude_management')?>'>Solicitudes</a> |
		<a href='<?php echo site_url('admin/service_agent')?>'>Servicios X Taxista</a> |
		<a href='<?php echo site_url('admin/reasons_sanction_management') ?>'>Motivos sanción</a> |
		<a href='<?php echo site_url('admin/sanction_management') ?>'>Sanciones</a> |
		<a href='<?php echo site_url('admin/show_agent_map') ?>'>Seguimiento Vehiculos</a> |
	<?php 
	}else
	if($this->userconfig->perfil=='CALL'){ ?>

		<a href='<?php echo site_url('admin/callService') ?>'>Pedir taxi</a> |
		<a href='<?php echo site_url('admin/user_managervehicle')?>'>Dueños de Taxis</a> |
		<a href='<?php echo site_url('admin/vehicle_management')?>'>Vehiculos</a> |
		<a href='<?php echo site_url('admin/agent_management')?>'>Taxitas</a> |
		<a href='<?php echo site_url('admin/sanction_management') ?>'>Sanciones</a> |
		<a href='<?php echo site_url('admin/show_agent_map') ?>'>Seguimiento Vehiculos</a> |

	<?php 	
	}else
	if($this->userconfig->perfil=='CUST'){?>

		<a href='<?php echo site_url('admin/show_agent_map') ?>'>Seguimiento Vehiculos</a> |
		

	<?php 
	}
	?>

		<a href='<?php echo site_url('admin/close')?>'>Salida segura</a> |

	</div> 

	<div style='height:20px;'></div>  
	<?php 

	if( ($op=="solicitude_management") or ($op=="service_agent") ){ 

	?>
	<div>
		<form action='<?php echo site_url('admin')."/$op";?>' method='GET' > 
			
			<div class='form-field-box even' id="fechaini_field_box">
				<div class='form-display-as-box' id="fechaini_display_as_box">
					Fecha Inicial :
				</div>
				<div class='form-input-box' id="fechaini_input_box">
					<input id='field-fechaini' name='fechaini' type='text' value='<?php echo $fechaini; ?>' maxlength='19' class='datetime-input' /> 
					<a class='datetime-input-clear' tabindex='-1'>Limpiar</a>
					(yyyy/mm/dd) hh:mm:ss				
				</div>
				<div class='clear'></div>	
			
				<div class='form-display-as-box' id="fechafin_display_as_box">
					Fecha final :
				</div>
				<div class='form-input-box' id="fechafin_input_box">
					<input id='field-fechafin' name='fechafin' type='text' value='<?php echo $fechafin; ?>' maxlength='19' class='datetime-input' /> 
					<a class='datetime-input-clear' tabindex='-1'>Limpiar</a>
					(yyyy/mm/dd) hh:mm:ss				
				</div>
				<div class='clear'></div>	
			</div>
			<input type='submit'  value="Consultar" name='btn_consultar' class="submit" />

		</form> 
    </div>
	<?php } ?>
	<div>
		<?php 


			if( ($op=="show_agent_map") or ($op=="callService") )
			{
				echo "<br>";
				$url = site_url('').'/'.$url;
				echo "<iframe id='targetFrame' src='$url' width='100%' height='700px'  frameborder='0' ></iframe>";
			}else
				echo $output; 
		?>
    </div>

    <script type="text/javascript">
//var js_date_format = 'dd/mm/yy';
var js_date_format = 'yy/mm/dd';
</script>

</body>
</html>