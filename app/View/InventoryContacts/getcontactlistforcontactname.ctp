<?php
	$options="<option value='0'>Seleccione un contacto</option>";
	if (!empty($inventoryContacts)){
		/*
		if (count($inventoryContacts)==1){
			$options.="<option value='".$inventoryContacts[0]['InventoryContact']['id']."' selected='true'>".$inventoryContacts[0]['InventoryContact']['first_name']." ".$inventoryContacts[0]['InventoryContact']['last_name']."</option>";
		}
		else {
			foreach ($inventoryContacts as $contact){
				$options.="<option value='".$contact['InventoryContact']['id']."'>".$contact['InventoryContact']['first_name']." ".$contact['InventoryContact']['last_name']."</option>";
			}
		}
		echo $options;
		*/
		foreach ($inventoryContacts as $contact){
			$options.="<option value='".$contact['InventoryContact']['id']."'>".$contact['InventoryContact']['first_name']." ".$contact['InventoryContact']['last_name']."</option>";
		}
	}
	echo $options;
?>