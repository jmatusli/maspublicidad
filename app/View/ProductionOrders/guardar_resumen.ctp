<?php 
	$filename="Resumen_Ordenes_Producci�n_".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	