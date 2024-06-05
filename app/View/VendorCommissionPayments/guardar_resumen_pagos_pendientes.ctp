<?php 
	$filename=date('d_m_Y')."_Resumen Pagos de Vendedor Pendientes.xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	