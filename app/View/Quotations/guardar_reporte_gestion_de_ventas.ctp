<?php 
	$filename="Cotizaciones por Ejecutivo_".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	