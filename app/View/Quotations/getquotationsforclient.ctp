<?php
	$options="<option value='0'>Seleccione Cotización</option>";
	//pr($quotationsForClient);
	if (!empty($quotationsForClient)){
		foreach ($quotationsForClient as $quotation){
			$options.="<option value='".$quotation['Quotation']['id']."'>".$quotation['Quotation']['quotation_code']."</option>";
		}
	}
	echo $options;
?>