<div class="companies view">
<?php 
	echo "<h2>".__('Company')." ".$company['Company']['name']."</h2>";
	echo "<dl>";
		echo "<dt>".__('Name')."</dt>";
		echo "<dd>".h($company['Company']['name'])."</dd>";
		echo "<dt>".__('Description')."</dt>";
		echo "<dd>".h($company['Company']['description'])."</dd>";
	echo "</dl>";
?> 
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_edit_permission){
			echo "<li>".$this->Html->link(__('Edit Company'), array('action' => 'edit', $company['Company']['id']))."</li>";
			echo "<br/>";
		}
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete Company'), array('action' => 'delete', $company['Company']['id']), array(), __('Está seguro que quiere eliminar la compañía %s?', $company['Company']['name']))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Companies'), array('action' => 'index'))."</li>";
		echo "<li>".$this->Html->link(__('New Company'), array('action' => 'add'))."</li>";
		echo "<br/>";
	echo "</ul>";
?> 
</div>
<div class="related">
<?php 	
	if (!empty($company['Employee'])){
		echo "<h3>".__('Empleados de esta compañía')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('First Name')."</th>";
					echo "<th>".__('Last Name')."</th>";
					echo "<th>".__('Position')."</th>";
					//echo "<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach ($company['Employee'] as $employee){
				echo "<tr>";
					echo "<td>".$employee['first_name']."</td>";
					echo "<td>".$employee['last_name']."</td>";
					echo "<td>".$employee['position']."</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";
	}
?>
</div>

<div class="related">
<?php 	
	if (!empty($company['User'])){
		echo "<h3>".__('Usuarios de esta compañía')."</h3>";
		echo "<table cellpadding = '0' cellspacing = '0'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Username')."</th>";
					echo "<th>".__('Role')."</th>";
					echo "<th>".__('Department')."</th>";
					//echo "<th class='actions'>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			foreach ($company['User'] as $user){
				echo "<tr>";
					echo "<td>".$user['username']."</td>";
					echo "<td>".$user['Role']['name']."</td>";
					if (!empty($user['Department']['name'])){
						echo "<td>".$user['Department']['name']."</td>";
					}
					else {
						echo "<td>-</td>";
					}
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";
	}
?>
</div>