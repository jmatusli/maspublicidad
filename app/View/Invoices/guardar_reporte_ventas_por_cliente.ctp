<?php 
	$filename="Ventas_por_Cliente_".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	