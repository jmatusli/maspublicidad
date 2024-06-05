<?php
	
	//pr($productsForDepartment);
	$list="<h3>Productos existentes de este Departamento</h3>";
	if (!empty($productsForDepartment)){
		
		$list.="<ul>";
		foreach ($productsForDepartment as $product){
			$list.="<li>".$this->Html->Link($product['Product']['name'],array('controller'=>'products','action'=>'view',$product['Product']['id']))."</li>";
		}
		$list.="</ul>";
	}
	echo $list;
?>