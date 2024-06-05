<div class="users index" style="overflow-x:auto">
<?php 
	echo "<h2>".__('Users')."</h2>";
  echo "<p class='comment'>Usuarios desactivados aparecen <i>en cursivo</i></p>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			//if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
			echo $this->Form->input('Report.company_id',array('label'=>__('Company'),'default'=>$companyId,'empty'=>array('0'=>__('All Companies'))));
			//}
		echo "</fieldset>";
		echo "<br/>";
	echo $this->Form->end(__('Refresh'));
	//echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
	
	//$excelOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	//$_SESSION['resumen'] = $excelOutput;
?>
	<p>
	<?php
	//echo $this->Paginator->counter(array(
	//'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	//));
	?>	</p>
	<div class="paging">
	<?php
		//echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		//echo $this->Paginator->numbers(array('separator' => ''));
		//echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_add_permission){
			echo "<li>".$this->Html->link(__('New User'), array('action' => 'add'))."</li>";
		}	
		echo "<br/>"; 
		//echo "<li>".$this->Html->link(__('List Roles'), array('controller' => 'roles', 'action' => 'index'))."</li>";
		//echo "<li>".$this->Html->link(__('New Role'), array('controller' => 'roles', 'action' => 'add'))."</li>";
		if ($userrole==ROLE_ADMIN) {
			//echo "<li>".$this->Html->link(__('List User Logs'), array('controller' => 'user_logs', 'action' => 'index'))."</li>";
		}	
		//echo "<li>".$this->Html->link(__('New User Log'), array('controller' => 'user_logs', 'action' => 'add'))."</li>";
	echo "</ul>";
?>
</div>
<div class='related'>
<?php
	echo "<table cellpadding='0' cellspacing='0' id='users'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th>". $this->Paginator->sort('username')."</th>";
				//echo "<!--th>". $this->Paginator->sort('password')."</th-->";
				echo "<th>". $this->Paginator->sort('role_id')."</th>";
				echo "<th>". $this->Paginator->sort('department_id')."</th>";
				echo "<th>". $this->Paginator->sort('company_id')."</th>";
				echo "<th>". $this->Paginator->sort('first_name')."</th>";
				echo "<th>". $this->Paginator->sort('last_name')."</th>";
				echo "<th>". $this->Paginator->sort('email')."</th>";
				echo "<th>". $this->Paginator->sort('phone')."</th>";
				echo "<th class='actions'>". __('Actions')."</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
		foreach ($users as $user){
      if ($user['User']['bool_active']){
        echo "<tr>";
      }
      else {
        echo "<tr class='italic'>";
      }
				echo "<td>". $this->Html->link($user['User']['username'],array('action'=>'view',$user['User']['id']))."</td>";
				//echo "<!--td>". h($user['User']['password'])."&nbsp;</td-->";
				echo "<td>". $this->Html->link($user['Role']['name'], array('controller' => 'roles', 'action' => 'view', $user['Role']['id']))."</td>";
				if (!empty($user['Department']['id'])){
					echo "<td>". $this->Html->link($user['Department']['name'], array('controller' => 'departments', 'action' => 'view', $user['Department']['id']))."</td>";
				}
				else {
					echo "<td>-</td>";
				}
				if (!empty($user['Company']['id'])){
					echo "<td>". $this->Html->link($user['Company']['name'], array('controller' => 'companies', 'action' => 'view', $user['Company']['id']))."</td>";
				}
				else {
					echo "<td>-</td>";
				}
				echo "<td>". h($user['User']['first_name'])."&nbsp;</td>";
				echo "<td>". h($user['User']['last_name'])."&nbsp;</td>";
				echo "<td>". h($user['User']['email'])."&nbsp;</td>";
				echo "<td>". h($user['User']['phone'])."&nbsp;</td>";
				echo "<td class='actions'>";
					//echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])); 
					if ($bool_edit_permission){
						echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])); 
					} 
					// echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), array(), __('Are you sure you want to delete %s?', $user['User']['username'])); 
				echo "</td>";
			echo "</tr>";
		}
		echo "</tbody>";
	echo "</table>";
?>
</div>