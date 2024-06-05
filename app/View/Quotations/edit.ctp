<script>
  $('body').on('submit','#QuotationEditForm',function(e){	
		var i=<?php echo count($this->request->data['QuotationImage']); ?>;
		//alert("the value of file "+i+" with identifier #DocumentUrlImage0 is "+$('#DocumentUrlImage0').val());
		while (i < <?php echo NUM_IMAGES; ?>){
			//alert("the value of file "+i+" with identifier #DocumentUrlImage"+i+" is "+$('#DocumentUrlImage'+i).val());
			if ($('#DocumentUrlImage'+i).val().length>0){
				if($('#DocumentUrlImage'+i)[0].files[0].size > 5242880){
					alert("La imagen excede 5MB!  No se puede guardar la cotización!");        
					e.preventDefault();    
				}
				else {
					var file_extension=get_extension($('#DocumentUrlImage'+i).val());
					//alert ("the file extension is "+file_extension);
					var bool_valid_extension=check_validity_extension(file_extension);
					if (!bool_valid_extension){
						alert("Solamente se permiten archivos jpg, jpeg, png y pdf!  No se puede guardar la cotización!");        
						e.preventDefault();    
					}
				}
			}
			i++;
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
  
	$('body').on('change','#QuotationQuotationDateDay',function(){
		// 20160713 updating of due date allowed
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#QuotationQuotationDateMonth',function(){
		// 20160713 updating of due date allowed
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#QuotationQuotationDateYear',function(){
		// 20160713 updating of due date allowed	
		updateDueDate();
		updateExchangeRate();
	});
	function updateDueDate(){
		var quotationdateday=$('#QuotationQuotationDateDay').val();
		var quotationdatemonth=$('#QuotationQuotationDateMonth').val();
		switch (quotationdatemonth){
			case "01":
				quotationdatemonth="0";
				break;
			case "02":
				quotationdatemonth="1";
				break;
			case "03":
				quotationdatemonth="2";
				break;
			case "04":
				quotationdatemonth="3";
				break;
			case "05":
				quotationdatemonth="4";
				break;
			case "06":
				quotationdatemonth="5";
				break;
			case "07":
				quotationdatemonth="6";
				break;
			case "08":
				quotationdatemonth="7";
				break;
			case "09":
				quotationdatemonth="8";
				break;
			case "10":
				quotationdatemonth="9";
				break;
			case "11":
				quotationdatemonth="10";
				break;
			case "12":
				quotationdatemonth="11";
				break;
		}
		var quotationdateyear=$('#QuotationQuotationDateYear').val();
		var d=new Date(quotationdateyear,quotationdatemonth,quotationdateday);
		var dueDate= new Date(d.getTime()+15*24*60*60*1000);		
		var duedatemonth=dueDate.getMonth();
		switch (dueDate.getMonth()){
			case 0:
				duedatemonth="01";
				break;
			case 1:
				duedatemonth="02";
				break;
			case 2:
				duedatemonth="03";
				break;
			case 3:
				duedatemonth="04";
				break;
			case 4:
				duedatemonth="05";
				break;
			case 5:
				duedatemonth="06";
				break;
			case 6:
				duedatemonth="07";
				break;
			case 7:
				duedatemonth="08";
				break;
			case 8:
				duedatemonth="09";
				break;
			case 9:
				duedatemonth="10";
				break;
			case 10:
				duedatemonth="11";
				break;
			case 11:
				duedatemonth="12";
				break;
		}
		$('#QuotationDueDateDay').val(('0'+dueDate.getDate()).slice(-2));
		//$('#QuotationDueDateMonth').val(('0'+(dueDate.getMonth()+1)).slice(-2));
		$('#QuotationDueDateMonth').val(duedatemonth);
		$('#QuotationDueDateYear').val(dueDate.getFullYear());
	}
	function updateExchangeRate(){
		var selectedday=$('#QuotationQuotationDateDay').children("option").filter(":selected").val();
		var selectedmonth=$('#QuotationQuotationDateMonth').children("option").filter(":selected").val();
		var selectedyear=$('#QuotationQuotationDateYear').children("option").filter(":selected").val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>exchange_rates/getexchangerate/',
			data:{"selectedday":selectedday,"selectedmonth":selectedmonth,"selectedyear":selectedyear},
			cache: false,
			type: 'POST',
			success: function (exchangerate) {
				$('#QuotationExchangeRate').val(exchangerate);
			},
			error: function(e){
				alert(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#QuotationRemarkWorkingDaysBeforeReminder',function(){
		var working_days_before_reminder=$(this).val();
		if (working_days_before_reminder<1||working_days_before_reminder>10){
			alert("El número de días laborales debe estar entre 1 y 10");
		}
		else {
			var reminderdatemoment = addWeekdays(moment(), working_days_before_reminder);
			var reminderdateyear=moment(reminderdatemoment).format('YYYY');
			var reminderdatemonth=moment(reminderdatemoment).format('MM');
			var reminderdateday=moment(reminderdatemoment).format('DD');
			
			$('#QuotationRemarkReminderDateDay').val(reminderdateday);
			$('#QuotationRemarkReminderDateMonth').val(reminderdatemonth);
			$('#QuotationRemarkReminderDateYear').val(reminderdateyear);
		}		
	});

	$('body').on('change','#QuotationDeliveryTime',function(){
		var deliverytime=$(this).val();
		$('td.productdeliverytime div input').each(function(){
			if ($(this).val()!=null){
				$(this).val(deliverytime);
			}
		});
	});
  
  $('body').on('click','.addImage',function(){
		var tableRow=$('#images tbody tr.hidden:eq(1)');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeImage',function(){
		var tableRow=$(this).closest('tr').remove();
		calculateTotal();
	});	
	
	
	$('body').on('change','#QuotationDueDateDay',function(){
		displayRejectedWarningIfNeeded();
	});
	$('body').on('change','#QuotationDueDateMonth',function(){
		displayRejectedWarningIfNeeded();
	});
	$('body').on('change','#QuotationDueDateYear',function(){
		displayRejectedWarningIfNeeded();
	});
	function displayRejectedWarningIfNeeded(){
		/*
		if (!$('#QuotationBoolRejected').is(':checked')){
			var dueDateDay=$('#QuotationDueDateDay').val();
			var dueDateMonth=$('#QuotationDueDateMonth').val();
			var dueDateYear=$('#QuotationDueDateYear').val();
			
			var currentDate=new Date();
			var dueDate = new Date(dueDateYear, dueDateMonth-1, dueDateDay);		
			if (currentDate>dueDate){
				$('#rejectedwarning').removeClass('hidden');
			}
			else {
				$('#rejectedwarning').addClass('hidden');
			}
		}
		*/
	}
	
	$('body').on('change','#QuotationBoolRejected',function(){
		if ($(this).val()==1){
			$('#QuotationRejectedReasonId').parent().removeClass('hidden');			
		}
		else {
			$('#QuotationRejectedReasonId').parent().addClass('hidden');			
		}
	});
	
  $('body').on('change','#QuotationClientId',function(){
    getContactList();  
  });
  function getContactList() {    
		$('#ContactInfo').removeClass('hidden');
    var clientid=$('#QuotationClientId').val();
    $.ajax({
			url: '<?php echo $this->Html->url('/'); ?>contacts/getcontactlist/',
			data:{"clientid":clientid},
			cache: false,
			type: 'POST',
			success: function (options) {
				$('#QuotationContactId').html(options);
			},
      error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
  }  
  
	$('body').on('change','#QuotationContactId',function(){
		loadContactInfo();
	});
	
	function loadContactInfo(){
		var contactid=$('#QuotationContactId').val();
		if (contactid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>contacts/getcontactinfo/',
				data:{"contactid":contactid},
				dataType:'json',
				cache: false,
				type: 'POST',
				success: function (contactdata) {
					/*
					var contactfirstname=contactdata.first_name;
					var contactlastname=contactdata.last_name;
					var contactphone=contactdata.phone;
					var contactcell=contactdata.cell;
					var contactemail=contactdata.email;
					var contactdepartment=contactdata.department;
					*/
					var contactinfohtml="";					
					contactinfohtml+="<dl>";
						contactinfohtml+="<dt>Nombre Contacto</dt>";
						contactinfohtml+="<dd>"+contactdata.first_name+" "+contactdata.last_name+"</dd>";
						contactinfohtml+="<dt>Teléfono</dt>";
						contactinfohtml+="<dd>"+contactdata.phone+"</dd>";
						contactinfohtml+="<dt>Celular</dt>";
						contactinfohtml+="<dd>"+contactdata.cell+"</dd>";
						contactinfohtml+="<dt>Correo</dt>";
						contactinfohtml+="<dd>"+contactdata.email+"</dd>";
						contactinfohtml+="<dt>Departamento</dt>";
						contactinfohtml+="<dd>"+contactdata.department+"</dd>";
					contactinfohtml+="</dl>";
					
					$('#contactInfo').html(contactinfohtml);
					
				},
				error: function(e){
					alert(e.responseText);
					console.log(e);
				}
			});
		}
		else {
			currentrow.find('td.productimage').empty();
		}
	}

	$('body').on('change','#QuotationCurrencyId',function(){
		var currencyid=$(this).val();
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text("US$");
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text("C$");
		}
		// now update all prices
		var exchangerate=parseFloat($('#QuotationExchangeRate').val());
		if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice/exchangerate);
				$(this).find('div input').val(newprice);
			});
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice*exchangerate);
				$(this).find('div input').val(newprice);
				
			});
		}
		$('td.productid div select').each(function(){
			if ($(this).val()>0){
				var rowid=$(this).closest('tr').attr('row');
				showProductImage(rowid);
				manageProductLink(rowid);		
			}
		});
		calculateTotal();
	});

	$('body').on('click','.addItem',function(){
		var tableRow=$('#productosParaCotizacion tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeItem',function(){
		var tableRow=$(this).parent().parent().remove();
		calculateTotal();
	});	
	
	$('body').on('change','.productid',function(){
		var rowid=$(this).closest('tr').attr('row');
		showProductImage(rowid);
		loadProductPriceAndIva(rowid);
		manageProductLink(rowid);
		var productname =$(this).find('div select option:selected').text();
		$(this).closest('tr').find('td.productdescription textarea').val(productname);
		calculateRow(rowid);
		calculateTotal();
	});	
	$('body').on('change','.productquantity',function(){
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		else {
			var roundedValue=Math.round($(this).find('div input').val());
			$(this).find('div input').val(roundedValue);
		}
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	$('body').on('change','.productunitprice',function(){
		if (!$(this).find('div input').val()||isNaN($(this).find('div input').val())){
			$(this).find('div input').val(0);
		}
		else {
			var roundedValue=roundToTwo($(this).find('div input').val());
			$(this).find('div input').val(roundedValue);
		}
		calculateRow($(this).closest('tr').attr('row'));
		calculateTotal();
	});	
	
	function showProductImage(rowid) {    
		var currentrow=$('#productosParaCotizacion').find("[row='" + rowid + "']");
		var productid=currentrow.find('td.productid div select').val();
		if (productid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>products/getproductimage/',
				data:{"productid":productid},
				cache: false,
				type: 'POST',
				success: function (productimage) {
					currentrow.find('td.productimage').html(productimage);
				},
				error: function(e){
					alert(e.responseText);
					console.log(e);
				}
			});
		}
		else {
			currentrow.find('td.productimage').empty();
		}
	}
	function loadProductPriceAndIva(rowid) {    
		var currentrow=$('#productosParaCotizacion').find("[row='" + rowid + "']");
		var productid=currentrow.find('td.productid div select').val();
		var currencyid=$('#QuotationCurrencyId').val();
		var dateday=$('#QuotationQuotationDateDay').val();
		var datemonth=$('#QuotationQuotationDateMonth').val();
		var dateyear=$('#QuotationQuotationDateYear').val();
		if (productid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>products/getproductinfo/',
				data:{"productid":productid,"currencyid":currencyid,"dateday":dateday,"datemonth":datemonth,"dateyear":dateyear},
				cache: false,
				dataType:'json',
				type: 'POST',
				success: function (product) {
					currentrow.find('td.productunitprice div input').val(product['Product']['calculated_unit_price']);
					if (product['Product']['bool_no_iva']){
						currentrow.find('td.boolnoiva div input').prop('checked',true);
						currentrow.find('td.booliva div input').prop('checked',false);
						currentrow.find('td.booliva div input[type=checkbox]').attr('onclick','return false');
					}
					else {
						currentrow.find('td.boolnoiva div input').prop('checked',$('#QuotationBoolIva').is(':checked'));
					}
				},
				error: function(e){
					alert(e.responseText);
					console.log(e);
				}
			});
		}
		else {
			currentrow.find('td.productimage').empty();
		}
	}
	function manageProductLink(rowid) {    
		var currentrow=$('#productosParaCotizacion').find("[row='" + rowid + "']");
		var productid=currentrow.find('td.productid div select').val();
		if (productid>0){
			currentrow.find('td.productactions a.productview').attr('href','<?php echo $this->Html->url('/'); ?>products/view/'+productid);
			currentrow.find('td.productactions a.productview').removeClass('hidden');
		}
		else {
			currentrow.find('td.productactions a.productview').attr('href','');
			currentrow.find('td.productactions a.productview').addClass('hidden');
		}
	}
	
	function calculateRow(rowid) {    
		var currentrow=$('#productosParaCotizacion').find("[row='" + rowid + "']");
		
		var quantity=parseFloat(currentrow.find('td.productquantity div input').val());
		var unitprice=parseFloat(currentrow.find('td.productunitprice div input').val());
		
		var totalprice=quantity*unitprice;
		
		currentrow.find('td.producttotalprice div input').val(roundToTwo(totalprice));
	}
	
	$('body').on('change','#QuotationBoolIva',function(){
		updateProductsForIva();
		calculateTotal();
	});
	
	function updateProductsForIva(){
		if ($('#QuotationBoolIva').is(':checked')){
			$('#productosParaCotizacion tr').each(function(){
				if (!$(this).find('td.boolnoiva div input[type=checkbox]').is(':checked')){
					$(this).find('td.booliva div input[type=checkbox]').prop('checked',true);
				}
			});
			
		}
		else {
			$('#productosParaCotizacion td.booliva div input[type=checkbox]').prop('checked',false);
		}
	}
	
	$('body').on('change','td.booliva div input',function(){
		calculateTotal();
	});
	
	function calculateTotal(){
		var totalProductQuantity=0;
		var subtotalPrice=0;
		var ivaPrice=0
		var totalPrice=0
		$("#productosParaCotizacion tbody tr:not(.hidden)").each(function() {
			var currentProductQuantity = $(this).find('td.productquantity div input');
			if (!isNaN(currentProductQuantity.val())){
				var currentQuantity = parseFloat(currentProductQuantity.val());
				totalProductQuantity += currentQuantity;
			}
			var currentProductPrice = $(this).find('td.producttotalprice div input');
			var currentPrice=0;
			if (!isNaN(currentProductPrice.val())){
				var currentPrice = parseFloat(currentProductPrice.val());
				subtotalPrice += currentPrice;
			}
			if ($(this).find('td.booliva div input').is(':checked')){
				$(this).find('td.iva div input').val(roundToTwo(0.15*currentPrice));
				ivaPrice+=roundToTwo(0.15*currentPrice);
			}
			else {
				$(this).find('td.iva div input').val(0);
			}
		});
		$('tr.totalrow.subtotal td.productquantity span').text(totalProductQuantity.toFixed(0));
		
		$('#subtotal span.amountright').text(subtotalPrice);
		$('tr.totalrow.subtotal td.totalprice div input').val(subtotalPrice.toFixed(2));
		
		$('#iva span.amountright').text(ivaPrice);
		$('tr.totalrow.iva td.totalprice div input').val(ivaPrice.toFixed(2));
		totalPrice=subtotalPrice + ivaPrice;
		$('#total span.amountright').text(totalPrice);
		$('tr.totalrow.total td.totalprice div input').val(totalPrice.toFixed(2));
		
		return false;
	}
	
	function formatCurrencies(){
		$("span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
		});
	}
	
	$(document).ready(function(){
		if ($('#QuotationBoolRejected').val()==1){
			$('#QuotationRejectedReasonId').parent().removeClass('hidden');			
		}
		else {
			$('#QuotationRejectedReasonId').parent().addClass('hidden');			
		}
	
		var currencyid=$('#QuotationCurrencyId').children("option").filter(":selected").val();
		
		if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text('C$ ');
		}
		else if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text('US$ ');			
		}
		$('a.productview').addClass('hidden');
	
		$('td.productid div select').each(function(){
			if ($(this).val()>0){
				var rowid=$(this).closest('tr').attr('row');
				showProductImage(rowid);
				manageProductLink(rowid);		
			}
		});
		calculateTotal();
		
		formatCurrencies();
		loadContactInfo();
		// 20160713 MODIFICATION BASED ON REQUEST MEETING 20160621
		/*
		// make due date fixed for sales executives even if they get edit permission
		<?php if (!$boolChangeDueDate){ ?>
			$('#QuotationDueDateDay').addClass('fixed');
			$('#QuotationDueDateMonth').addClass('fixed');
			$('#QuotationDueDateYear').addClass('fixed');
		<?php } ?>
		*/
		updateExchangeRate();
		$('#QuotationRemarkUserId').addClass('fixed');
		$('#QuotationRemarkWorkingDaysBeforeReminder').trigger('change');
		$('select.fixed option:not(:selected)').attr('disabled', true);
		
		displayRejectedWarningIfNeeded();
	});
