<?php 
	$filename="Cotizaciones_Por_Cliente_".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	