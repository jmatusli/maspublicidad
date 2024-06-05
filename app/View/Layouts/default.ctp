<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) intersinaptico (www.intersinaptico.com)
 * @link          http://www.intersinaptico.com
 * @package       app.View.Layouts
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'Mas Publicidad');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		
		//echo $this->Html->meta('icon');
		
		echo $this->Html->css('bootstrap.min.css');
		echo $this->Html->css('cake.generic.css');
		echo $this->Html->css('maspublicidad.css');
		echo $this->Html->css('menu.css');
		echo $this->Html->css('modal.css');
		echo $this->Html->css('maspublicidad.print.css');
    if ($currentController == 'invoices' && $currentAction== 'imprimirVenta'){
      echo $this->Html->css('maspublicidad.invoice.css');  
    }

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		echo $this->Html->script('jquery-2.1.4.min');
		echo $this->Html->script('jquery-ui.min');
		echo $this->Html->script('combobox');
		echo $this->Html->script('date');
		echo $this->Html->script('moment.min');
		
		echo $this->Html->script('ckeditor/ckeditor');
		echo $this->Html->script('ckeditor/adapters/jquery');
	?>
</head>
<body id="ornasa">
	<div id="container">
		<div id="header">
			<div id="headerbar">
				<?php
					echo $this->Html->image("logo.gif", array("alt" => "Mas Publicidad",'url' => $userhomepage));
				?>
				<nav role="navigation">				
				<?php 
					//pr($active);
					echo $this->MenuBuilder->build('main-menu',$active); 
				?>	
				</nav>
			<?php	
				//echo "<span>".$username."</span>";
				if(!empty($modificationInfo)){
					if ($modificationInfo!=NA){
						//echo "<div class='useraction' style='position:absolute;right:350px;top:30px;'><div style='position:relative;'>".$modificationInfo."</div></div>";
						//echo "<div style='position:relative;'>".$modificationInfo."</div>";
						//echo $modificationInfo;
						echo "<div class='useractions' style='position:absolute;right:0px;top:0px;'>".$modificationInfo."</div>";
					}
				}
			?>
				<!--div class='exchangerate' style='position:absolute;right:150px;top:40px;'><span>Tasa de Cambio:<?php echo $currentExchangeRate; ?></span></div-->
			<?php 
				
				echo $this->Html->link(__('Logout'),'/users/logout', array('class' => 'btn btn-primary logout'));	
				echo "<a href='javascript:window.print();' class='btn btn-primary print'>Imprimir</a>";
				echo "<div class='dropdown'>";
					echo "<button class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown'>";
						echo "Mensajes";
						echo "<span class='caret'></span>";
					echo "</button>";
					echo "<ul class='dropdown-menu'>";
						echo "<li>".$this->Html->link('Mis Mensajes',array('controller'=>'messages','action'=>'index'))."</li>";
						echo "<li><a href='#newMessage' role='button' class='btn btn-large btn-primary' data-toggle='modal'>Crear Nuevo Mensaje</a></li>";
					echo "</ul>";
				echo "</div>";
				echo "<span class='username'>".$username."</span>";
				
				if (!empty($unreadMessages)){
					echo "<script language='javascript'>";
						echo "alert('Tienes nuevos mensajes, verifica tus mensajes');";
						//return $this->redirect(array('controller'=>'messages','action' => 'index'));
					echo "</script>";
				}
			?>
			</div>
		</div>
		<?php 
			//pr($sub);
			//pr($active);
			if ($sub!="NA"){
				echo '<div id="sub-menu">';
				echo $this->MenuBuilder->build($sub,$active); 
				echo '</div>';
			}
			
		echo "<div id='content'>";
		
		
			echo "<div id='newMessage' class='modal fade'>";
				echo "<div class='modal-dialog'>";
					echo "<div class='modal-content'>";
						echo $this->Form->create('Message', array('enctype' => 'multipart/form-data')); 
						echo "<div class='modal-header'>";
							echo "<h4 class='modal-title'>Crear Nuevo Mensaje</h4>";
						echo "</div>";
						echo "<div class='modal-body'>";
							
							echo "<fieldset>";
								echo "<legend>".__('Add Message')."</legend>";
								
								echo $this->Form->input('from_user_id',array('default'=>$userid,'type'=>'hidden'));
								
								for ($i=0;$i<count($recipientUsers)-1;$i++){
									if ($i==0){
										echo $this->Form->input('Message.recipient_user_id.'.$i,array('label'=>'Destinatario','options'=>$recipientUsers,'class'=>'recipient','default'=>'0','empty'=>array('0'=>'Seleccione Destinatario')));
									}
									else {
										echo $this->Form->input('Message.recipient_user_id.'.$i,array('label'=>'Destinatario','options'=>$recipientUsers,'class'=>'recipient','default'=>'0','empty'=>array('0'=>'Seleccione Destinatario'),'div'=>array('class'=>'hidden')));
									}
								}
								echo $this->Form->input('subject');
								echo $this->Form->input('body_text');
								echo $this->Form->input('Document.url_image',array('label'=>'Cargar Imagen','type'=>'file','id'=>'DocumentUrlImage0'));
								
							echo "</fieldset>";							
							
						echo "</div>";
						echo "<div class='modal-footer'>";
							echo "<button type='button' class='btn btn-default' data-dismiss='modal'>Cerrar</button>";
							echo "<button type='button' class='btn btn-primary' id='saveMessage'>".__('Enviar')."</button>";
						echo "</div>";
						echo $this->Form->end(); 	
					echo "</div>";
				echo "</div>";
			echo "</div>";
			?>
			
			<script>
				$('body').on('change','.recipient',function(){	
					if ($(this).val()){
						var nextrecipient= $(this).closest('fieldset').find('div.hidden:first');
						nextrecipient.removeClass('hidden');
					}
				});	
				
				$('body').on('click','#saveMessage',function(){
					var boolpictureok=true;
					var booldataok=true;
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
					if ($('#MessageRecipientUserId0').val()==0){
						booldataok=false;
						alert("Se debe seleccionar por lo menos un destinatario!");        
					}
					if (!$('#MessageSubject').val()){
						booldataok=false;
						alert("Se debe registrar el asunto del mensaje!");        
					}
					if (!$('#MessageBodyText').val()){
						booldataok=false;
						alert("Se debe registrar el texto del mensaje!");        
					}
					if (boolpictureok&&booldataok){				
						// AND NOW THE ACTUAL SAVING STARTS
						var formElement=$('#MessageIndexForm');
						var formData=new FormData(formElement[0]);
						//alert(formData);
						$.ajax({
							url: '<?php echo $this->Html->url('/'); ?>messages/savemessage/',
							data: formData,
							type: 'POST',
							cache: false,
							contentType: false,//contentType should not be false for serialize to work
							processData: false,//processData should not be false for serialize to work
							success: function (result) {
								//alert(result);
								alert("Mensaje Enviado!");
							},
							error: function(e){
								alert(e.responseText); 
							}
						});
						$('#newMessage').modal('hide');
					}
				});
				
				$('body').on('hidden.bs.modal','#newMessage',function(){
					$('#MessageSubject').val('');
					$('#MessageBodyText').val('');
					$('.recipient').val('0');
					
					$(this).find("input[type=checkbox], input[type=radio]")
					   .prop("checked", "")
					   .end();
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
			</script>
			
			<?php
			echo $this->Session->flash();
			echo $this->Session->flash('auth');
			echo $this->fetch('content'); 
		echo "</div>";
			$currentController= $this->params['controller'];
			$currentAction= $this->params['action'];
			if (!($currentController=="users"&&$currentAction=="login")){
				
		?>	
		<script>
			function roundToTwo(num) {    
				return +(Math.round(num + "e+2") + "e-2");
			}
			
			/*$('select[name*="[year]"] option:not(:selected)').attr('disabled', true);*/
			/*$('select.fixed option:not(:selected)').attr('disabled', true);*/
			
			$('body').on('change','input[type=text]',function(){	
				if (!$(this).hasClass('keepcase')){
          var uppercasetext=$(this).val().toUpperCase();
          $(this).val(uppercasetext)
        }
			});
      $('body').on('click','.pdflink',function(){
				var href=$(this).attr('href');
				var timestamp=$.now();
				href=href + '?timestamp='+timestamp;
				$(this).attr('href',href);
			});
			$('#previousmonth').click(function(event){
				var thisMonth = parseInt($('#ReportStartdateMonth').val());
				var previousMonth= (thisMonth-1)%12;
				var previousYear=parseInt($('#ReportStartdateYear').val());
				if (previousMonth==0){
					previousMonth=12;
					previousYear-=1;
				}
				if (previousMonth<10){
					previousMonth="0"+previousMonth;
				}
				$('#ReportStartdateDay').val('01');
				$('#ReportStartdateMonth').val(previousMonth);
				$('#ReportStartdateYear').val(previousYear);
				var daysInPreviousMonth=daysInMonth(previousMonth,previousYear);
				$('#ReportEnddateDay').val(daysInPreviousMonth);
				$('#ReportEnddateMonth').val(previousMonth);
				$('#ReportEnddateYear').val(previousYear);
			});
			
			$('#nextmonth').click(function(event){
				var thisMonth = parseInt($('#ReportStartdateMonth').val());
				var nextMonth= (thisMonth+1)%12;
				var nextYear=parseInt($('#ReportStartdateYear').val());
				if (nextMonth==0){
					nextMonth=12;
				}
				if (nextMonth==1){
					nextYear+=1;
				}
				if (nextMonth<10){
					nextMonth="0"+nextMonth;
				}
				$('#ReportStartdateDay').val('01');
				$('#ReportStartdateMonth').val(nextMonth);
				$('#ReportStartdateYear').val(nextYear);
				var daysInNextMonth=daysInMonth(nextMonth,nextYear);
				$('#ReportEnddateDay').val(daysInNextMonth);
				$('#ReportEnddateMonth').val(nextMonth);
				$('#ReportEnddateYear').val(nextYear);
			});
      
      $('#previousyear').click(function(event){
				var previousYear=parseInt($('#ReportStartdateYear').val())-1;
				$('#ReportStartdateDay').val('01');
				$('#ReportStartdateMonth').val('01');
				$('#ReportStartdateYear').val(previousYear);
				$('#ReportEnddateDay').val('31');
				$('#ReportEnddateMonth').val('12');
				$('#ReportEnddateYear').val(previousYear);
			});
			
			$('#nextyear').click(function(event){
				var nextYear=parseInt($('#ReportStartdateYear').val())+1;
				$('#ReportStartdateDay').val('01');
				$('#ReportStartdateMonth').val('01');
				$('#ReportStartdateYear').val(nextYear);
				$('#ReportEnddateDay').val('31');
				$('#ReportEnddateMonth').val('12');
				$('#ReportEnddateYear').val(nextYear);
			});
			
			function daysInMonth(month,year) {
				return new Date(year, month, 0).getDate();
			}
						
			$('body').on('keypress','#content',function(e){
				 var node = (e.target) ? e.target : ((e.srcElement) ? e.srcElement : null);
				if(e.which == 13 && node.type !="textarea") { // Checks for the enter key
				//if(e.which == 13) { // Checks for the enter key
					e.preventDefault(); // Stops IE from triggering the button to be clicked
				}
			});
			
			$('body').on('click','div.numeric input',function(){
				if (!$(this).attr('readonly')){
					if ($(this).val()=="0"){
						$(this).val("");
					}
				}
			});
			
			$('body').on('click','div.decimal input',function(){
				if (!$(this).attr('readonly')){
					if ($(this).val()=="0"){
						$(this).val("");
					}
				}
			});
			
			$('body').on('blur','div.numeric input',function(){
				if (!$(this).val()||isNaN($(this).val())){
					$(this).val(0);
				}
			});	
			$('body').on('blur','div.decimal input',function(){
				if (!$(this).val()||isNaN($(this).val())){
					$(this).val(0);
				}
			});	
			
			function confirmBackspaceNavigations () {
				// http://stackoverflow.com/a/22949859/2407309
				var backspaceIsPressed = false
				$(document).keydown(function(event){
					if (event.which == 8) {
						backspaceIsPressed = true
					}
				})
				$(document).keyup(function(event){
					if (event.which == 8) {
						backspaceIsPressed = false
					}
				})
				$(window).on('beforeunload', function(){
					if (backspaceIsPressed) {
						backspaceIsPressed = false
						return "Está seguro de ir a la pantalla anterior?"
					}
				})
			} // confirmBackspaceNavigations
			
			//http://stackoverflow.com/questions/20788411/how-to-exclude-weekends-between-two-dates-using-moment-js
			function addWeekdays(date, days) {
			  date = moment(date); // use a clone
			  while (days > 0) {
				date = date.add(1, 'days');
				// decrease "days" only if it's a weekday.
				if (date.isoWeekday() !== 6 && date.isoWeekday() !== 7) {
				  days -= 1;
				}
			  }
			  return date;
			}
			
			function getMonthFromDate(startdate){				
				var startdatemonth=startdate.getMonth();
				switch (startdatemonth){
					case 0:
						resultdatemonth="01";
						break;
					case 1:
						resultdatemonth="02";
						break;
					case 2:
						resultdatemonth="03";
						break;
					case 3:
						resultdatemonth="04";
						break;
					case 4:
						resultdatemonth="05";
						break;
					case 5:
						resultdatemonth="06";
						break;
					case 6:
						resultdatemonth="07";
						break;
					case 7:
						resultdatemonth="08";
						break;
					case 8:
						resultdatemonth="09";
						break;
					case 9:
						resultdatemonth="10";
						break;
					case 10:
						resultdatemonth="11";
						break;
					case 11:
						resultdatemonth="12";
						break;
				}
				return resultdatemonth;
			}
			
			$(document).ready(function(){
				confirmBackspaceNavigations ()
			});
		</script>
		<?php
			}
		?>
		<div id="footer">
			<?php 
				echo '<div id="copyright">Copyright 2015-'.date('Y').' @ Intersinaptico</div>';
				echo $this->Html->link(
					$this->Html->image('logo_intersinaptico_50.jpg', ['alt' => 'intersinaptico', 'border' => '0']),
					'http://www.intersinaptico.com/',
					['target' => '_blank', 'escape' => false, 'id' => 'intersinaptico']
				);
				echo "<div style='padding-left:300px;'>sesión hasta ".date('d-m-Y H:i:s',$this->Session->read('Config.time'))."</div>";
			?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
	<?php echo $this->Html->script('bootstrap.min'); ?>
	<?php echo $this->Html->script('jquery.number'); ?>
</body>
</html>
