<!doctype html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 
	<title><?= $this->config->item('app_name') ?></title>

	<link rel="stylesheet" href="<?=base_url()?>assets/css/app.css" />
    <link rel="stylesheet" href="<?=base_url()?>assets/css/jquery.mobile-1.3.2.min.css" />
 
    <script src="<?=base_url()?>assets/js/jquery-1.10.2.min.js"></script>
    <script src="<?=base_url()?>assets/js/jquery.mobile-1.3.2.min.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script> 
    
    <script src="<?=base_url()?>assets/js/custripan.js"></script>
    
  	<script>
 		var lang = '<?=current_lang()?>';
        var app_path = '<?=ci_config('app_path')?>';
 		var verification_interval = <?=ci_config('verification_interval')?>;
 		var searching_msg = '<h1><?=lang('dashboard.searching')?></h1>';
 	</script>
</head>
 
<body>

<div data-role="page" id="page1">

    <div data-theme="e" data-role="header">
    	<table border=0 width="100%"><tbody>
        <tr>
            <td >
                Sucursal:
                <select name="select-sucursal" id="select-sucursal"  data-native-menu="true" onchange="get_all_units(this.value)" > 
                    <option value="-1">Todas</option>
                </select>
            </td >
            <td >
                Unidad:
                <select name="select-unidad" id="select-unidad"  data-native-menu="true"  > 
                    <option value="-1">Todas</option>
                </select>
            </td >
            <td >
                Placa:
                <select name="select-placa" id="select-placa"  data-native-menu="true" > 
                    <option value="-1">Todas</option>
                </select>
            </td >
            <td >
                Taxista:
                <select name="select-taxista" id="select-taxista"  data-native-menu="true" > 
                    <option value="-1">Todas</option>
                </select>
            </td >
             
        </tr >
    
        </tbody></table>

    </div>
      <a id="btn-cosultar" data-role="button"  data-theme="a" >Consultar</a>
    <div data-role="content" class="padding-0">
         <div id="map_canvas"></div>
    </div>
    
</div>

</body>
</html>