</script>
<div class="quotations form fullwidth">
<?php 
	echo $this->Form->create('Quotation', ['enctype' => 'multipart/form-data']); 
	echo "<fieldset>"; 
		echo "<legend>".__('Edit Quotation')."</legend>";
	
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";	
				echo "<div class='col-md-4'>";
		
					echo $this->Form->input('id');
					echo $this->Form->input('user_id',array('label'=>__('Ejecutivo de Ventas')));
					echo $this->Form->input('quotation_date',array('dateFormat'=>'DMY'));
					//echo $this->Form->input('bool_rejected',array('label'=>'Caída'));
					//	echo $this->Form->input('bool_rejected',array('label'=>'Caída','format'=>array('before','label','between','input','after','error'),'after'=>'<span id=\'rejectedwarning\' class=\'redfatwarning hidden\'><= Se debe marcar la cotización como caída porque ya venció; para cálculos la cotización se considera como caída</span>','div'=>array('class'=>'checkboxwithwarning')));
					echo $this->Form->input('bool_rejected',array('label'=>'Caída','options'=>$rejectedOptions));
					echo $this->Form->input('rejected_reason_id',array('default'=>0,'empty'=>array('0'=>'Seleccione Razón de Caída')));
					echo $this->Form->input('exchange_rate',array('default'=>$exchangeRateQuotation,'readonly'=>'readonly'));
					echo $this->Form->input('quotation_code',array('readonly'=>'readonly'));
					echo $this->Form->input('client_id');
					echo $this->Form->input('contact_id',array('required'=>false,'empty'=>array('0'=>'Seleccione un contacto')));
					$dueDateArray=array();
					if ($boolChangeDueDate){
						$dueDateArray=array('dateFormat'=>'DMY');
						//$dueDateArray=array();
					}
					else {
						$dueDateArray=array('dateFormat'=>'DMY','class'=>'fixed');
						//$dueDateArray=array('class'=>'fixed');
						
					}
					echo $this->Form->input('bool_iva',array('label'=>'IVA'));
					echo $this->Form->input('currency_id');
					
					echo $this->Form->input('due_date',array('dateFormat'=>'DMY'));	
					echo $this->Form->input('delivery_time',array('label'=>'Tiempo de Entrega'));
				echo "</div>";
			echo "</div>";
			echo "<div class='rows'>";	
				echo "<div class='col-md-6'>";
					echo "<div>";
						echo "<dl>";
							echo "<dt>Subtotal</dt>";
							echo "<dd id='subtotal'><span class='currency'></span><span class='amountright'>0</span></dd>";
							echo "<dt>IVA</dt>";
							echo "<dd id='iva'><span class='currency'></span><span class='amountright'>0</span></dd>";
							echo "<dt>Total</dt>";
							echo "<dd id='total'><span class='currency'></span><span class='amountright'>0</span></dd>";
						echo "</dl>";
					echo "</div>";
					
					echo "<div id='contactInfo'>";
					echo "</div>";
				
					echo $this->Form->input('QuotationRemark.user_id',array('label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden'));
					echo $this->Form->input('QuotationRemark.remark_text',array('rows'=>'2'));
					echo $this->Form->input('QuotationRemark.working_days_before_reminder',array('default'=>5));
					echo $this->Form->input('QuotationRemark.reminder_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('QuotationRemark.action_type_id',array('default'=>ACTION_TYPE_OTHER));
					if (!empty($quotationRemarks)){
						echo "<table>";
							echo "<thead>";
								echo "<tr>";
									echo "<th>Fecha</th>";
									echo "<th>Vendedor</th>";
									echo "<th>Remarca</th>";
								echo "</tr>";
							echo "</thead>";
							echo "<tbody>";							
							foreach ($quotationRemarks as $quotationRemark){
								//pr($quotationRemark);
								$remarkDateTime=new DateTime($quotationRemark['QuotationRemark']['remark_datetime']);
								echo "<tr>";
									echo "<td>".$remarkDateTime->format('d-m-Y H:i')."</td>";
									echo "<td>".$quotationRemark['User']['username']."</td>";
									echo "<td>".$quotationRemark['QuotationRemark']['remark_text']."</td>";
								echo "</tr>";
							}
							echo "</tbody>";
						echo "</table>";
					}
				echo "</div>";
			echo "</div>";
		echo "</div>";
    
    echo "<p>Mantenga la casilla seleccionada para conservar la imagen.</p>";
    echo "<table id='images'>"; 
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Image')."</th>";
					echo "<th>".__('')."</th>";
					echo "<th>".__('')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			if (!empty($this->request->data['QuotationImage'])){      
        for ($qi=0;$qi<count($this->request->data['QuotationImage']);$qi++){
          $url=$this->request->data['QuotationImage'][$qi]['url_image'];
          echo "<tr row='".$qi."' style='font-size:1.1em;'>";
            echo "<td>".$this->Form->input('QuotationImage.'.$qi.'.bool_retain',['label'=>'Imagen existente','type'=>'checkbox','checked'=>'checked','div'=>['class'=>'input checkbox checkboxleftbig']])."</td>";
            echo "<td><a href='".($this->Html->url('/').$url)."' target='_blank'>".substr($url,strrpos($url,"/")+1)."</a></td>";
            
            echo "<td>".$this->Form->input('QuotationImage.'.$qi.'.id',['label'=>false,'type'=>'hidden','value'=>$this->request->data['QuotationImage'][$qi]['id']])."</td>";
          echo "</tr>";
        }
      }
      if (count($this->request->data['QuotationImage'])<NUM_IMAGES){
        for ($i=count($this->request->data['QuotationImage']);$i<=NUM_IMAGES;$i++){
          // 20170512 FOR SOME BIZAR REASON JQUERY DID NOT READ THE VALUE OF AND THEREFORE DID NOT VALIDATE THE FIRST FILE INPUT
          if ($i==0){
            echo "<tr row='".$i."' class='hidden'>";
          }
          elseif ($i==1 || $i==count($this->request->data['QuotationImage'])){
            echo "<tr row='".$i."'>";
          }
          else {
            echo "<tr row='".$i."' class='hidden'>";
          }
            echo "<td class='image'>".$this->Form->input('Document.url_image.'.$i,array('label'=>'Cargar Imagen','type'=>'file'))."</td>";
            echo "<td><button class='removeImage' type='button'>".__('Remover Imagen')."</button></td>";
            if ($i==NUM_IMAGES){
              echo "<td>&nbsp;</td>";
            }
            else {
              echo "<td ><button class='addImage' type='button'>".__('Añadir Otro Imagen')."</button></td>";
            }
          echo "</tr>";
        }
      }
			echo "</tbody>";
		echo "</table>";	
		/*
		echo "<div class='righttop'>";
			echo "<dl>";
				echo "<dt>Subtotal</dt>";
				echo "<dd id='subtotal'><span class='currency'></span><span class='amountright'>0</span></dd>";
				echo "<dt>IVA</dt>";
				echo "<dd id='iva'><span class='currency'></span><span class='amountright'>0</span></dd>";
				echo "<dt>Total</dt>";
				echo "<dd id='total'><span class='currency'></span><span class='amountright'>0</span></dd>";
			echo "</dl>";
		echo "</div>";
		*/
		
		echo "<table id='productosParaCotizacion'>"; 
			echo "<thead>";
				echo "<tr>";
					echo "<th style='width:20%'>".__('Product')."</th>";
					echo "<th style='width:8%'>".__('Image')."</th>";
					echo "<th style='width:20%'>".__('Observación')."</th>";
					echo "<th style='width:15%'>".__('Tiempo de entrega')."</th>";
					echo "<th style='width:8%'>".__('Quantity')."</th>";
					echo "<th style='width:8%'>".__('Unit Price')."</th>";
					echo "<th style='width:8%'>".__('Total Price')."</th>";
					echo "<th class='hidden'>".__('WithoutIVA')."</th>";
					echo "<th style='width:5%'>".__('IVA ?')."</th>";
					echo "<th>".__('Actions')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			//pr($this->request->data);
			$quotationProducts=$this->request->data['QuotationProduct'];
			$counter=0;
			if (count($requestProducts)>0){
				for ($i=0;$i<count($requestProducts);$i++) { 
					//pr($requestProducts[$i]['QuotationProduct']);
					echo "<tr row='".$i."'>";
						echo "<td class='productid'>".$this->Form->input('QuotationProduct.'.$i.'.product_id',array('label'=>false,'value'=>$requestProducts[$i]['QuotationProduct']['product_id'],'empty' =>array('0'=>__('Choose a Product'))))."</td>";
						echo "<td class='productimage'></td>";
						echo "<td class='productdescription'>".$this->Form->textarea('QuotationProduct.'.$i.'.product_description',array('label'=>false,'value'=>$requestProducts[$i]['QuotationProduct']['product_description'],'cols'=>1,'rows'=>10))."</td>";
						echo "<td class='productdeliverytime'>".$this->Form->input('QuotationProduct.'.$i.'.delivery_time',array('label'=>false,'value'=>$requestProducts[$i]['QuotationProduct']['delivery_time']))."</td>";
						echo "<td class='productquantity'>".$this->Form->input('QuotationProduct.'.$i.'.product_quantity',array('type'=>'numeric','label'=>false,'value'=>$requestProducts[$i]['QuotationProduct']['product_quantity'],'required'=>'required'))."</td>";
						echo "<td class='productunitprice amount'><span class='currency'></span>".$this->Form->input('QuotationProduct.'.$i.'.product_unit_price',array('type'=>'decimal','label'=>false,'value'=>$requestProducts[$i]['QuotationProduct']['product_unit_price']))."</td>";
						echo "<td class='producttotalprice amount'><span class='currency'></span>".$this->Form->input('QuotationProduct.'.$i.'.product_total_price',array('type'=>'decimal','label'=>false,'value'=>$requestProducts[$i]['QuotationProduct']['product_total_price'],'readonly'=>'readonly'))."</td>";
						echo "<td class='hidden boolnoiva'>".$this->Form->input('QuotationProduct.'.$i.'.bool_no_iva',array('label'=>false,'checked'=>$requestProducts[$i]['QuotationProduct']['bool_no_iva']))."</td>";
						echo "<td class='booliva'>".$this->Form->input('QuotationProduct.'.$i.'.bool_iva',array('label'=>false,'checked'=>$requestProducts[$i]['QuotationProduct']['bool_iva']))."</td>";
						echo "<td class='productactions'>";
							echo "<button class='removeItem' type='button'>".__('Quitar')."</button>";
							echo "<button class='addItem' type='button'>".__('Otro')."</button>";
							echo $this->Html->link(__('Ver'), array('controller' => 'products', 'action' => 'view',$requestProducts[$i]['QuotationProduct']['product_id']),array('class'=>'productview','target'=>'_blank'));
						echo "</td>";
					echo "</tr>";			
					$counter++;
				}
			}
			for ($j=$counter;$j<25;$j++) { 
				if ($j==$counter){
					echo "<tr row='".$j."'>";
				} 
				else {
					echo "<tr row='".$j."' class='hidden'>";
				} 
					echo "<td class='productid'>".$this->Form->input('QuotationProduct.'.$j.'.product_id',array('label'=>false,'default'=>'0','empty' =>array('0'=>__('Choose a Product'))))."</td>";
					echo "<td class='productimage'></td>";
					//echo "<td class='productdescription'>".$this->Form->input('QuotationProduct.'.$j.'.product_description',array('label'=>false))."</td>";
					echo "<td class='productdescription'>".$this->Form->textarea('QuotationProduct.'.$j.'.product_description',array('label'=>false,'cols'=>1,'rows'=>10))."</td>";
					echo "<td class='productdeliverytime'>".$this->Form->input('QuotationProduct.'.$j.'.delivery_time',array('label'=>false))."</td>";
					echo "<td class='productquantity'>".$this->Form->input('QuotationProduct.'.$j.'.product_quantity',array('type'=>'numeric','label'=>false,'default'=>'0','required'=>'required'))."</td>";
					echo "<td class='productunitprice amount'><span class='currency'></span>".$this->Form->input('QuotationProduct.'.$j.'.product_unit_price',array('type'=>'decimal','label'=>false,'default'=>'0'))."</td>";
					echo "<td class='producttotalprice amount'><span class='currency'></span>".$this->Form->input('QuotationProduct.'.$j.'.product_total_price',array('type'=>'decimal','label'=>false,'default'=>'0','readonly'=>'readonly'))."</td>";
					echo "<td class='hidden boolnoiva'>".$this->Form->input('QuotationProduct.'.$j.'.bool_no_iva',array('label'=>false,'default'=>0))."</td>";
					echo "<td class='booliva'>".$this->Form->input('QuotationProduct.'.$j.'.bool_iva',array('label'=>false,'default'=>$this->request->data['Quotation']['bool_iva']))."</td>";
					echo "<td class='productactions'>";
						echo "<button class='removeItem' type='button'>".__('Quitar')."</button>";
						echo "<button class='addItem' type='button'>".__('Otro')."</button>";
						echo $this->Html->link(__('Ver'), array('controller' => 'products', 'action' => 'view'),array('class'=>'productview','target'=>'_blank'));
					echo "</td>";
				echo "</tr>";			
			}
			echo "<tr class='totalrow subtotal'>";
				echo "<td>Subtotal</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				//echo "<td class='productquantity amount right'><span class='amountright'></span></td>";
				echo "<td class='productquantity amount right'><span></span></td>";
				echo "<td></td>";
				echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_subtotal',array('label'=>false,'type'=>'decimal','step'=>'0.01','readonly'=>'readonly','default'=>'0'))."</td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "<tr class='totalrow iva'>";
				echo "<td>IVA</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_iva',array('label'=>false,'type'=>'decimal','step'=>'0.01','readonly'=>'readonly','default'=>'0'))."</td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "<tr class='totalrow total'>";
				echo "<td>Total</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_total',array('label'=>false,'type'=>'decimal','step'=>'0.01','readonly'=>'readonly','default'=>'0'))."</td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "</tbody>";
		echo "</table>";
		echo $this->Form->submit(__('Guardar'),array('name'=>'save', 'id'=>'save')); 

		
		echo $this->Form->input('observations',array('rows'=>'2','label'=>'Observaciones para Pdf'));
		
		echo $this->Form->input('bool_print_images',array('label'=>'Mostrar imagen en pdf?'));
		echo $this->Form->input('bool_print_delivery_time',array('label'=>'Mostrar tiempo de entrega en pdf?'));
		
		echo $this->Form->input('payment_form',array('label'=>'Forma de Pago'));
		echo $this->Form->input('remark_delivery',array('label'=>'Remarca sobre entrega'));
		echo $this->Form->input('remark_cheque',array('label'=>'Remarca sobre cheque'));
		echo $this->Form->input('remark_elaboration',array('label'=>'Remarca sobre elaboración'));
		echo $this->Form->input('text_client_signature',array('label'=>'Etiqueta firma cliente'));
		echo $this->Form->input('text_authorization',array('label'=>'Etiqueta autorización'));
		echo $this->Form->input('text_seal',array('label'=>'Etiqueta sello'));
		echo $this->Form->input('authorization',array('label'=>'Persona quien autoriza'));
		
	echo "</fieldset>";

		
	echo $this->Form->end(); 
 ?>
</div>
<!--div class='actions'>
<?php 	
	/*
	echo "<h3>".__('Actions')."</h3>";
	echo "<ul>";
		echo "<!--li>".$this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Quotation.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Quotation.id')))."</li-->";
		echo "<li>".$this->Html->link(__('List Quotations'), array('action' => 'index'))."</li>";
		echo "<br/>";
		echo "<li>".$this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index'))." </li>";
		echo "<li>".$this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add'))." </li>";
		echo "<li>".$this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index'))." </li>";
		echo "<li>".$this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add'))." </li>";
		echo "<li>".$this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index'))." </li>";
		echo "<li>".$this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add'))." </li>";
	echo "</ul>";
	*/
?>
</div-->
