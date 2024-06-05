<div class="sales index fullwidth">
<?php	
	$excel="";
	
	echo "<h2>".__('Reporte de Cotizaciones por Categoría y Producto')."</h2>";
	echo "<div class='container-fluid'>";
		echo "<div class='rows'>";
			echo "<div class='col-md-6'>";
				echo $this->Form->create('Report'); 
					echo "<fieldset>";
						echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')));
						echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')));
						echo "<br/>";
						echo $this->Form->input('Report.currency_id',array('label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId));
						echo "<br/>";			
						echo $this->Form->input('Report.product_category_id',array('label'=>__('Product Category'),'default'=>$product_category_id,'empty'=>array('0'=>__('Seleccione Categoría de Producto'))));
						echo $this->Form->input('Report.product_id',array('label'=>__('Product'),'default'=>'0','empty'=>array('0'=>__('Seleccione Producto'))));
					echo "</fieldset>";
					echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
					echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
					echo "<br/>";
				echo $this->Form->end(__('Refresh')); 
				echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteCotizacionesPorCategoria'), array( 'class' => 'btn btn-primary')); 
			echo "</div>";
			echo "<div class='col-md-6'>";
				echo "<h3>Resumen Totales por Categoría</h3>";
				$overviewTable="";
				$overviewTable.="<table id='resumen_cotizaciones_categoria'>";
					$overviewTable.="<thead>";
						$overviewTable.="<tr>";
							$overviewTable.="<th>Categoría</th>";
							$overviewTable.="<th>Precio Subtotal</th>";
						$overviewTable.="</tr>";
					$overviewTable.="</thead>";
					$overviewTable.="<tbody>";
					foreach ($selectedProductCategories as $productCategory){
						$overviewTable.="<tr>";
							$overviewTable.="<td>".$productCategory['ProductCategory']['name']."</td>";
							switch ($currencyId){
								case CURRENCY_CS:
									$overviewTable.="<td class='CScurrency'><span class='amountright'>".$productCategory['total_price_CS']."</span></td>";
									break;
								case CURRENCY_USD:
									$overviewTable.="<td class='USDcurrency'><span class='amountright'>".$productCategory['total_price_USD']."</span></td>";
									break;
							}
						$overviewTable.="</tr>";
					}
					$overviewTable.="</tbody>";
				$overviewTable.="</table>";
				echo $overviewTable;
				$excel.=$overviewTable;
			echo "</div>";
		echo "</div>";
	echo "</div>";
	
	$startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
	
	$output="";
	foreach ($selectedProductCategories as $productCategory){
		$showCategory=true;
		if (!empty($product_category_id)){
			if ($productCategory['ProductCategory']['id']!=$product_category_id){
				$showCategory=false;
			}
		}
		//pr($productCategory);
		if (!empty($productCategory['QuotationProducts'])&&$showCategory){
			$title="Cotizaciones para Categoría ".$this->Html->Link($productCategory['ProductCategory']['name'],array('controller'=>'product_categories','action'=>'view',$productCategory['ProductCategory']['id']));
			if (count($selectedProducts)==1){
				$title.=" y Producto ".$this->Html->Link($selectedProducts[0]['Product']['name'],array('controller'=>'products','action'=>'view',$selectedProducts[0]['Product']['id']));
			}
			echo "<h3>".$title."</h3>";
			$outputhead="<thead>";
				$outputhead.="<tr>";
					$outputhead.="<th>".__('Date')."</th>";
					$outputhead.="<th>".__('Quotation Code')."</th>";
					$outputhead.="<th>".__('Orden de Venta')."</th>";
					$outputhead.="<th>".__('Vendedor')."</th>";
					$outputhead.="<th>".__('Client')."</th>";
					$outputhead.="<th>".__('Product')."</th>";
					$outputhead.="<th>".__('Quantity')."</th>";
					$outputhead.="<th class='right'>".__('Precio Total')."</th>";
					$outputhead.="<th>".__('Caído')."</th>";
					$outputhead.="<th>".__('Vendido')."</th>";
				$outputhead.="</tr>";
			$outputhead.="</thead>";
		
			$excelhead="<thead>";
				$excelhead.="<tr><th colspan='10' align='center'>".COMPANY_NAME."</th></tr>";	
				$excelhead.="<tr><th colspan='10' align='center'>".__('Reporte Cotizaciones por Departamento')." de ".$startDateTime->format('d-m-Y')." hasta ".$endDateTime->format('d-m-Y')."</th></tr>";
				$excelhead.="<tr>";
					$excelhead.="<th>".__('Date')."</th>";
					$excelhead.="<th>".__('Quotation Code')."</th>";
					$excelhead.="<th>".__('Orden de Venta')."</th>";
					$excelhead.="<th>".__('Vendedor')."</th>";
					$excelhead.="<th>".__('Client')."</th>";
					$excelhead.="<th>".__('Product')."</th>";
					$excelhead.="<th>".__('Quantity')."</th>";
					$excelhead.="<th class='right'>".__('Precio Total')."</th>";
					$excelhead.="<th>".__('Caído')."</th>";
					$excelhead.="<th>".__('Vendido')."</th>";
				$excelhead.="</tr>";
			$excelhead.="</thead>";
		
			$totalTotalCS=0;
			$totalTotalUSD=0;
			$totalDropped=0;
			$totalSold=0;
		
			$bodyRows="";
			foreach ($productCategory['QuotationProducts'] as $product){
				//pr($product);
				$quotationDate=new DateTime($product['Quotation']['quotation_date']);
				$currencyClass="";
				if ($product['QuotationProduct']['currency_id']==CURRENCY_CS){
					$totalTotalCS+=$product['QuotationProduct']['product_total_price'];
					//added calculation of totals in US$
					$totalTotalUSD+=round($product['QuotationProduct']['product_total_price']/$product['QuotationProduct']['exchange_rate'],2);
					$currencyClass="class='CScurrency'";
					
					// dropped and sold calculated in US dollars
					$totalDropped+=round($product['QuotationProduct']['dropped']*$product['QuotationProduct']['product_total_price']/$product['QuotationProduct']['exchange_rate'],2);
					$totalSold+=round($product['QuotationProduct']['sold']*$product['QuotationProduct']['product_total_price']/$product['QuotationProduct']['exchange_rate'],2);
				}
				else if ($product['QuotationProduct']['currency_id']==CURRENCY_USD){
					$totalTotalUSD+=$product['QuotationProduct']['product_total_price'];
					
					// dropped and sold calculated in US dollars
					$totalDropped+=round($product['QuotationProduct']['dropped']*$product['QuotationProduct']['product_total_price'],2);
					$totalSold+=round($product['QuotationProduct']['sold']*$product['QuotationProduct']['product_total_price'],2);
					
					//added calculation of totals in C$
					$totalTotalCS+=round($product['QuotationProduct']['product_total_price']*$product['QuotationProduct']['exchange_rate'],2);
					$currencyClass="class='USDcurrency'";
				}
				
				$bodyRows.="<tr>";
					$bodyRows.="<td>".$quotationDate->format('d-m-Y')."</td>";	
					$bodyRows.="<td>".$this->Html->Link($product['Quotation']['quotation_code'],array('controller'=>'quotations','action'=>'view',$product['Quotation']['id']),array('target'=>'_blank'))."</td>";	
					$bodyRows.="<td>".($product['Quotation']['bool_sales_order_present']?__("Yes"):__("No"))."</td>";
					$bodyRows.="<td>".$this->Html->Link($product['Quotation']['User']['username'],array('controller'=>'clients','action'=>'view',$product['Quotation']['User']['id']),array('target'=>'_blank'))."</td>";
					$bodyRows.="<td>".$this->Html->Link($product['Quotation']['Client']['name'],array('controller'=>'clients','action'=>'view',$product['Quotation']['Client']['id']),array('target'=>'_blank'))."</td>";
					$bodyRows.="<td>".$this->Html->Link($product['Product']['name'],array('controller'=>'products','action'=>'view',$product['Product']['id']),array('target'=>'_blank'))."</td>";
					$bodyRows.="<td>".$product['QuotationProduct']['product_quantity']."</td>";
					$bodyRows.="<td ".$currencyClass."><span class='amountright'>".$product['QuotationProduct']['product_total_price']."</span></td>";				
					$bodyRows.="<td class='percentage'><span class='amountright'>".$product['QuotationProduct']['dropped']."</span></td>";
					$bodyRows.="<td class='percentage'><span class='amountright'>".$product['QuotationProduct']['sold']."</span></td>";
				$bodyRows.="</tr>";
			}
			$totalRows="";
			//if ($totalTotalCS>0){
			if ($currencyId==CURRENCY_CS){
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total C$</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='CScurrency'><span class='amountright'>".$totalTotalCS."</span></td>";
					if (($totalDropped+$totalSold)>0){
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalDropped/($totalDropped+$totalSold),2)."</span></td>";
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalSold/($totalDropped+$totalSold),2)."</span></td>";
					}
					else {
						$totalRows.="<td class='percentage'><span class='amountright'>0</span></td>";
						$totalRows.="<td class='percentage'><span class='amountright'>0</span></td>";
					}
				$totalRows.="</tr>";
			}
			//if ($totalTotalUSD>0){
			if ($currencyId==CURRENCY_USD){
				$totalRows.="<tr class='totalrow'>";
					$totalRows.="<td>Total US$</td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td></td>";
					$totalRows.="<td class='USDcurrency'><span class='amountright'>".$totalTotalUSD."</span></td>";
					if (($totalDropped+$totalSold)>0){
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalDropped/($totalDropped+$totalSold),2)."</span></td>";
						$totalRows.="<td class='percentage'><span class='amountright'>".round(100*$totalSold/($totalDropped+$totalSold),2)."</span></td>";
					}
					else {
						$totalRows.="<td class='percentage'><span class='amountright'>0</span></td>";
						$totalRows.="<td class='percentage'><span class='amountright'>0</span></td>";
					}
				$totalRows.="</tr>";
			}
			$body="<tbody>".$totalRows.$bodyRows.$totalRows."</tbody>";
			$table_id=substr("categoria_".trim($productCategory['ProductCategory']['name']),0,30);
			echo "<table id='".$table_id."'>".$outputhead.$body."</table>";
			$excel.="<table id='".$table_id."'>".$excelhead.$body."</table>";
		}
	}
	$_SESSION['reporteCotizacionesPorCategoria'] = $excel;
?>
</div>
<script>
	$('body').on('change','#ReportProductCategoryId',function(){	
		var productcategoryid=$(this).children("option").filter(":selected").val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>products/getproductsforproductcategory/',
			data:{"productcategoryid":productcategoryid},
			cache: false,
			type: 'POST',
			success: function (products) {
				$('#ReportProductId').html(products);
			},
			error: function(e){
				console.log(e);
			}
		});
	});

	function formatNumbers(){
		$("td.number").each(function(){
			$(this).number(true,0);
		});
	}
	function formatPercentages(){
		$("td.percentage span.amountright").each(function(){
			$(this).number(true,0);
			$(this).append(" %");
		});
	}
	
	function formatCSCurrencies(){
		$("td.CScurrency span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
			$(this).parent().prepend("C$ ");
		});
	}
	
	function formatUSDCurrencies(){
		$("td.USDcurrency span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
			$(this).parent().prepend("US$ ");
		});
	}
	
	$(document).ready(function(){
		formatNumbers();
		formatPercentages();
		formatCSCurrencies();
		formatUSDCurrencies();
	});
	
</script>
