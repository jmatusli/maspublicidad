<div class="vendorCommissionPayments index">
<?php 
	echo "<h2>".__('Vendor Commission Payments')."</h2>";
	echo $this->Form->create('Report');
		echo "<fieldset>";
			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));
			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));
			if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT) { 
				echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId,'empty'=>array('0'=>__('Todos Usuarios'))));
			}
			else {
				echo $this->Form->input('Report.user_id',array('label'=>__('Mostrar Usuario'),'options'=>$users,'default'=>$userId));
			}
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
	echo $this->Form->end(__('Refresh'));
	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarResumen'), array( 'class' => 'btn btn-primary'));
?> 
</div>
<div class='actions'>
<?php 
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<li>".$this->Html->link(__('New Vendor Commission Payment'), array('action' => 'add'))."</li>";
		if ($bool_invoice_index_permission){
			echo "<br/>";
			echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))."</li>";
		}
		if ($bool_invoice_add_permission){
			echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))."</li>";
		}
	echo "</ul>";
?>
</div>
<div>
<?php
	$excelOutput="";

	foreach($selectedUsers as $selectedUser){
		$pageHeader="<thead>";
			$pageHeader.="<tr>";
				$pageHeader.="<th>".$this->Paginator->sort('user_id')."</th>";
				$pageHeader.="<th>".$this->Paginator->sort('invoice_id')."</th>";
				$pageHeader.="<th>".$this->Paginator->sort('payment_date')."</th>";
				$pageHeader.="<th>".$this->Paginator->sort('commission_paid')."</th>";
				$pageHeader.="<th class='actions'>".__('Actions')."</th>";
			$pageHeader.="</tr>";
		$pageHeader.="</thead>";
		$excelHeader="<thead>";
			$excelHeader.="<tr>";
				$excelHeader.="<th>".$this->Paginator->sort('user_id')."</th>";
				$excelHeader.="<th>".$this->Paginator->sort('invoice_id')."</th>";
				$excelHeader.="<th>".$this->Paginator->sort('payment_date')."</th>";
				$excelHeader.="<th>".$this->Paginator->sort('commission_paid')."</th>";
			$excelHeader.="</tr>";
		$excelHeader.="</thead>";

		$pageBody="";
		$excelBody="";

		foreach ($selectedUser['vendorCommissionPayments'] as $vendorCommissionPayment){ 
			$paymentDateTime=new DateTime($vendorCommissionPayment['VendorCommissionPayment']['payment_date']);
			$pageRow="";
				$pageRow.="<td>".$this->Html->link($vendorCommissionPayment['User']['username'], array('controller' => 'users', 'action' => 'view', $vendorCommissionPayment['User']['id']))."</td>";
				$pageRow.="<td>".$this->Html->link($vendorCommissionPayment['Invoice']['invoice_code'], array('controller' => 'invoices', 'action' => 'view', $vendorCommissionPayment['Invoice']['id']))."</td>";
				$pageRow.="<td>".$paymentDateTime->format('d-m-Y')."</td>";
				$pageRow.="<td>".h($vendorCommissionPayment['VendorCommissionPayment']['commission_paid'])."</td>";
			
			$excelBody.="<tr>".$pageRow."</tr>";

				$pageRow.="<td class='actions'>";
					$pageRow.=$this->Html->link(__('View'), array('action' => 'view', $vendorCommissionPayment['VendorCommissionPayment']['id']));
					if ($bool_edit_permission){
						$pageRow.=$this->Html->link(__('Edit'), array('action' => 'edit', $vendorCommissionPayment['VendorCommissionPayment']['id']));
					}
					if ($bool_delete_permission){
						$pageRow.=$this->Form->postLink(__('Delete'), array('action' => 'delete', $vendorCommissionPayment['VendorCommissionPayment']['id']), array(), 'Está seguro que quiere eliminar el pago de comisión para factura '.$vendorCommissionPayment['Invoice']['invoice_code'].'?');
					}
				$pageRow.="</td>";
				

			$pageBody.="<tr>".$pageRow."</tr>";
		}

		$pageTotalRow="";
		$pageTotalRow.="<tr class='totalrow'>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
			$pageTotalRow.="<td></td>";
		$pageTotalRow.="</tr>";

		$pageBody="<tbody>".$pageTotalRow.$pageBody.$pageTotalRow."</tbody>";
		$table_id="comisiones_por_pagar_".$selectedUser['User']['username'];
		$pageOutput="<table cellpadding='0' cellspacing='0' id='".$table_id."'>".$pageHeader.$pageBody."</table>";
		echo $pageOutput;
		$excelOutput.="<table id='".$table_id."'>".$excelHeader.$excelBody."</table>";
	}
	$_SESSION['resumen'] = $excelOutput;
?>
</div>
<script>
	function formatNumbers(){
		$("td.number span.amountright").each(function(){
			if (Math.abs(parseFloat($(this).text()))<0.001){
				$(this).text("0");
			}
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2,'.',',');
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("C$");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency").each(function(){
			
			if (parseFloat($(this).find('.amountright').text())<0){
				$(this).find('.amountright').prepend("-");
			}
			$(this).find('.amountright').number(true,2);
			$(this).find('.currency').text("US$");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatCSCurrencies();
		formatUSDCurrencies();
	});

</script>