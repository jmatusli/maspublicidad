<div class="RejectedReasons form">
<?php echo $this->Form->create('RejectedReason'); ?>
	<fieldset>
		<legend><?php echo __('Add Rejected Reason'); ?></legend>
	<?php
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
		echo "<li>".$this->Html->link(__('List Rejected Reasons'), array('action' => 'index'))."</li>";
	echo "</ul>";
?>
</div>
