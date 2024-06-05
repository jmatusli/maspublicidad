<?php
	$options="<option value='0'>Seleccione cliente de inventario</option>";
	if (!empty($inventoryClients)){
		foreach ($inventoryClients as $inventoryClient){
			$options.="<option value='".$inventoryClient['InventoryClient']['id']."'>".$inventoryClient['InventoryClient']['name']."</option>";
		}
	}
	echo $options;
?>

