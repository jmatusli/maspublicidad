<?php 
	$filename=date('d_m_Y')."_Comisiones por vendedor.xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	