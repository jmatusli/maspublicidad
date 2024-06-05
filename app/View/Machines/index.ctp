<div class="machines index">
	<h2><?php echo __('Machines'); ?></h2>
	<?php //pr($machines); ?>
	<table cellpadding="0" cellspacing="0">
		<thead>
			<tr>
					<!--th><?php echo $this->Paginator->sort('id'); ?></th-->
					<th><?php echo $this->Paginator->sort('name'); ?></th>
					<th><?php echo $this->Paginator->sort('description'); ?></th>
					<!--th><?php echo $this->Paginator->sort('created'); ?></th-->
					<!--th><?php echo $this->Paginator->sort('modified'); ?></th-->
					<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php 
			foreach ($machines as $machine){
				if ($machine['Machine']['bool_active']){
					echo "<tr>";
				}
				else {
					echo "<tr class='italic'>";
				}
					echo "<td>".$this->Html->link($machine['Machine']['name'].($machine['Machine']['bool_active']?"":" (Deshabilitada)"),array('action'=>'view',$machine['Machine']['id']))."</td>";
					echo "<td>".$machine['Machine']['description']."</td>";
					echo "<td class='actions'>";
						echo $this->Html->link(__('View'), array('action' => 'view', $machine['Machine']['id'])); 
						if ($bool_edit_permission){
							echo $this->Html->link(__('Edit'), array('action' => 'edit', $machine['Machine']['id'])); 
						}
						if ($bool_delete_permission){
						// echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $machine['Machine']['id']), array(), __('Está seguro que quiere eliminar la máquina %s?', $machine['Machine']['name']));
						}
					echo "</td>";
				echo "</tr>";
			}
		?>
		</tbody>
	</table>
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Machine'), array('action' => 'add'))."</li>";
		echo "<br/>";
	echo "</ul>";
?>
</div>