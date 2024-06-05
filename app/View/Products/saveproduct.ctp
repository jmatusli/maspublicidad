<?php
	$options="<option value='0'>Seleccione Producto</option>";
	//pr($products);
	if (!empty($products)){
		foreach ($products as $product){
			$options.="<option value='".$product['Product']['id']."'>".$product['Product']['name']."</option>";
		}
	}
	echo $options;
?>