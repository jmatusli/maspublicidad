<?php
	$options="<option value='0'>-- Contacto --</option>";
	if (!empty($contacts)){
		foreach ($contacts as $contact){
			$options.="<option value='".$contact['Contact']['id']."'>".$contact['Contact']['first_name']." ".$contact['Contact']['last_name']."</option>";
		}
	}
	echo $options;
?>
