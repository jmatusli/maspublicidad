<?php
	if (!empty($inventoryProduct)){
		if (!empty($inventoryProduct['InventoryProduct']['url_image'])){
			$url=$inventoryProduct['InventoryProduct']['url_image'];
			echo "<img src='".$this->Html->url('/').$url."' alt='Picture' class='resizesmall'></img>";
		}
	}
	echo "<span></span>";
?>
