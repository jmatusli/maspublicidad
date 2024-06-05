<script>
	$('body').on('change','#ProductProductCategoryId',function(){	
		var productcategoryid=$(this).children("option").filter(":selected").val();
		if (productcategoryid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>products/getproductsforproductcategorylisted/',
				data:{"productcategoryid":productcategoryid},
				cache: false,
				type: 'POST',
				success: function (products) {
					$('#ProductList').html(products);
				},
				error: function(e){
					console.log(e);
				}
			});
		}
		else {
			$('#ProductList').empty();
		}
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
		$('#ProductProductCategoryId').trigger('change');
	});
</script>
<div class="products form">
<?php echo $this->Form->create('Product', array('enctype' => 'multipart/form-data')); ?>
	<fieldset>
		<legend><?php echo __('Edit Product')." ".$this->request->data['Product']['name']; ?></legend>
	<?php
		echo $this->Form->input('id');
		
		echo $this->Form->input('product_category_id',array('empty'=>array('0'=>'Seleccione Categoría de Producto')));
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('code');
		if (!empty($this->request->data['Product']['url_image'])){
			$url=$this->request->data['Product']['url_image'];
			//pr($url);
			//echo "image url is ".$this->Html->url('/').$url."<br/>";
			echo "<img src='".$this->Html->url('/').$url."' alt='Producto' class='resize'></img>";
		}
		if (empty($this->request->data['Product']['url_image'])){
			echo $this->Form->input('Document.url_image.0',array('label'=>'Cargar Imagen','type'=>'file'));
		}
		else {
			echo $this->Form->input('Document.url_image.0',array('label'=>'Cargar nueva Imagen','type'=>'file'));
		}
		
		echo $this->Form->input('currency_id');
		echo $this->Form->input('product_unit_price');
		echo $this->Form->input('product_unit_cost');
		echo $this->Form->input('bool_no_iva',array('label'=>'Producto sin IVA'));
    echo $this->Form->input('bool_active',array('label'=>'Activo'));
		echo $this->Form->Submit(__('Submit'));
	?>
		<div id='ProductList' style='width:45%;clear:left;' class='col-md-6'>
		</div>
		<div id='ProviderList' style='width:45%;float:left;clear:none;' class='col-md-6'>
			<h3>Proveedores Relacionados</h3>
		<?php
			//pr($providers);
			for ($p=0;$p<count($providers);$p++){
				$providerChecked=false;
				if (!empty($providers[$p]['ProductProvider'])){
					$providerChecked=true;
				}
				echo $this->Form->input('Provider.'.$p.'.provider_id',array('type'=>'checkbox','checked'=>$providerChecked,'label'=>$providers[$p]['Provider']['name'],'div'=>array('class'=>'checkboxleft')));
			}
		?>
		</div>
	</fieldset>
	<?php echo $this->Form->Submit(__('Submit')); ?>
	<?php echo $this->Form->end(); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<!--li><?php //echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Product.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Product.id'))); ?></li-->
		<li><?php echo $this->Html->link(__('List Products'), array('action' => 'index')); ?></li>
		<br/>
	<?php
		if ($bool_provider_index_permission){
			echo "<li>".$this->Html->link(__('List Providers'), array('controller' => 'providers', 'action' => 'index'))."</li>";
		}
		if ($bool_provider_index_permission){
			echo "<li>".$this->Html->link(__('New Provider'), array('controller' => 'providers', 'action' => 'add'))."</li>";
		}
		if ($bool_productcategory_index_permission){
			echo "<li>".$this->Html->link(__('List Product Categories'), array('controller' => 'product_categories', 'action' => 'index'))."</li>";
		}
		if ($bool_productcategory_add_permission){
			echo "<li>".$this->Html->link(__('New Product Category'), array('controller' => 'product_categories', 'action' => 'add'))."</li>";
		}
	?>
	</ul>
</div>
