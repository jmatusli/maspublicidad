<?php
	$options="<option value='0'>Seleccione Producto</option>";
	//pr($productsForCategory);
	if (!empty($productsForCategory)){
		foreach ($productsForCategory as $product){
			$options.="<option value='".$product['Product']['id']."'>".$product['Product']['name']."</option>";
		}
	}
	echo $options;
?>