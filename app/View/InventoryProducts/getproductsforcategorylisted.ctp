<?php
	
	//pr($inventoryProductsForInventoryProductLine);
	$list="<h3>Productos existentes de esta Línea</h3>";
	if (!empty($inventoryProductsForInventoryProductLine)){
		
		$list.="<ul>";
		foreach ($inventoryProductsForInventoryProductLine as $inventoryProduct){
			$list.="<li>".$this->Html->Link($inventoryProduct['InventoryProduct']['name'],array('controller'=>'inventory_products','action'=>'view',$inventoryProduct['InventoryProduct']['id']))."</li>";
		}
		$list.="</ul>";
	}
	echo $list;
?>