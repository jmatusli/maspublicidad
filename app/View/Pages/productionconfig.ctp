<?php
	
	echo "<div id='configoptions'>";
	echo "<h1>".__('Configuration Options')."</h1>";
	
	echo "<div class='col-md-4'>";
	echo $this->Html->image("product.jpg", array("alt" => "Product",'url' => array('controller' => 'orders','action' => 'index'))); 
	echo "<h2>";
	echo __('Products'); 
	echo "</h2>";
	echo "<div>";
	echo $this->Html->link(__('Product Types'), array('controller' => 'productTypes', 'action' => 'index'));
	echo "</div>";
	echo "<div>";
	echo $this->Html->link(__('Products'), array('controller' => 'products', 'action' => 'index'));
	echo "</div>";
	echo "<div>";
	echo $this->Html->link(__('Providers'), array('controller' => 'thirdParties', 'action' => 'indexProviders'));
	echo "</div>";
	echo "<div>";
	echo $this->Html->link(__('Clients'), array('controller' => 'thirdParties', 'action' => 'indexClients'));
	echo "</div>";
	echo "</div>";
	
	echo "<div class='col-md-4'>";
	echo $this->Html->image("production.jpg", array("alt" => "Production",'url' => array('controller' => 'orders','action' => 'index'))); 
	echo "<h2>";
	echo __('Production'); 
	echo "</h2>";
	echo "<div>";
	echo $this->Html->link(__('Machines'), array('controller' => 'machines', 'action' => 'index'));
	echo "</div>";
	echo "<div>";
	echo $this->Html->link(__('Operators'), array('controller' => 'operators', 'action' => 'index'));
	echo "</div>";
	echo "<div>";
	echo $this->Html->link(__('Shifts'), array('controller' => 'shifts', 'action' => 'index'));
	echo "</div>";
	echo "</div>";
	
	
	echo "<div class='col-md-4'>";
		echo $this->Html->image("user.jpg", array("alt" => "User",'url' => array('controller' => 'users','action' => 'index'))); 
		echo '<h2>'.__('Users').'</h2>';
		if ($userrole == ROLE_ADMIN){
			echo "<div>".$this->Html->link(__('User Management'), array('controller' => 'users', 'action' => 'index'))."</div>";
      
      echo "<div>".$this->Html->link('Permisos', ['controller' => 'users', 'action' => 'rolePermissions'])."</div>";
      echo "<div>".$this->Html->link('Permisos de Producción', ['controller' => 'users', 'action' => 'roleProductionPermissions'])."</div>";
      echo "<div>".$this->Html->link('Permisos de Config', ['controller' => 'users', 'action' => 'roleConfigPermissions'])."</div>";
      
      echo "<div>".$this->Html->link('Logs de usuario', ['controller' => 'userLogs', 'action' => 'resumen'])."</div>";
		}
		echo "<div>".$this->Html->link(__('Employees'), array('controller' => 'employees', 'action' => 'index'))."</div>";
		echo "<div>".$this->Html->link(__('Employee Holidays'), array('controller' => 'employee_holidays', 'action' => 'index'))."</div>";
	echo "</div>";

	echo "</div>";
