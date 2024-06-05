<?php
	$options="<option value='0'>Seleccione cliente</option>";
	if (!empty($clients)){
		foreach ($clients as $client){
			$options.="<option value='".$client['Client']['id']."'>".$client['Client']['name']."</option>";
		}
	}
	echo $options;
?>

