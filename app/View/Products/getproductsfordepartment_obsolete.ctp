<?php
	$options="<option value='0'>Seleccione Producto</option>";
	//pr($productsForDepartment);
	if (!empty($productsForDepartment)){
		foreach ($productsForDepartment as $product){
			$options.="<option value='".$product['Product']['id']."'>".$product['Product']['name']."</option>";
		}
	}
	echo $options;
?>