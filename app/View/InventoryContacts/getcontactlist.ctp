<?php
	$options="<option value='0'>Seleccione un contacto</option>";
	if (!empty($inventoryContacts)){
		foreach ($inventoryContacts as $contact){
			$options.="<option value='".$contact['InventoryContact']['id']."'>".$contact['InventoryContact']['first_name']." ".$contact['InventoryContact']['last_name']."</option>";
		}
	}
	echo $options;
?>
