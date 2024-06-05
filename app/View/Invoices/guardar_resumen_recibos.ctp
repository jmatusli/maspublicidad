<?php 
	$filename=date('d_m_Y')."_Resumen Recibos.xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	