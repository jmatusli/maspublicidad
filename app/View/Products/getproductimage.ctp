<?php
	if (!empty($product)){
		if (!empty($product['Product']['url_image'])){
			$url=$product['Product']['url_image'];
			echo "<img src='".$this->Html->url('/').$url."' alt='Picture' class='resizesmall'></img>";
		}
	}
	echo "<span></span>";
?>
