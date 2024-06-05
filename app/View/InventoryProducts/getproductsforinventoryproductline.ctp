<?php
	$options="<option value='0'>Seleccione Producto</option>";
	//pr($inventoryProductsForInventoryProductLine);
	if (!empty($inventoryProductsForInventoryProductLine)){
		foreach ($inventoryProductsForInventoryProductLine as $inventoryProduct){
			$options.="<option value='".$inventoryProduct['InventoryProduct']['id']."'>".$inventoryProduct['InventoryProduct']['name']."</option>";
		}
	}
	echo $options;
?>