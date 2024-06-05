<?php 
	$filename="Resumen Productos_Inventario_".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	