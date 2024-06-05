<script>
	$('body').on('change','#ProductionProcessDepartmentId',function(){	
		getNewProductionProcessCode();
		loadPendingProductsForDepartment();
	});
	
	function getNewProductionProcessCode(){
		var department_id=$('#ProductionProcessDepartmentId').children("option").filter(":selected").val();
		if (department_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>production_processes/getnewproductionprocesscode/',
				data:{"department_id":department_id},
				cache: false,
				type: 'POST',
				success: function (productionprocesscode) {
					$('#ProductionProcessProductionProcessCode').val(productionprocesscode);
				},
				error: function(e){
					console.log(e);
					alert(e.responseText);
				}
			});
		}
	}
		
	function loadPendingProductsForDepartment(){
		var department_id=$('#ProductionProcessDepartmentId').val();
		var production_process_id=<?php echo $production_process_id; ?>;
		//var affectedproductid=$(this).closest('tr').find('.purchaseorderproductid div select').attr('id');		
		if (department_id>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>sales_order_products/getprocessproductsfordepartment/',
				data:{"department_id":department_id,"production_process_id":production_process_id},
				cache: false,
				type: 'POST',
				success: function (productoptions) {
					//$('#'+affectedproductid).html(productoptions);
					$('td.salesorderproductid div select').each(function(){
						var salesorderproductidvalue=$(this).val();
						$(this).html(productoptions);
						$(this).val(salesorderproductidvalue);
					});
					//$('td.salesorderproductid div select').html(productoptions);
				},
				error: function(e){
					console.log(e);
					alert(e.responseText);
				}
			});
		}
		else {
			$('td.salesorderproductid div select').html("<option value='0'>Seleccione Producto</option>");
		}
	}

	$('body').on('change','#ProductionProcessBoolAnnulled',function(){	
		hideFieldsForAnnulled();
	});
	
	function hideFieldsForAnnulled(){
		if ($('#ProductionProcessBoolAnnulled').is(':checked')){
			$('#productos').addClass('hidden');
		}
		else {
			
			$('#productos').removeClass('hidden');
		}
	}
	
	$('body').on('change','td.salesorderproductid div select.sales_order_product_id',function(){	
		var currentrow=$(this).closest('tr');
		var salesorderproductid=$(this).val();		
		var affectedsalesorderproductid=$(this).attr('id');		
		if (salesorderproductid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>sales_order_products/getsalesorderproductinfo/',
				data:{"salesorderproductid":salesorderproductid},
				dataType:'json',
				cache: false,
				type: 'POST',
				success: function (salesorderproductinfo) {
					currentrow.find('td.salesorderproductid div input.product_id').val(salesorderproductinfo['Product']['id']);
					currentrow.find('td.productdescription div textarea').val(salesorderproductinfo['SalesOrderProduct']['product_description']);
					currentrow.find('td.productionorderid div input.production_order_id').val(salesorderproductinfo['ProductionOrderProduct'][0]['ProductionOrder']['id']);
					currentrow.find('td.productionorderid div input.production_order_code').val(salesorderproductinfo['ProductionOrderProduct'][0]['ProductionOrder']['production_order_code']);
					currentrow.find('td.pendingquantity div input').val(salesorderproductinfo['SalesOrderProduct']['product_quantity']);
					currentrow.find('td.productquantity div input').val(salesorderproductinfo['SalesOrderProduct']['product_quantity']);
					currentrow.find('td.operationlocationid div select').val(salesorderproductinfo['ProductionOrderProduct'][0]['operationlocations']);
				},
				error: function(e){
					console.log(e);
					alert(e.responseText);
				}
			});
		}
		else {
			currentrow.find('td.salesorderproductid div input.product_id').val(0);
			currentrow.find('td.productdescription div textarea').val('');
			currentrow.find('td.productionorderid div input.production_order_id').val(0);
			currentrow.find('td.productionorderid div input.production_order_code').val('');
			currentrow.find('td.pendingquantity div input').val(0);
			currentrow.find('td.productquantity div input').val(0);
			currentrow.find('td.operationlocationid div select').val(0);
		}
		//CHECK IF QUANTITY IS NOT BIGGER THAN THE QUANTITY IN THE PURCHASE ORDER
	});
	
	$('body').on('change','.productquantity',function(){	
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		else {
			var roundedValue=Math.round($(this).find('div input').val());
			var pendingQuantity=$(this).closest('tr').find('td.pendingquantity div input').val();
			if (roundedValue>pendingQuantity){
				$(this).find('div input').val(pendingQuantity);
				alert("La cantidad entregada no puede estar mayor que la cantidad pendiente");
			}
			else {
				$(this).find('div input').val(roundedValue);
			}
		}
	});	
	
	$('body').on('click','.addItem',function(){
		var tableRow=$('#productos tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeItem',function(){
		var tableRow=$(this).closest('tr').remove();
		calculateTotal();
	});	
	
	$(document).ready(function(){
		if ($('#ProductionProcessProductionProcessCode').val()==""){
			getNewProductionProcessCode();
		}
		loadPendingProductsForDepartment();
		hideFieldsForAnnulled();
		
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
	
	$( document ).ajaxComplete(function() {
		$('select.fixed option:not(:selected)').attr('disabled', true);
	});
</script>

<div class="productionProcesses form fullwidth">
<?php 
	echo $this->Form->create('ProductionProcess'); 
	echo "<fieldset>";
		echo "<legend>".__('Edit Production Process')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='row'>";
				echo "<div class='col-md-6'>";
					echo $this->Form->input('production_process_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('department_id',array('empty'=>array('0'=>'Seleccione Departamento')));
					echo $this->Form->input('production_process_code');
					echo $this->Form->input('bool_annulled');
				echo "</div>";
				
				echo "<div class='col-md-6'>";
					echo "<div id='salesOrderInfo'>";
						echo "<div><span class='bold '>&nbsp;</span></div>";
						echo "<div><span class='bold '>&nbsp;</span></div>";
					echo "</div>";
	
					echo $this->Form->input('ProductionProcessRemark.user_id',array('label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden'));
					echo $this->Form->input('ProductionProcessRemark.remark_text',array('rows'=>'2'));	
					
					if (!empty($productionProcessRemarks)){
						echo "<table>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>Fecha</th>";
									echo "<th>Vendedor</th>";
									echo "<th>Remarca</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";							
							foreach ($productionProcessRemarks as $productionProcessRemark){
								//pr($productionProcessRemark);
								$remarkDateTime=new DateTime($productionProcessRemark['ProductionProcessRemark']['remark_datetime']);
								echo "<tr>";
									echo "<td>".$remarkDateTime->format('d-m-Y H:i')."</td>";
									echo "<td>".$productionProcessRemark['ProductionProcessRemark']['User']['username']."</td>";
									echo "<td>".$productionProcessRemark['ProductionProcessRemark']['remark_text']."</td>";
								echo "</tr>";
							}
							echo "</tbody>";
						echo "</table>";
					}
				echo "</div>";
			echo "</div>";				
			echo "<div class='row'>";
				echo "<div class='col-md-12' id='ProductTable'>";
					echo "<table id='productos'>";
						echo "<thead>";
							echo "<tr>";
								echo "<th>".__('Product')."</th>";
								echo "<th>".__('Description')."</th>";
								echo "<th>".__('Production Order')."</th>";
								echo "<th>".__('Pending Quantity')."</th>";
								echo "<th>".__('Product Quantity')."</th>";
								echo "<th>".__('Operator')."</th>";
								echo "<th>".__('Machine')."</th>";
								echo "<th>".__('Operation Location')."</th>";
								echo "<th>".__('Actions')."</th>";
							echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						$i=0;
						
						for ($i=0;$i<count($requestProducts);$i++){
							echo "<tr row='".$i."'>";
								echo "<td class='salesorderproductid'>";
									echo $this->Form->input('ProductionProcessProduct.'.$i.'.product_id',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['product_id'],'type'=>'hidden','class'=>'product_id'));
									echo $this->Form->input('ProductionProcessProduct.'.$i.'.sales_order_product_id',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['sales_order_product_id'],'empty'=>array('0'=>'Seleccione Producto'),'class'=>'sales_order_product_id'));
								echo "</td>";
								echo "<td class='productdescription'>".$this->Form->input('ProductionProcessProduct.'.$i.'.product_description',array('label'=>false,'rows'=>2,'value'=>$requestProducts[$i]['ProductionProcessProduct']['product_description'],'readonly'=>'readonly'))."</td>";
								echo "<td class='productionorderid'>";
									echo $this->Form->input('ProductionProcessProduct.'.$i.'.production_order_id',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['production_order_id'],'type'=>'hidden','class'=>'production_order_id'));
									echo $this->Form->input('ProductionProcessProduct.'.$i.'.production_order_code',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['production_order_code'],'type'=>'text','class'=>'production_order_code'));
								echo "</td>";
								echo "<td class='pendingquantity amount'>".$this->Form->input('ProductionProcessProduct.'.$i.'.pending_quantity',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['product_quantity'],'type'=>'decimal','readonly'=>'readonly'))."</td>";
								echo "<td class='productquantity amount'>".$this->Form->input('ProductionProcessProduct.'.$i.'.product_quantity',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['product_quantity'],'type'=>'decimal'))."</td>";
								echo "<td class='operatorid'>".$this->Form->input('ProductionProcessProduct.'.$i.'.operator_id',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['operator_id'],'empty'=>array('0'=>'Seleccione Operador')))."</td>";
								echo "<td class='machineid'>".$this->Form->input('ProductionProcessProduct.'.$i.'.machine_id',array('label'=>false,'value'=>$requestProducts[$i]['ProductionProcessProduct']['machine_id'],'empty'=>array('0'=>'Seleccione Máquina')))."</td>";
								echo "<td class='operationlocationid'>".$this->Form->input('ProductionProcessProduct.'.$i.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','value'=>$requestProducts[$i]['ProductionProcessProduct']['locationIds'],'empty'=>array('0'=>'Seleccione Lugar')))."</td>";
								echo "<td>";
									echo "<button class='removeItem' type='button'>".__('Remove Item')."</button>";
									echo "<button class='addItem' type='button'>".__('Add Item')."</button>";
								echo "</td>";
							echo "</tr>";
						}						
						for ($j=$i;$j<30;$j++){
							//pr($product);
							if ($j==$i){
								echo "<tr row='".$j."'>";
							}
							else {
								echo "<tr row='".$j."' class='hidden'>";
							}
								echo "<td class='salesorderproductid'>";
									echo $this->Form->input('ProductionProcessProduct.'.$j.'.product_id',array('label'=>false,'default'=>0,'type'=>'hidden','class'=>'product_id'));
									echo $this->Form->input('ProductionProcessProduct.'.$j.'.sales_order_product_id',array('label'=>false,'default'=>0,'empty'=>array('0'=>'Seleccione Producto'),'class'=>'sales_order_product_id'));
								echo "</td>";
								echo "<td class='productdescription'>".$this->Form->input('ProductionProcessProduct.'.$j.'.product_description',array('label'=>false,'rows'=>2,'default'=>'','readonly'=>'readonly'))."</td>";
								echo "<td class='productionorderid'>";
									echo $this->Form->input('ProductionProcessProduct.'.$i.'.production_order_id',array('label'=>false,'default'=>0,'type'=>'hidden','class'=>'production_order_id'));
									echo $this->Form->input('ProductionProcessProduct.'.$i.'.production_order_code',array('label'=>false,'default'=>'','type'=>'text','class'=>'production_order_code'));
								echo "</td>";
								echo "<td class='pendingquantity amount'>".$this->Form->input('ProductionProcessProduct.'.$j.'.pending_quantity',array('label'=>false,'default'=>0,'type'=>'decimal','readonly'=>'readonly'))."</td>";
								echo "<td class='productquantity amount'>".$this->Form->input('ProductionProcessProduct.'.$j.'.product_quantity',array('label'=>false,'default'=>0,'type'=>'decimal'))."</td>";
								echo "<td class='operatorid'>".$this->Form->input('ProductionProcessProduct.'.$j.'.operator_id',array('label'=>false,'default'=>0,'empty'=>array('0'=>'Seleccione Operador')))."</td>";
								echo "<td class='machineid'>".$this->Form->input('ProductionProcessProduct.'.$j.'.machine_id',array('label'=>false,'default'=>0,'empty'=>array('0'=>'Seleccione Máquina')))."</td>";
								echo "<td class='operationlocationid'>".$this->Form->input('ProductionProcessProduct.'.$j.'.operation_location_id',array('multiple'=>true,'label'=>false,'default'=>'0','empty'=>array('0'=>'Seleccione Lugar')))."</td>";
								echo "<td>";
										echo "<button class='removeItem' type='button'>".__('Remove Item')."</button>";
										echo "<button class='addItem' type='button'>".__('Add Item')."</button>";
								echo "</td>";
							echo "</tr>";
						}
							//echo "<tr class='totalrow'>";
							//	echo "<td>Total</td>";
							//	echo "<td></td>";
							//	echo "<td>".$amountProducts."</td>";
							//	echo "<td></td>";
							//echo "</tr>";		
						echo "</tbody>";
					echo "</table>";
					
				echo "</div>";
			echo "</div>";				
		echo "</div>";
		
	echo "</fieldset>";
	echo $this->Form->end(__('Submit')); 
?>
</div>
<!--div class='actions'>
<?php 
	/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('ProductionProcess.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('ProductionProcess.id')))."</li>";
		echo "<li>".$this->Html->link(__('List Production Processes'), array('action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Departments'), array('controller' => 'departments', 'action' => 'index'))." </li>";
		echo "<li>".$this->Html->link(__('New Department'), array('controller' => 'departments', 'action' => 'add'))." </li>";
	echo "</ul>";
	*/
?>
</div-->

