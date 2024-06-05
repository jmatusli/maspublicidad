<?php 
	$filename=date('d_m_Y')."_Resumen Comisiones Por Pagar.xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	