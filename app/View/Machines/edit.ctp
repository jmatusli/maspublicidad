<div class="machines form">
<?php 
	echo $this->Form->create('Machine'); 
	echo "<div class='col-md-6'>";
		echo "<fieldset>";
			echo "<legend>".__('Edit Machine')."</legend>";

			echo $this->Form->input('id',array('hidden'=>'hidden'));
			echo $this->Form->input('name');
			echo $this->Form->input('description');
			echo $this->Form->input('bool_active');

		echo "</fieldset>";
	echo "</div>";
	/*
	echo "<div id='ProductList' style='float:left;clear:none;' class='col-md-6'>"; 
		echo "<h3>Productos que se pueden producir con esta máquina</h3>";
		echo "<div class='col-md-6' style='float:left;clear:none;'> ";
		for ($p=0;$p<ceil(count($products)/2);$p++){
			$productChecked=false;
			if (!empty($products[$p]['MachineProduct'])){
				$productChecked=true;
			}
			echo $this->Form->input('Product.'.$p.'.product_id',array('type'=>'checkbox','checked'=>$productChecked,'label'=>$products[$p]['Product']['name'],'div'=>array('class'=>'checkboxleft')));
		}
		echo "</div>";
		echo "<div class='col-md-6' style='float:left;clear:none;'>";
		for ($p=ceil(count($products)/2);$p<count($products);$p++){
			$productChecked=false;
			if (!empty($products[$p]['MachineProduct'])){
				$productChecked=true;
			}
			echo $this->Form->input('Product.'.$p.'.product_id',array('type'=>'checkbox','checked'=>$productChecked,'label'=>$products[$p]['Product']['name'],'div'=>array('class'=>'checkboxleft')));
		}
		echo "</div>";
	
	echo "</div>"; 
	*/
	echo $this->Form->end(__('Submit')); 
?>
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		if ($bool_delete_permission){
			echo "<li>",$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Machine.id')), array(), __('Está seguro que quiere eliminar la máquina %s?', $this->Form->value('Machine.name')))."</li>";
			echo "<br/>";
		}
		echo "<li>".$this->Html->link(__('List Machines'), array('action' => 'index'))."</li>";
		echo "<br/>";
	echo "</ul>";
?>
</div>
