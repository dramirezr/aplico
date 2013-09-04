<?php 
	if(!$userconfig = $this->session->userdata('userconfig')){
			redirect($user->lang.'/login'); 
	}

	if($userconfig->perfil=='ADMIN'){
?>

<!-- encabezado administradores -->
<div>
	<a href='<?php echo site_url('admin/user_management')?>'>Usuarios del sistema</a> |
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
<!-- FIN encabezado administradores -->
<!-- encabezado call center -->
<?php 	
	}else
	if($userconfig->perfil=='CALL'){
?>

<div>
	<a href='<?php echo site_url('es')?>'>Pedir taxi</a> |
	<a href='<?php echo site_url('admin/close')?>'>Salida segura</a> |
</div> 

<?php 
	}
?>
<!-- FIN encabezado call center -->