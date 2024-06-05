<?php
	//pr($productsForProductCategory);
	$list="<h3>Productos existentes de esta Categoría</h3>";
	if (!empty($productsForProductCategory)){
		
		$list.="<ul>";
		foreach ($productsForProductCategory as $product){
			$list.="<li>".$this->Html->Link($product['Product']['name'],array('controller'=>'products','action'=>'view',$product['Product']['id']))."</li>";
		}
		$list.="</ul>";
	}
	echo $list;
?>