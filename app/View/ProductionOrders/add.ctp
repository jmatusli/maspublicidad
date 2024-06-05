<script>
	$('body').on('change','#ProductionOrderSalesOrderId',function(){	
		loadSalesOrderInfo();
		getNewProductionOrderCode();
		loadSalesOrderProducts();
	});
	
	function loadSalesOrderInfo(){
		var sales_order_id=$('#ProductionOrderSalesOrderId').children("option").filter(":selected").val();
		if (sales_order_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>sales_orders/getsalesorderinfonofinance/',
				data:{"sales_order_id":sales_order_id},
				cache: false,
				type: 'POST',
				success: function (salesorderinfo) {
					$('#salesOrderInfo').html(salesorderinfo);
				},
				error: function(e){
					console.log(e);
					alert(e.ResponseText);
				}
			});
		}
		else {
			$('#salesOrderInfo').html("<div><span class='bold '>&nbsp;</span></div><div><span class='bold '>&nbsp;</span></div>");
		}
	}
	
	//$('body').on('change','#ProductionOrderDepartmentId',function(){	
	//	getNewProductionOrderCode();
	////	loadSalesOrderProducts();
	//});
	
	function getNewProductionOrderCode(){
		var sales_order_id=$('#ProductionOrderSalesOrderId').children("option").filter(":selected").val();
		if (sales_order_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>production_orders/getnewproductionordercode/',
				data:{"sales_order_id":sales_order_id},
				cache: false,
				type: 'POST',
				success: function (productionordercode) {
					$('#ProductionOrderProductionOrderCode').val(productionordercode);
				},
				error: function(e){
					console.log(e);
					alert(e.responseText);
				}
			});
		}
	}
	
	function loadSalesOrderProducts(){
		var sales_order_id=$('#ProductionOrderSalesOrderId').children("option").filter(":selected").val();
		if (sales_order_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>production_orders/getproductsforsalesorder/',
				data:{"sales_order_id":sales_order_id},
				cache: false,
				type: 'POST',
				success: function (products) {
					$('#ProductTable').html(products);
				},
				error: function(e){
					console.log(e);
					alert(e.ResponseText);
				}
			});
		}
		else {
			$('#ProductTable').empty();
		}
	}
	
	$('body').on('change','#ProductionOrderBoolAnnulled',function(){	
		hideFieldsForAnnulled();
	});
	
	function hideFieldsForAnnulled(){
		if ($('#ProductionOrderBoolAnnulled').is(':checked')){
			$('#ProductionOrderInstructions').parent().addClass('hidden');
			$('#productos').addClass('hidden');
		}
		else {
			$('#ProductionOrderInstructions').parent().removeClass('hidden');
			$('#productos').removeClass('hidden');
		}
	}
	
	$('body').on('click','.addItem',function(){
		var tableRow=$('#productos tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeItem',function(){
		var tableRow=$(this).closest('tr').remove();
		calculateTotal();
	});	
	
	$('body').on('change','.departmentid div select',function(){
		if ($(this).val()){
			var oneEmptyRowVisible=false;
			$(this).closest('td').find('div select:not(".hidden")').each(function(){
				if ($(this).val()==0){
					oneEmptyRowVisible=true;
				}
			});
			if (!oneEmptyRowVisible){
				var selectnextdepartment=$(this).closest('td').find('div select.hidden:first');
				var spannumber=$(this).closest('td').find('span.hidden:first');
				var spanplusbutton=$(this).closest('td').find('span.plusbutton.hidden:first');			
				selectnextdepartment.removeClass("hidden");
				spannumber.removeClass("hidden");
				spanplusbutton.removeClass("hidden");
			}
		}
		else {
			/* do nothing, because it should hold a value */
		}
	});	
	
	function calculateTotal(){
		var totalProductQuantity=0;
		$("#productos tbody tr:not(.hidden) td.productquantity div input").each(function() {
			if (!isNaN($(this).val())){
				var currentQuantity = parseFloat($(this).val());
				totalProductQuantity += currentQuantity;
			}
		});
		$('tr.totalrow td.productquantity span').text(totalProductQuantity.toFixed(0));
		
	}
	
	$('form').submit(function( e ) {          
		if ($('#DocumentUrlDocument0').val().length>0){
			if($('#DocumentUrlDocument0')[0].files[0].size > 5242880){
				alert("El documento excede 5MB!");        
				e.preventDefault();    
			}
			else {
				var file_extension=get_extension($('#DocumentUrlDocument0').val());
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
		if ($('#ProductionOrderProductionOrderCode').val()==""){
			getNewProductionOrderCode();
		}
		
		hideFieldsForAnnulled();
		
		$('#ProductionOrderRemarkUserId').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeDay').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeMonth').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeYear').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeHour').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeMin').addClass('fixed');
		$('#ProductionOrderRemarkRemarkDatetimeMeridian').addClass('fixed');
		
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
	
	$( document ).ajaxComplete(function() {
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>

<div class="productionOrders form fullwidth">
<?php 
	if (empty($salesOrders)){
		echo "<h2>No hay ordenes de venta sin orden de producción</h2>";
	}
	else {
		echo $this->Form->create('ProductionOrder', array('enctype' => 'multipart/form-data')); 
		echo "<fieldset>";
			echo "<legend>".__('Add Production Order')."</legend>";
			echo "<div class='container-fluid'>";
				echo "<div class='row'>";
					echo "<div class='col-md-5'>";
						echo $this->Form->input('sales_order_id',array('default'=>'0','value'=>$sales_order_id,'empty'=>array('0'=>'Seleccione Orden de Venta')));
						echo $this->Form->input('production_order_date',array('dateFormat'=>'DMY'));
						echo $this->Form->input('production_order_code');
						if ($bool_annul_permission){
							echo $this->Form->input('bool_annulled',array('default'=>false));
						}
						else {
							echo $this->Form->input('bool_annulled',array('default'=>'false','onclick'=>'return false'));
						}
						
						echo $this->Form->input('Document.url_doc.0',array('label'=>'Cargar Documento de Diseño (5 MB max)','type'=>'file'));
					echo "</div>";
					
					echo "<div class='col-md-5'>";
						echo "<div id='salesOrderInfo'>";
							echo "<div><span class='bold '>&nbsp;</span></div>";
							echo "<div><span class='bold '>&nbsp;</span></div>";
						echo "</div>";
		
						echo $this->Form->input('ProductionOrderRemark.user_id',array('label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden'));
						echo $this->Form->input('ProductionOrderRemark.remark_text',array('rows'=>'2','required'=>false));	
					echo "</div>";
					
					echo "<div class='col-md-2'>";
						echo "<h3>".__('Actions')."</h3>";
						echo "<ul>";
							echo "<li>".$this->Html->link(__('List Production Orders'), array('action' => 'index'))."</li>";
							echo "<br/>";
							if ($bool_salesorder_index_permission){
								echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))." </li>";
							}
							if ($bool_salesorder_add_permission){
								echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))." </li>";
							}
						echo "</ul>";
					echo "</div>";
				echo "</div>";				
				echo "<div class='row'>";
					echo "<div class='col-md-12' id='ProductTable'>";
						echo "<table id='productos'>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>".__('Product')."</th>";
									echo "<th>".__('Description')."</th>";
									echo "<th>".__('Instructions')."</th>";
									echo "<th>".__('Product Quantity')."</th>";
									echo "<th>".__('Operation Location')."</th>";
									echo "<th style='width:20%;'>".__('Department')."</th>";
									//echo "<th>".__('Actions')."</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";
							$i=0;
							$amountProducts=0;
							if (!empty($productsForSalesOrder)){
								foreach ($productsForSalesOrder as $product){
									//pr($product);
									echo "<tr row='".$i."'>";
										echo "<td class='productid'>";
											echo $this->Form->input('ProductionOrderProduct.'.$i.'.product_id',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_id'],'class'=>'fixed'));
											echo $this->Form->input('ProductionOrderProduct.'.$i.'.sales_order_product_id',array('label'=>false,'type'=>'hidden','value'=>$product['SalesOrderProduct']['id']));
										echo "</td>";
										echo "<td class='productdescription'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_description',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_description'],'readonly'=>'readonly'))."</td>";
										echo "<td class='productinstruction'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_instruction',array('label'=>false,'default'=>''))."</td>";
										echo "<td class='productquantity amount'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_quantity',array('label'=>false,'value'=>$product['SalesOrderProduct']['product_quantity'],'type'=>'numeric','readonly'=>'readonly'))."</td>";
										echo "<td class='operationlocationid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Lugar')))."</td>";
										echo "<td class='departmentid'>";
										for ($j=0;$j<count($departments);$j++){
											if ($j==0){
												echo "<span>".($j+1)."</span>".$this->Form->input('ProductionOrderProduct.'.$i.'.departments.'.$j.'.department_id',array('label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Departamento'),'div'=>array('style'=>'display:inline-block;')))."<span class='plusbutton'  >+</span>"."<br/>";
											}
											else {
												echo "<span class='hidden'>".($j+1)."</span>".$this->Form->input('ProductionOrderProduct.'.$i.'.departments.'.$j.'.department_id',array('label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Departamento'),'class'=>'hidden','div'=>array('style'=>'display:inline-block;')))."<span class='plusbutton hidden'>+</span>"."<br/>";
											}
										}
										echo "</td>";
										
									echo "</tr>";
									$i++;
									$amountProducts+=$product['SalesOrderProduct']['product_quantity'];
								}
							}
							else if (!empty($requestProducts)){
								foreach ($requestProducts as $product){
									//pr($product);
									echo "<tr row='".$i."'>";
										echo "<td class='productid'>";
											echo $this->Form->input('ProductionOrderProduct.'.$i.'.product_id',array('label'=>false,'value'=>$product['ProductionOrderProduct']['product_id'],'class'=>'fixed'));
											echo $this->Form->input('ProductionOrderProduct.'.$i.'.sales_order_product_id',array('label'=>false,'type'=>'hidden','value'=>$product['ProductionOrderProduct']['sales_order_product_id']));
										echo "</td>";
										echo "<td class='productdescription'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_description',array('label'=>false,'value'=>$product['ProductionOrderProduct']['product_description'],'readonly'=>'readonly'))."</td>";
										echo "<td class='productinstruction'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_instruction',array('label'=>false,'value'=>$product['ProductionOrderProduct']['product_instruction']))."</td>";
										echo "<td class='productquantity amount'>".$this->Form->input('ProductionOrderProduct.'.$i.'.product_quantity',array('label'=>false,'value'=>$product['ProductionOrderProduct']['product_quantity'],'type'=>'numeric','readonly'=>'readonly'))."</td>";
										echo "<td class='operationlocationid'>".$this->Form->input('ProductionOrderProduct.'.$i.'.operation_location_id',array('multiple'=>true,'label'=>false,'value'=>$product['ProductionOrderProduct']['operation_location_id'],'empty'=>array('0'=>'Seleccione Lugar')))."</td>";
										echo "<td class='departmentid'>";
										for ($j=0;$j<count($departments);$j++){
											if ($j==0){
												echo "<span>".($j+1)."</span>".$this->Form->input('ProductionOrderProduct.'.$i.'.departments.'.$j.'.department_id',array('label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Departamento'),'div'=>array('style'=>'display:inline-block;')))."<span class='plusbutton'  >+</span>"."<br/>";
											}
											else {
												echo "<span class='hidden'>".($j+1)."</span>".$this->Form->input('ProductionOrderProduct.'.$i.'.departments.'.$j.'.department_id',array('label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Departamento'),'class'=>'hidden','div'=>array('style'=>'display:inline-block;')))."<span class='plusbutton hidden'>+</span>"."<br/>";
											}
										}
										echo "</td>";
										
									echo "</tr>";
									$i++;
									$amountProducts+=$product['ProductionOrderProduct']['product_quantity'];
								}
							}
								
								//for ($j=$i;$j<30;$j++){
								//	//pr($product);
								//	if ($j==$i){
								//		echo "<tr row='".$j."'>";
								//	}
								//	else {
								//		echo "<tr row='".$j."' class='hidden'>";
								//	}
								//		echo "<td class='productid'>".$this->Form->input('ProductionOrderProduct.'.$j.'.product_id',array('label'=>false,'default'=>0,'empty'=>array('0'=>'Seleccione Producto')))."</td>";
								//		echo "<td class='productquantity amount'>".$this->Form->input('ProductionOrderProduct.'.$j.'.product_quantity',array('label'=>false,'default'=>'0','type'=>'numeric'))."</td>";
								//		echo "<td class='operationlocationid'>".$this->Form->input('ProductionOrderProduct.'.$j.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Lugar')))."</td>";
								//		echo "<td>";
								//				echo "<button class='removeItem' type='button'>".__('Remove Item')."</button>";
								//				echo "<button class='addItem' type='button'>".__('Add Item')."</button>";
								//		echo "</td>";
								//	echo "</tr>";
								//}
								echo "<tr class='totalrow'>";
									echo "<td>Total</td>";
									echo "<td></td>";
									echo "<td></td>";
									echo "<td class='productquantity amount centered'><span></span></td>";
									echo "<td></td>";
									echo "<td></td>";
								echo "</tr>";	
							echo "</tbody>";
						echo "</table>";
						
					echo "</div>";
				echo "</div>";				
				
				echo $this->Form->input('instructions');
			echo "</div>";
			
		echo "</fieldset>";
		echo $this->Form->end(__('Submit')); 
	}
?>
</div>
