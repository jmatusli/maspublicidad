<script>
	$('body').on('change','#InventoryProductInventoryProductLineId',function(){	
		var inventoryproductlineid=$(this).children("option").filter(":selected").val();
		if (inventoryproductlineid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>inventory_products/getproductsforinventoryproductlinelisted/',
				data:{"inventoryproductlineid":inventoryproductlineid},
				cache: false,
				type: 'POST',
				success: function (inventoryproducts) {
					$('#InventoryProductList').html(inventoryproducts);
				},
				error: function(e){
					console.log(e);
					alert(e.ResponseText);
				}
			});
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>inventory_product_lines/getboolpromotion/',
				data:{"inventoryproductlineid":inventoryproductlineid},
				cache: false,
				type: 'POST',
				success: function (boolpromotion) {
					if (boolpromotion){
						$('#InventoryProductBoolPromotion').prop('checked',true);
					}
					else {
						$('#InventoryProductBoolPromotion').prop('checked',false);
					}
					displayPrices();
				},
				error: function(e){
					console.log(e);
					alert(e.ResponseText);
				}
			});
		}
		else {
			$('#InventoryProductList').empty();
		}
		if ($('#InventoryProductInventoryProductLineId').val()!=<?php echo INVENTORY_PRODUCT_LINE_PER_METER; ?>){
			$('#InventoryProductWidth').parent().addClass('hidden');
		}
		else {
			$('#InventoryProductWidth').parent().removeClass('hidden');
		}
	});
	
	$('body').on('change','#InventoryProductBoolPromotion',function(){	
		displayPrices();
	});
	
	function displayPrices(){
		if ($('#InventoryProductBoolPromotion').is(':checked')){
			$('#InventoryProductProductUnitPriceA').parent().removeClass('hidden');
			$('#InventoryProductProductUnitPriceB').parent().removeClass('hidden');
			$('#InventoryProductProductUnitPriceC').parent().removeClass('hidden');
		}
		else {
			$('#InventoryProductProductUnitPriceA').parent().addClass('hidden');
			$('#InventoryProductProductUnitPriceB').parent().addClass('hidden');
			$('#InventoryProductProductUnitPriceC').parent().addClass('hidden');
		}
	}
	
	$('body').on('change','#InventoryProductProductUnitCost',function(){	
		var unitcost=$(this).val();
		var unitpriceA=roundToTwo(unitcost*1.06);
		var unitpriceB=roundToTwo(unitcost*1.05);
		var unitpriceC=roundToTwo(unitcost*1.04);
		$('#InventoryProductProductUnitPriceA').val(unitpriceA);
		$('#InventoryProductProductUnitPriceB').val(unitpriceB);
		$('#InventoryProductProductUnitPriceC').val(unitpriceC);
	});
	
	$('body').on('submit','form',function(e){	
		if ($('#DocumentUrlImage0').val().length>0){
			if($('#DocumentUrlImage0')[0].files[0].size > 5242880){
				alert("La imagen excede 5MB!");        
				e.preventDefault();    
			}
			else {
				var file_extension=get_extension($('#DocumentUrlImage0').val());
				var bool_valid_extension=check_validity_extension(file_extension);
				if (!bool_valid_extension){
					alert("Solamente se permiten archivos jpg, jpeg, png y pdf!");        
					e.preventDefault();    
				}
			}
		}
	});
	
	function get_extension(filename) {    
		var parts = filename.split('.');    
		return parts[parts.length - 1].toLowerCase();
	}
	
	function check_validity_extension(file_extension) {    
		if (file_extension=="jpg" ||file_extension=="jpeg" ||file_extension=="png" ||file_extension=="pdf"){
			return true;
		} 
		else {
			return false;
		}
	}
	
	$(document).ready(function(){
		$('#InventoryProductInventoryProductLineId').trigger('change');
		displayPrices();
	});
</script>
<div class="products form">
<?php 
	echo $this->Form->create('InventoryProduct', array('enctype' => 'multipart/form-data')); 
	echo "<fieldset>";
		echo "<legend>".__('Add Inventory Product')."</legend>";
	
		echo $this->Form->input('inventory_product_line_id',array('default'=>'0','empty'=>array('0'=>'Seleccione Línea de Producto')));
		echo $this->Form->input('name');
		echo $this->Form->input('code');
		echo $this->Form->input('brand');
		echo $this->Form->input('description');
		echo $this->Form->input('measuring_unit_id');
		echo $this->Form->input('bool_promotion',array('label'=>'Producto Promocional'));
		echo $this->Form->input('width',array('default'=>'0'));
		echo $this->Form->input('Document.url_image.0',array('label'=>'Cargar Imagen','type'=>'file'));
		echo $this->Form->input('currency_id',array('default'=>CURRENCY_USD));
		echo $this->Form->input('product_unit_cost',array('type'=>'decimal'));
		echo $this->Form->input('product_unit_price_A',array('label'=>'Precio Unitario A','type'=>'decimal'));
		echo $this->Form->input('product_unit_price_B',array('label'=>'Precio Unitario B','type'=>'decimal'));
		echo $this->Form->input('product_unit_price_C',array('label'=>'Precio Unitario C','type'=>'decimal'));
	echo "</fieldset>";
	echo "<div class='container-fluid'>";
		echo "<div class='row'>";
			echo "<div id='InventoryProductList' class='col-md-6'>";
			
			echo "</div>";
			echo "<div id='InventoryProviderList' class='col-md-6'>";
				echo "<h3>Proveedores Relacionados</h3>";
				for ($p=0;$p<count($inventoryProviders);$p++){
					echo $this->Form->input('InventoryProvider.'.$p.'.inventory_provider_id',array('type'=>'checkbox','checked'=>false,'label'=>$inventoryProviders[$p]['InventoryProvider']['name'],'div'=>array('class'=>'checkboxleft')));
				}
			echo "</div>";
		echo "</div>";
	echo "</div>";
	echo $this->Form->end(__('Submit')); 
?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Inventory Products'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_inventoryprovider_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Providers'), array('controller' => 'inventory_providers', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryprovider_index_permission){
			echo "<li>".$this->Html->link(__('New Inventory Provider'), array('controller' => 'inventory_providers', 'action' => 'add'))."</li>";
		}
		if ($bool_inventoryproductline_index_permission){
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'inventory_product_lines', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'inventory_product_lines', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
