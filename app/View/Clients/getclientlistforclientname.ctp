<?php
	$options="";
	if (!empty($clients)){
		if (count($clients)==1){
			$options.="<option value='".$clients[0]['Client']['id']."' selected='true'>".$clients[0]['Client']['name']."</option>";
		}
		else {
			foreach ($clients as $client){
				$options.="<option value='".$client['Client']['id']."'>".$client['Client']['name']."</option>";
			}
		}
		echo $options;
	}
?>