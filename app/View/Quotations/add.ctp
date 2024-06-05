<script>
	$('body').on('submit','#QuotationAddForm',function(e){	
		var i=0;
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
	
	$('body').on('change','#QuotationUserId',function(){	
		getNewQuotationCode();
	});
	$('body').on('change','#QuotationQuotationDateDay',function(){	
		getNewQuotationCode();
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#QuotationQuotationDateMonth',function(){	
		getNewQuotationCode();
		updateDueDate();
		updateExchangeRate();
	});
	$('body').on('change','#QuotationQuotationDateYear',function(){	
		getNewQuotationCode();
		updateDueDate();
		updateExchangeRate();
	});
	function getNewQuotationCode(){
		var userid=$('#QuotationUserId').val();
		var quotationdateday=$('#QuotationQuotationDateDay').val();
		var quotationdatemonth=$('#QuotationQuotationDateMonth').val();
		var quotationdateyear=$('#QuotationQuotationDateYear').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>quotations/getnewquotationcode/',
			data:{"userid":userid,"quotationdateday":quotationdateday,"quotationdatemonth":quotationdatemonth,"quotationdateyear":quotationdateyear},
			cache: false,
			type: 'POST',
			success: function (quotationcode) {
				$('#QuotationQuotationCode').val(quotationcode);
				//alert(quotationcode);
			},
			error: function(e){
				//$('#QuotationQuotationCode').val(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
	}
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
	
	$('body').on('keyup','#ClientName',function(){
		if (event.keyCode==8){
			$('#ClientName').val('');
		}
		//refreshClientList($(this).val());
	});
	$('body').on('change','#QuotationClientId',function(){
		var clientid=$('#QuotationClientId').val();
		if (clientid>0){
			getClientInfo();
			$('#saveClient').addClass('hidden');
			$('#ClientCreatingUserUsername').parent().removeClass('hidden');
			$('#ClientLastQuotationCode').parent().removeClass('hidden');
		}
		else {
			$('#ClientAddress').val('');
			$('#ClientPhone').val('');
			$('#ClientCell').val('');
			$('#ContactInfo').addClass('hidden');
			$('#ClientCreatingUserUsername').parent().addClass('hidden');
			$('#ClientLastQuotationCode').parent().addClass('hidden');
			$('#saveClient').removeClass('hidden');
		}
	});
	function getClientInfo() {    
		var clientid=$('#QuotationClientId').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>clients/getclientinfo/',
			data:{"clientid":clientid},
			dataType:'json',
			cache: false,
			type: 'POST',
			success: function (clientdata) {
				var clientname=clientdata.Client.name;
				var clientruc=clientdata.Client.ruc;
				var clientaddress=clientdata.Client.address;
				var clientphone=clientdata.Client.phone;
				var clientcell=clientdata.Client.cell;
				
				$('#ClientName').val(clientname);
				$('#ClientRuc').val(clientruc);
				$('#ClientAddress').val(clientaddress);
				$('#ClientPhone').val(clientphone);
				$('#ClientCell').val(clientcell);
				
				var creatinguser=clientdata.CreatingUser;
				if (creatinguser.id===null){
					$('#ClientCreatingUserUsername').val("No registrado");
				}
				else {
					$('#ClientCreatingUserUsername').val(clientdata.CreatingUser.username);
				}
				var lastquotation=clientdata.Quotation[0];
				if (lastquotation!==undefined){
					$('#ClientLastQuotationCode').val(clientdata.Quotation[0].quotation_code);
				}
				else {
					$('#ClientLastQuotationCode').val("-");
				}
				
			},
			error: function(e){
				//$('#ClientPhone').val(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
		getContactList();
	}
	
	function getContactList() {    
		$('#ContactInfo').removeClass('hidden');
		var clientid=$('#QuotationClientId').val();
		var contactfirstnameval=$('#ContactFirstName').val();
		var contactlastnameval=$('#ContactLastName').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>contacts/getcontactlistforcontactname/',
			data:{"clientid":clientid,"contactfirstnameval":contactfirstnameval,"contactlastnameval":contactlastnameval},
			cache: false,
			type: 'POST',
			success: function (options) {
				$('#QuotationContactId').html(options);
				if ($('#QuotationContactId').children('option').length==1){
					$('#ContactBoolNew').prop('checked',true);
				}
				/*
				if (options.length>0){
					$('#QuotationContactId').parent().removeClass('hidden');
					$('#ContactBoolNew').prop('checked',false);
					$('#QuotationContactId').html(options);
					
					if ($('#QuotationContactId').children('option').length==1){
						getContactInfo();
					}
					else {
						$('#ContactPhone').val('');
						$('#ContactCell').val('');
						$('#ContactEmail').val('');
						$('#ContactDepartment').val('');
					}
				}
				else {
					$('#QuotationContactId').parent().addClass('hidden');
					$('#ContactBoolNew').prop('checked',true);
				}
				*/
			},
			error: function(e){
				//$('#ContactPhone').val(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#QuotationContactId',function(){
		getContactInfo();
	});
	
	function getContactInfo() {    
		var contactid=$('#QuotationContactId').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>contacts/getcontactinfo/',
			data:{"contactid":contactid},
			dataType:'json',
			cache: false,
			type: 'POST',
			success: function (contactdata) {
				var contactfirstname=contactdata.first_name;
				var contactlastname=contactdata.last_name;
				var contactphone=contactdata.phone;
				var contactcell=contactdata.cell;
				var contactemail=contactdata.email;
				var contactdepartment=contactdata.department;
				$('#ContactFirstName').val(contactfirstname);
				$('#ContactLastName').val(contactlastname);
				$('#ContactPhone').val(contactphone);
				$('#ContactCell').val(contactcell);
				$('#ContactEmail').val(contactemail);
				$('#ContactDepartment').val(contactdepartment);
				
				$('#ContactBoolNew').prop('checked',false);
			},
			error: function(e){
				//$('#ContactPhone').val(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	function setNewClient(){
		$('#ClientBoolNew').prop('checked',true);
		$('#QuotationClientId').parent().addClass('hidden');
		$('#QuotationClientId').prop('required',false);
		$('#QuotationClientId').val('0');
		$('#ClientAddress').val('');
		$('#ClientPhone').val('');
		$('#ClientCell').val('');
		$('#ContactInfo').removeClass('hidden');
	}
	$('body').on('change','#ClientBoolNew',function(){
		if ($('#ClientBoolNew').is(':checked')){
			$('#ContactInfo').removeClass('hidden');
			$('#QuotationClientId').parent().addClass('hidden');
      $('#saveClient').removeClass('hidden');
		}
		else {
			$('#QuotationClientId').parent().removeClass('hidden');
      $('#saveClient').addClass('hidden');
		}
	});
	
	$('body').on('click','#saveClient',function(){
		var clientid=$('#QuotationClientId').val();
		var clientname=$('#ClientName').val();
		var boolnewclient=$('#ClientBoolNew').is(':checked');
		var clientruc=$('#ClientRuc').val();
		var clientaddress=$('#ClientAddress').val();
		var clientphone=$('#ClientPhone').val();
		var clientcell=$('#ClientCell').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>clients/saveclient/',
			data:{"clientid":clientid,"clientname":clientname,"boolnewclient":boolnewclient,"clientruc":clientruc,"clientaddress":clientaddress,"clientphone":clientphone,"clientcell":clientcell},
			cache: false,
			type: 'POST',
			success: function (data) {
				if (data=="1"){
					updateClientList();
				}
        else {
          // 20170821 DUPLICATE CLIENT PREVENTION WAS ONLY IN THE CLIENT MODEL, NOW EXPLICIT IN CLIENTS SAVECLIENT
          console.log(data);
          alert(data);
        }
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	});
	function updateClientList() {    
		var clientname=$('#ClientName').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>clients/getclientlist/',
			cache: false,
			type: 'POST',
			success: function (options) {
				$('#QuotationClientId').parent().removeClass('hidden');
				$('#ClientBoolNew').prop('checked',false);
				$('#QuotationClientId').html(options);
				$('#ClientName').val('');
				$('#ClientRuc').val('');
				$('#ClientAddress').val('');
				$('#ClientPhone').val('');
				$('#ClientCell').val('');
				if ($('#QuotationClientId').has('option:contains('+clientname+')').length){
					var optionvalue=$('#QuotationClientId option:contains('+clientname+')').val();
					$('#QuotationClientId').val(optionvalue);
					$('#QuotationClientId').trigger('change');
				}
			},
			error: function(e){
				//$('#ClientPhone').val(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	$('body').on('change','#ContactBoolNew',function(){
		if ($('#ContactBoolNew').is(':checked')){
			$('#QuotationContactId').parent().addClass('hidden');
		}
		else {
			$('#QuotationContactId').parent().removeClass('hidden');
		}
	});
	
	$('body').on('keyup','#ContactFirstName',function(){
		if (event.keyCode==8){
			$('#ContactFirstName').val('');
		}
		verifyBoolContactNew();
	});
	$('body').on('keyup','#ContactLastName',function(){
		if (event.keyCode==8){
			$('#ContactFirstName').val('');
		}
		verifyBoolContactNew();
	});
	
	function verifyBoolContactNew() {    
		$('#ContactInfo').removeClass('hidden');
		var clientid=$('#QuotationClientId').val();
		var contactfirstnameval=$('#ContactFirstName').val();
		var contactlastnameval=$('#ContactLastName').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>contacts/getcontactcount/',
			data:{"clientid":clientid,"contactfirstnameval":contactfirstnameval,"contactlastnameval":contactlastnameval},
			cache: false,
			type: 'POST',
			success: function (contactcount) {
				if (contactcount==0){
					$('#ContactBoolNew').prop('checked',true);
				}
			},
			error: function(e){
				//$('#ContactPhone').val(e.responseText);
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
	function setNewContact(){
		$('#ContactFirstName').val('');
		$('#ContactLastName').val('');
		$('#ContactBoolNew').prop('checked',true);
		$('#QuotationContactId').parent().addClass('hidden');
		$('#QuotationContactId').prop('required',false);
		$('#ContactPhone').val('');
		$('#ContactCell').val('');
		$('#ContactEmail').val('');
		$('#ContactDepartment').val('');
	}
	
	$('body').on('click','#saveContact',function(){
		var clientid=$('#QuotationClientId').val();
		var contactid=$('#QuotationContactId').val();
		var boolnewcontact=$('#ContactBoolNew').is(':checked');
		
		var contactfirstname=$('#ContactFirstName').val();
		var contactlastname=$('#ContactLastName').val();
		var contactemail=$('#ContactEmail').val();
		var contactphone=$('#ContactPhone').val();
		var contactcell=$('#ContactCell').val();
		var contactdepartment=$('#ContactDepartment').val();
		if (clientid>0){
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>contacts/savecontact/',
				data:{"clientid":clientid,"contactid":contactid,"boolnewcontact":boolnewcontact,"contactfirstname":contactfirstname,"contactlastname":contactlastname,"contactemail":contactemail,"contactphone":contactphone,"contactcell":contactcell,"contactdepartment":contactdepartment},
				cache: false,
				type: 'POST',
				success: function (contactid) {
					if (contactid>0){
						updateContactList(contactid);
					}
				},
				error: function(e){
					//$('#ContactPhone').val(e.responseText);
					console.log(e);
					alert(e.responseText);
				}
			});
		}
		else {
			alert ("Se debe especificar un cliente para el contacto, no se guardó el contacto!");
		}
	});
	function updateContactList(contactid) {    
		var clientid=$('#QuotationClientId').val();
		$.ajax({
			url: '<?php echo $this->Html->url('/'); ?>contacts/getcontactlist/',
			data:{"clientid":clientid},
			cache: false,
			type: 'POST',
			success: function (options) {
				$('#QuotationContactId').parent().removeClass('hidden');
				$('#ContactBoolNew').prop('checked',false);
				$('#QuotationContactId').html(options);
				$('#ContactFirstName').val('');
				$('#ContactLastName').val('');
				$('#ContactEmail').val('');
				$('#ContactPhone').val('');
				$('#ContactCell').val('');
				$('#ContactDepartment').val('');
				$('#QuotationContactId').val(contactid);
				$('#QuotationContactId').trigger('change');
			},
			error: function(e){
				console.log(e);
				alert(e.responseText);
			}
		});
	}
	
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
	
	$('body').on('click','#saveProduct',function(){
		var boolpictureok=true;
		var booldataok=true;
		//
		if ($('#DocumentUrlImage0').val().length>0){
			if($('#DocumentUrlImage0')[0].files[0].size > 5242880){
				boolpictureok=false;
				alert("La imagen excede 5MB!");        
				//e.preventDefault();    
			}
			else {
				var file_extension=get_extension($('#DocumentUrlImage0').val());
				var bool_valid_extension=check_validity_extension(file_extension);
				if (!bool_valid_extension){
					boolpictureok=false;
					alert("Solamente se permiten archivos jpg, jpeg, png y pdf!");        
					//e.preventDefault();    
				}
			}
		}
		if ($('#ProductDepartmentId').val()==0){
			booldataok=false;
			alert("Se debe seleccionar el departamento!");        
		}
		if (!$('#ProductName').val()){
			booldataok=false;
			alert("Se debe registrar el nombre del producto!");        
		}
		if (boolpictureok&&booldataok){				
			// AND NOW THE ACTUAL SAVING STARTS
			//var formData=$('#ProductAddForm').serialize();
			var formElement=$('#ProductAddForm');
			var formData=new FormData(formElement[0]);
			//alert(formData);
			$.ajax({
				url: '<?php echo $this->Html->url('/'); ?>products/saveproduct/',
				data: formData,
				type: 'POST',
				cache: false,
				contentType: false,//contentType should not be false for serialize to work
				processData: false,//processData should not be false for serialize to work
				success: function (result) {
					//alert(result);
					$('td.productid div select').each(function() {
						var selectedvalue=$(this).val();
						$(this).html(result);
						$(this).val(selectedvalue);
					});
				},
				error: function(e){
					alert(e.responseText); 
				}
			});
			$('#newProduct').modal('hide');
		}
	});
	
	$('body').on('hidden.bs.modal','#newProduct',function(){
		$('#ProductProductCategoryId').val('0');
		$('#ProductName').val('');
		$('#ProductDescription').val('');
		$('#ProductCode').val('');
		$('#ProductCurrencyId').val('<?php echo CURRENCY_USD; ?>');
		$('#ProductProductUnitPrice').val('0');
		$('#ProductProductUnitCost').val('0');
		
		$(this).find("input[type=checkbox], input[type=radio]")
		   .prop("checked", "")
		   .end();
	});
	
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
				//$(this).find('div input').trigger('change');
				//$(this).trigger('change');
				calculateRow($(this).closest('tr').attr('row'));
			});
		}
		else if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('td.productunitprice').each(function(){
				var originalprice= $(this).find('div input').val();
				var newprice=roundToTwo(originalprice*exchangerate);
				$(this).find('div input').val(newprice);
				//$(this).find('div input').trigger('change');
				//$(this).trigger('change');
				calculateRow($(this).closest('tr').attr('row'));
			});
		}
		calculateTotal();
	});
	
	$('body').on('click','.addItem',function(){
		var tableRow=$('#productosParaCotizacion tbody tr.hidden:first');
		tableRow.removeClass("hidden");
	});

	$('body').on('click','.removeItem',function(){
		var tableRow=$(this).closest('tr').remove();
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
					//currentrow.find('td.productimage').html(e.responseText);
					console.log(e);
					alert(e.responseText);
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
		$("td.amount span.amountright").each(function(){
			if (parseFloat($(this).text())<0){
				$(this).parent().prepend("-");
			}
			$(this).number(true,2);
		});
		var currencyid=$('#QuotationCurrencyId').children("option").filter(":selected").val();
		if (currencyid==<?php echo CURRENCY_CS; ?>){
			$('span.currency').text('C$ ');
		}
		else if (currencyid==<?php echo CURRENCY_USD; ?>){
			$('span.currency').text('US$ ');			
		}
	}
	
	$(document).ready(function(){
		formatCurrencies();

		$('a.productview').addClass('hidden');
		$('#ContactInfo').addClass('hidden');
		
		getNewQuotationCode();
		updateDueDate();
		updateExchangeRate();
		
		$('#QuotationClientId').trigger('change');
		$('#QuotationRemarkWorkingDaysBeforeReminder').trigger('change');
		
		$('#QuotationRemarkUserId').addClass('fixed');
		$('select.fixed option:not(:selected)').attr('disabled', true);
    
    $('#saveClient').addClass('hidden');
	});
</script>
<div class="quotations form fullwidth">
<?php 
	echo $this->Form->create('Quotation', array('enctype' => 'multipart/form-data')); 
	echo "<fieldset>";
		echo "<legend>".__('Add Quotation')."</legend>";
		echo "<div class='container-fluid'>";
			echo "<div class='rows'>";	
				echo "<div class='col-md-4'>";
					echo $this->Form->input('user_id',array('label'=>__('Ejecutivo de Ventas'),'default'=>$this->Session->read('User.id')));
					echo $this->Form->input('quotation_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('exchange_rate',array('default'=>$exchangeRateQuotation,'readonly'=>'readonly'));
					echo $this->Form->input('quotation_code',array('readonly'=>'readonly'));
				echo "</div>";
				echo "<div class='col-md-5'>";
					echo $this->Form->input('QuotationRemark.user_id',array('label'=>'Vendedor','value'=>$loggedUserId,'type'=>'hidden'));
					echo $this->Form->input('QuotationRemark.remark_text',array('rows'=>2,'required'=>'required','default'=>'Cotización creada'));
					echo $this->Form->input('QuotationRemark.working_days_before_reminder',array('default'=>5));
					echo $this->Form->input('QuotationRemark.reminder_date',array('dateFormat'=>'DMY'));
					echo $this->Form->input('QuotationRemark.action_type_id',array('default'=>ACTION_TYPE_OTHER));
				echo "</div>";
				echo "<div class='col-md-3'>";
					echo "<dl>";
						echo "<dt>Subtotal</dt>";
						echo "<dd id='subtotal'><span class='currency'></span><span class='amountright'>0</span></dd>";
						echo "<dt>IVA</dt>";
						echo "<dd id='iva'><span class='currency'></span><span class='amountright'>0</span></dd>";
						echo "<dt>Total</dt>";
						echo "<dd id='total'><span class='currency'></span><span class='amountright'>0</span></dd>";
					echo "</dl>";
				echo "</div>";
				
			echo "</div>";
			echo "<div class='rows'>";	
				echo "<div class='col-md-5'>";
					echo "<div class='boxed' id='ClientInfo'>";
						echo $this->Form->input('client_id',array('label'=>'Clientes existentes','default'=>'0','empty'=>array('0'=>'Seleccione Cliente Existente')));
						echo $this->Form->input('Client.name',array('label'=>'Nombre Cliente'));
						echo $this->Form->input('Client.bool_new',array(
							'type'=>'checkbox',
							'label'=>'Nuevo Cliente',
							'style'=>'width:80%;',
						));
						echo $this->Form->input('Client.ruc');
						echo $this->Form->input('Client.address');
						echo $this->Form->input('Client.phone');
						echo $this->Form->input('Client.cell');
						echo $this->Form->input('Client.creating_user_username',array('label'=>'Creado por','readonly'=>'readonly'));
						echo $this->Form->input('Client.last_quotation_code',array('label'=>'Ultima Cotización','readonly'=>'readonly'));
						echo $this->Form->input('contact_id',array('label'=>'Contactos existentes para Cliente','default'=>'0','required'=>false,'empty'=>array('0'=>'Seleccione Contacto Existente')));
						echo "<button id='saveClient' type='button'>".__('Guardar Cliente')."</button>";
					echo "</div>";
				echo "</div>";
				echo "<div class='col-md-5'>";
					echo "<div class='boxed' id='ContactInfo'>";
						echo $this->Form->input('Contact.first_name',array('label'=>'Nombre Contacto','required'=>false));
						echo $this->Form->input('Contact.last_name',array('label'=>'Apellido Contacto','required'=>false));
						echo $this->Form->input('Contact.bool_new',array(
							'type'=>'checkbox',
							'label'=>'Nuevo Contacto',
							'style'=>'width:80%;',
						));
						
						echo $this->Form->input('Contact.phone');
						echo $this->Form->input('Contact.cell');
						echo $this->Form->input('Contact.email');
						echo $this->Form->input('Contact.department',array('type'=>'text'));
						echo "<button id='saveContact' type='button'>".__('Guardar Contacto')."</button>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		
		echo $this->Form->input('bool_iva',array('label'=>'IVA','default'=>true));
		echo $this->Form->input('currency_id',array('default'=>CURRENCY_USD));
		
		echo $this->Form->input('due_date',array('dateFormat'=>'DMY'));
		echo $this->Form->input('delivery_time',array('label'=>'Tiempo de Entrega'));
		
		echo "<table id='images'>"; 
			echo "<thead>";
				echo "<tr>";
					echo "<th>".__('Image')."</th>";
					echo "<th>".__('Remover')."</th>";
					echo "<th>".__('Otro')."</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			
			for ($i=0;$i<=NUM_IMAGES;$i++){
				// 20170512 FOR SOME BIZAR REASON JQUERY DID NOT READ THE VALUE OF AND THEREFORE DID NOT VALIDATE THE FIRST FILE INPUT
				if ($i==0){
					echo "<tr row='".$i."' class='hidden'>";
				}
				elseif ($i==1){
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
					else{
						echo "<td ><button class='addImage' type='button'>".__('Añadir Otro Imagen')."</button></td>";
					}
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";	
		echo "<a href='#newProduct' role='button' class='btn btn-large btn-primary' data-toggle='modal'>Crear nuevo Producto</a>";
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
					echo "<td class='booliva'>".$this->Form->input('QuotationProduct.'.$j.'.bool_iva',array('label'=>false,'default'=>true))."</td>";
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
				echo "<td class='productquantity amount right'><span></span></td>";
				echo "<td></td>";
				echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_subtotal',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "<tr class='totalrow iva'>";
				echo "<td>IVA</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_iva',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "<tr class='totalrow total'>";
				echo "<td>Total</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td class='totalprice amount right'><span class='currency'></span>".$this->Form->input('price_total',array('label'=>false,'type'=>'decimal','readonly'=>'readonly','default'=>'0'))."</td>";
				echo "<td></td>";
			echo "</tr>";		
			echo "</tbody>";
		echo "</table>";
		echo $this->Form->submit(__('Guardar'),array('name'=>'save', 'id'=>'save')); 
		
		echo $this->Form->input('observations',array('rows'=>'2','label'=>'Observaciones para pdf'));
		
		echo $this->Form->input('bool_print_images',array('label'=>'Mostrar imagen en pdf?','checked'=>false));
		echo $this->Form->input('bool_print_delivery_time',array('label'=>'Mostrar tiempo de entrega en pdf?','checked'=>false));
		
		echo $this->Form->input('payment_form',array('label'=>'Forma de Pago','default'=>'50 % al ordenar y 50 % contra entrega'));
		echo $this->Form->input('remark_delivery',array('label'=>'Remarca sobre entrega','default'=>'Los días de entrega del producto es a partir del arte aprobado y con orden de compra'));
		echo $this->Form->input('remark_cheque',array('label'=>'Remarca sobre cheque','default'=>'El cheque será emitido a nombre de Más Publicidad'));
		echo $this->Form->input('remark_elaboration',array('label'=>'Remarca sobre elaboración','default'=>'Se procederá a la elaboración de su producto una vez recibida la autorización del arte y cotización'));
		echo $this->Form->input('text_client_signature',array('label'=>'Etiqueta firma cliente','default'=>'Firma Autorizada del Cliente'));
		echo $this->Form->input('text_authorization',array('label'=>'Etiqueta autorización','default'=>'Nombre y cargo de quien autoriza'));
		echo $this->Form->input('text_seal',array('label'=>'Etiqueta sello','default'=>'Sello de la institución'));
		echo $this->Form->input('authorization',array('label'=>'Persona quien autoriza'));
	echo "</fieldset>";
	//echo $this->Form->end(__('Submit')); 
	echo $this->Form->end(); 
	
	echo "<div id='newProduct' class='modal fade'>";
		echo "<div class='modal-dialog'>";
			echo "<div class='modal-content'>";
				echo $this->Form->create('Product', array('enctype' => 'multipart/form-data')); 
				echo "<div class='modal-header'>";
					//echo "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>";
					echo "<h4 class='modal-title'>Crear nuevo Producto</h4>";
				echo "</div>";
				echo "<div class='modal-body'>";
					echo "<fieldset>";
						echo "<legend>".__('Add Product')."</legend>";
						echo $this->Form->input('product_category_id',array('default'=>'0','empty'=>array('0'=>'Seleccione Categoría de Producto')));
						echo $this->Form->input('name');
						echo $this->Form->input('description');
						echo $this->Form->input('code');
						echo $this->Form->input('Document.url_image',array('label'=>'Cargar Imagen','type'=>'file','id'=>'DocumentUrlImage0'));
						echo $this->Form->input('currency_id',array('default'=>CURRENCY_USD));
						echo $this->Form->input('product_unit_price',array('default'=>'0'));
						echo $this->Form->input('product_unit_cost',array('default'=>'0'));
					
					echo "</fieldset>";
					echo "<div class='container-fluid'>";
						echo "<div class='row'>";
							echo "<div id='ProductList' class='col-md-8'>";
							
							echo "</div>";
							echo "<div id='ProviderList' class='col-md-4'>";
								echo "<h3>Proveedores Relacionados</h3>";
								for ($p=0;$p<count($providers);$p++){
									echo $this->Form->input('Provider.'.$p.'.provider_id',array('type'=>'checkbox','checked'=>false,'label'=>$providers[$p]['Provider']['name'],'div'=>array('class'=>'checkboxleft')));
								}
								echo "</div>";
							echo "</div>";
						echo "</div>";
					
				echo "</div>";
				echo "<div class='modal-footer'>";
					echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";
					echo "<button type='button' class='btn btn-primary' id='saveProduct'>".__('Submit')."</button>";
				echo "</div>";
				echo $this->Form->end(); 	
			echo "</div>";
		echo "</div>";
	echo "</div>";
?>
</div>
<!--div class='actions'>
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Quotations'), array('action' => 'index')); ?></li>
		<br/>
		<li><?php echo $this->Html->link(__('List Clients'), array('controller' => 'clients', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Client'), array('controller' => 'clients', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Invoices'), array('controller' => 'invoices', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Invoice'), array('controller' => 'invoices', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sales Orders'), array('controller' => 'sales_orders', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sales Order'), array('controller' => 'sales_orders', 'action' => 'add')); ?> </li>
	</ul>
</div-->