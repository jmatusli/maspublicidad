<?php
	$options="";
	if (!empty($inventoryClients)){
		if (count($inventoryClients)==1){
			$options.="<option value='".$inventoryClients[0]['InventoryClient']['id']."' selected='true'>".$inventoryClients[0]['InventoryClient']['name']."</option>";
		}
		else {
			foreach ($inventoryClients as $inventoryClient){
				$options.="<option value='".$inventoryClient['InventoryClient']['id']."'>".$inventoryClient['InventoryClient']['name']."</option>";
			}
		}
		echo $options;
	}
?>