<?php
	
	//pr($productsForCategory);
	$list="<h3>Productos existentes de esta Categoría</h3>";
	if (!empty($productsForCategory)){
		
		$list.="<ul>";
		foreach ($productsForCategory as $product){
			$list.="<li>".$this->Html->Link($product['Product']['name'],array('controller'=>'products','action'=>'view',$product['Product']['id']))."</li>";
		}
		$list.="</ul>";
	}
	echo $list;
?>