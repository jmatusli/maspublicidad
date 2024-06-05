<?php 
	$filename="Cotizaciones_Por_Categoria_".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	