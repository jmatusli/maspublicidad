<?php
	$options="<option value='0'>Seleccione Producto</option>";
	//pr($inventoryProducts);
	if (!empty($inventoryProducts)){
		foreach ($inventoryProducts as $inventoryProduct){
			$options.="<option value='".$inventoryProduct['InventoryProduct']['id']."'>".$inventoryProduct['InventoryProduct']['name']."</option>";
		}
	}
	echo $options;
?>