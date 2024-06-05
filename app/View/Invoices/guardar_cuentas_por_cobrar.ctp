<?php 
	$filename=date('d_m_Y')."_Cuentas por Cobrar.xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	