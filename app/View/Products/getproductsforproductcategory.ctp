<?php
	$options="<option value='0'>Seleccione Producto</option>";
	//pr($productsForProductCategory);
	if (!empty($productsForProductCategory)){
		foreach ($productsForProductCategory as $product){
			$options.="<option value='".$product['Product']['id']."'>".$product['Product']['name']."</option>";
		}
	}
	echo $options;
?>