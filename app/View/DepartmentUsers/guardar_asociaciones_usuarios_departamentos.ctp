<?php 
	$filename="Asociaciones_Usuarios_Departamentos".date('d_m_Y').".xlsx";
	$this->PhpExcel->generalExport($exportData,$filename,"");
?>
	
	