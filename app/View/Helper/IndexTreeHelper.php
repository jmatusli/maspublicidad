﻿<?php 
	class IndexTreeHelper extends AppHelper {
		var $helpers = array('Html'); // include the html helper
		
		public function RecursiveProductLines($array) { 
			if (count($array)) { 
				echo "\n<ul>\n"; 
				foreach ($array as $vals) { 					
					echo "<li id=\"".$vals['ProductLine']['id']."\">".$this->Html->link($vals['ProductLine']['name'], array('controller' => 'product_lines', 'action' => 'view', $vals['ProductLine']['id']));  
					if (count($vals['children'])) { 
						$this->RecursiveProductLines($vals['children']); 
					} 
					echo "</li>\n"; 
				} 
				echo "</ul>\n"; 
			}
		}
		
		public function RecursiveAccountingCodes($array) { 
			if (count($array)) { 
				echo "\n<ul>\n"; 
				foreach ($array as $vals) { 
					//pr($vals);
					//if (!empty($vals['AccountingCode']['id'])&&$vals['AccountingCode']['bool_active']){
						if ($vals['AccountingCode']['bool_main']){
							echo "<li id=\"".$vals['AccountingCode']['id']."\">".$this->Html->link($vals['AccountingCode']['code']." ".$vals['AccountingCode']['description'], array('controller' => 'accounting_codes', 'action' => 'view', $vals['AccountingCode']['id']));  
						}
						else {
							echo "<li id=\"".$vals['AccountingCode']['id']."\">".$this->Html->link($vals['AccountingCode']['code']." ".$vals['AccountingCode']['description'], array('controller' => 'accounting_codes', 'action' => 'view', $vals['AccountingCode']['id']), array( 'class' => 'italic'));  
						}
					//}
					if (count($vals['children'])) { 
						$this->RecursiveAccountingCodes($vals['children']); 
					} 
					echo "</li>\n"; 
				} 
				echo "</ul>\n"; 
			}
		}
	}
?>