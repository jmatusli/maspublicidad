<div class="RejectedReasons form">
<?php echo $this->Form->create('RejectedReason'); ?>
	<fieldset>
		<legend><?php echo __('Edit Rejected Reason'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('list_order');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class='actions'>
<?php
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('RejectedReason.id')), array(), __('Está seguro que quiere eliminar la razón de caída %s?', $this->Form->value('RejectedReason.name')))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Rejected Reasons'), array('action' => 'index'))."</li>";
	echo "</ul>";
?>
</div>
