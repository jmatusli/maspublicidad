<script>
	$('body').on('change','#InventoryProductInventoryProductLineId',function(){	
		var inventoryproductlineid=$(this).children("option").filter(":selected").val();
		if (inventoryproductlineid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>inventoryproducts/getproductsforinventoryproductlinelisted/',
				data:{"inventoryproductlineid":inventoryproductlineid},
				cache: false,
				type: 'POST',
				success: function (inventoryproducts) {
					$('#ProductList').html(inventoryproducts);
				},
				error: function(e){
					console.log(e);
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
		if (file_extension=="jpg"||file_extension=="jpeg"||file_extension=="png"||file_extension=="pdf"){
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
<?php echo $this->Form->create('InventoryProduct', array('enctype' => 'multipart/form-data')); ?>
	<fieldset>
		<legend><?php echo __('Edit Inventory Product')." ".$this->request->data['InventoryProduct']['name']; ?></legend>
	<?php
		echo $this->Form->input('id');
		
		echo $this->Form->input('inventory_product_line_id',array('empty'=>array('0'=>'Seleccione Categoría de Producto')));
		echo $this->Form->input('name');
		echo $this->Form->input('code');
		echo $this->Form->input('brand');
		echo $this->Form->input('description');
		echo $this->Form->input('measuring_unit_id');
		echo $this->Form->input('bool_promotion',array('label'=>'Producto Promocional'));
		echo $this->Form->input('width');
		if (!empty($this->request->data['InventoryProduct']['url_image'])){
			$url=$this->request->data['InventoryProduct']['url_image'];
			//pr($url);
			//echo "image url is ".$this->Html->url('/').$url."<br/>";
			echo "<img src='".$this->Html->url('/').$url."' alt='Producto' class='resize'></img>";
		}
		if (empty($this->request->data['InventoryProduct']['url_image'])){
			echo $this->Form->input('Document.url_image.0',array('label'=>'Cargar Imagen','type'=>'file'));
		}
		else {
			echo $this->Form->input('Document.url_image.0',array('label'=>'Cargar nueva Imagen','type'=>'file'));
		}
		
		echo $this->Form->input('currency_id');
		echo $this->Form->input('product_unit_cost',array('type'=>'decimal'));
		echo $this->Form->input('product_unit_price_A',array('label'=>'Precio Unitario A','type'=>'decimal'));
		echo $this->Form->input('product_unit_price_B',array('label'=>'Precio Unitario B','type'=>'decimal'));
		echo $this->Form->input('product_unit_price_C',array('label'=>'Precio Unitario C','type'=>'decimal'));
	?>
	</fieldset>

	<div id='InventoryProductList' style='width:45%;clear:left;' class='col-md-6'>
	</div>
	<div id='InventoryProviderList' style='width:45%;float:left;clear:none;' class='col-md-6'>
		<h3>Proveedores de Inventario Relacionados</h3>
	<?php
		//pr($inventoryProviders);
		for ($p=0;$p<count($inventoryProviders);$p++){
			$providerChecked=false;
			if (!empty($inventoryProviders[$p]['InventoryProductInventoryProvider'])){
				$providerChecked=true;
			}
			echo $this->Form->input('InventoryProvider.'.$p.'.inventory_provider_id',array('type'=>'checkbox','checked'=>$providerChecked,'label'=>$inventoryProviders[$p]['InventoryProvider']['name'],'div'=>array('class'=>'checkboxleft')));
		}
	?>
	</div>
	<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Product.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Product.id'))); ?></li-->
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
			echo "<li>".$this->Html->link(__('List Inventory Product Lines'), array('controller' => 'product_categories', 'action' => 'index'))."</li>";
		}
		if ($bool_inventoryproductline_add_permission){
			echo "<li>".$this->Html->link(__('New Inventory Product Line'), array('controller' => 'product_categories', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
