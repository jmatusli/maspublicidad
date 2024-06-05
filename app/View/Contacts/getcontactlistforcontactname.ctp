<?php
	$options="<option value='0'>Seleccione un contacto</option>";
	if (!empty($contacts)){
		/*
		if (count($contacts)==1){
			$options.="<option value='".$contacts[0]['Contact']['id']."' selected='true'>".$contacts[0]['Contact']['first_name']." ".$contacts[0]['Contact']['last_name']."</option>";
		}
		else {
			foreach ($contacts as $contact){
				$options.="<option value='".$contact['Contact']['id']."'>".$contact['Contact']['first_name']." ".$contact['Contact']['last_name']."</option>";
			}
		}
		echo $options;
		*/
		foreach ($contacts as $contact){
			$options.="<option value='".$contact['Contact']['id']."'>".$contact['Contact']['first_name']." ".$contact['Contact']['last_name']."</option>";
		}
	}
	echo $options;
?>