<?php
	if (!empty($quotation)){
		//pr($quotation);
		$quotationDate=new DateTime($quotation['Quotation']['quotation_date']);
		echo "<h3>Resumen Cotización ".$this->Html->link($quotation['Quotation']['quotation_code'],['controller'=>'quotations','action'=>'view',$quotation['Quotation']['id']]."</h3>";
		echo "<dl>";
			echo "<dt>Ejecutivo</dt>";
			echo "<dd>".$this->Html->link($quotation['User']['username'],array('controller'=>'users','action'=>'view',$quotation['User']['id']))."</dd>";
			echo "<dt>Cliente</dt>";
			echo "<dd>".$this->Html->link($quotation['Client']['name'],array('controller'=>'clients','action'=>'view',$quotation['Client']['id']))."</dd>";
			echo "<dt>Fecha Cotización</dt>";
			echo "<dd>".$quotationDate->format('d-m-Y')."</dd>";
		echo "</dl>";
	}
?>
<script>
	$(document).ajaxComplete(function() {	
	});
</script>