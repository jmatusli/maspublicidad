<div class="sales index fullwidth">
<?php	
	echo "<h2>".__('Reporte de Ventas por Cliente')."</h2>";
	
  $tableHeader="<thead>";
    $tableHeader.="<tr>";
      $tableHeader.="<th class='hidden'>Client ID</th>";
      $tableHeader.="<th>".__('Descripción del cliente')."</th>";
      $tableHeader.="<th class='centered'>".__('TOTAL')."</th>";
      $tableHeader.="<th>".__('Frecuencia')."</th>";
      foreach ($monthArray as $period){
        $tableHeader.="<th class='centered'>".$period['period']."</th>";
      }
      $tableHeader.="<th class='centered'>".__('TOTAL')."</th>";
      $tableHeader.="<th class='centered'>".__('%')."</th>";
    $tableHeader.="</tr>";
  $tableHeader.="</thead>";
    
  $vipClientTableRows=$objectiveClientTableRows=$normalClientTableRows="";  
  $vipClientTotal=$objectiveClientTotal=$normalClientTotal=0;
  
  $vipClientSalesPerPeriod=[];
  $objectiveClientSalesPerPeriod=[];
  $normalClientSalesPerPeriod=[];
	for ($i=0;$i<count($monthArray);$i++){
		$vipClientSalesPerPeriod[$i]=0;
    $objectiveClientSalesPerPeriod[$i]=0;
    $normalClientSalesPerPeriod[$i]=0;
	}
  
  foreach ($salesArray as $salesForClient){
    $clientId=$salesForClient['client_id'];
    if ($clientId==382){
      //pr($salesForClient);
    }
    //echo "client id is ".$clientId."<br/>";
    if ($salesForClient['totalCS']>0 && $clientId>0){
      //pr($salesForClient);
      if ($clientList[$clientId]['bool_vip']){
        $vipClientTotal+=($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS']);
        
        $vipClientTableRows.="<tr>";
          $vipClientTableRows.="<td class='hidden'>".$clientId."</td>";  
          $vipClientTableRows.="<td>".$this->Html->link($clientList[$clientId]['name'], array('controller' => 'clients', 'action' => 'view', $clientId))."</td>";
          $vipClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS'])."</span></td>";
          $vipClientTableRows.="<td class='centered number'>".$salesForClient['frequency']."</td>";
          foreach ($salesForClient['sales'] as $periodCounter=>$clientSales){
            $vipClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$clientSales['totalUSD']:$clientSales['totalCS'])."</span></td>";
            $vipClientSalesPerPeriod[$periodCounter]+=($currencyId==CURRENCY_USD?$clientSales['totalUSD']:$clientSales['totalCS']);
          }
          $vipClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS'])."</span></td>";
          $vipClientTableRows.="<td class='centered percentage'>".($salesArray[0]['totalCS']>0?100*$salesForClient['totalCS']/$salesArray[0]['totalCS']:0)."</td>";
        $vipClientTableRows.="</tr>";
      }
      else {
        
        //pr($salesForClient);
        $boolFrequencyNumber=($salesForClient['frequency'] >= $frequencyNumber ? 1:0);
        $boolCutoffReached=(($currencyId==CURRENCY_USD && $salesForClient['totalUSD']>=$cutoffAmount) || ($currencyId!=CURRENCY_USD && $salesForClient['totalCS']>=$cutoffAmount));
        $boolObjectiveClient=0;
        if ($boolOrConditionId==BOOL_OR){
          if ($boolFrequencyNumber || $boolCutoffReached){
            $boolObjectiveClient=1;  
          }
        }
        else {
          if ($boolFrequencyNumber && $boolCutoffReached){
            $boolObjectiveClient=1;  
          }
        }
        //echo "bool objective client is ".$boolObjectiveClient."<br>";
        
        if ($boolObjectiveClient){
          $objectiveClientTotal+=($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS']);
          
          $objectiveClientTableRows.="<tr>";
            $objectiveClientTableRows.="<td class='hidden'>".$clientId."</td>";  
            $objectiveClientTableRows.="<td>".$this->Html->link($clientList[$clientId]['name'], array('controller' => 'clients', 'action' => 'view', $clientId))."</td>";
            $objectiveClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS'])."</span></td>";
            $objectiveClientTableRows.="<td class='centered number'>".$salesForClient['frequency']."</td>";  
            foreach ($salesForClient['sales'] as $periodCounter=>$clientSales){
              $objectiveClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$clientSales['totalUSD']:$clientSales['totalCS'])."</span></td>";
              $objectiveClientSalesPerPeriod[$periodCounter]+=($currencyId==CURRENCY_USD?$clientSales['totalUSD']:$clientSales['totalCS']);
            }
            $objectiveClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS'])."</span></td>";
            $objectiveClientTableRows.="<td class='centered percentage'>".($salesArray[0]['totalCS']>0?100*$salesForClient['totalCS']/$salesArray[0]['totalCS']:0)."</td>";
          $objectiveClientTableRows.="</tr>";
        }
        else {
          $normalClientTotal+=($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS']);
      
          $normalClientTableRows.="<tr>";
            $normalClientTableRows.="<td class='hidden'>".$clientId."</td>";  
            $normalClientTableRows.="<td>".$this->Html->link($clientList[$clientId]['name'], array('controller' => 'clients', 'action' => 'view', $clientId))."</td>";
            $normalClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS'])."</span></td>";
            $normalClientTableRows.="<td class='centered number'>".$salesForClient['frequency']."</td>";  
            foreach ($salesForClient['sales'] as $periodCounter=>$clientSales){
              //echo "currency id is ".$currencyId."<br/>";
              //echo "usdcurrency id is ".CURRENCY_USD."<br/>";
              //echo "calculated class is  ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."<br/>";
              $normalClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$clientSales['totalUSD']:$clientSales['totalCS'])."</span></td>";
              $normalClientSalesPerPeriod[$periodCounter]+=($currencyId==CURRENCY_USD?$clientSales['totalUSD']:$clientSales['totalCS']);
            }
            $normalClientTableRows.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesForClient['totalUSD']:$salesForClient['totalCS'])."</span></td>";
            $normalClientTableRows.="<td class='centered percentage'>".($salesArray[0]['totalCS']>0?100*$salesForClient['totalCS']/$salesArray[0]['totalCS']:0)."</td>";
          $normalClientTableRows.="</tr>";
        }
        
      }
    }
  }
  $vipClientTotalRow=$objectiveClientTotalRow=$normalClientTotalRow="";
  $vipClientPercentage=$objectiveClientPercentage=$normalClientPercentage=0;
  
  if ($salesArray[0]['totalCS']>0){
    $vipClientPercentage=($currencyId==CURRENCY_USD?100*$vipClientTotal/$salesArray[0]['totalUSD']:100*$vipClientTotal/$salesArray[0]['totalCS']);
    $objectiveClientPercentage=($currencyId==CURRENCY_USD?100*$objectiveClientTotal/$salesArray[0]['totalUSD']:100*$objectiveClientTotal/$salesArray[0]['totalCS']);
    $normalClientPercentage=($currencyId==CURRENCY_USD?100*$normalClientTotal/$salesArray[0]['totalUSD']:100*$normalClientTotal/$salesArray[0]['totalCS']);
    
  }  
  $vipClientTotalRow.="<tr class='totalrow'>";
    $vipClientTotalRow.="<td class='hidden'></td>";
    $vipClientTotalRow.="<td>Total</td>";
    $vipClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$vipClientTotal."</span></td>";
    $vipClientTotalRow.="<td> </td>";
    for ($i=0;$i<count($vipClientSalesPerPeriod);$i++){
      $vipClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$vipClientSalesPerPeriod[$i]."</span></td>";
    }
    $vipClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$vipClientTotal."</span></td>";
    $vipClientTotalRow.="<td class='centered percentage'>".$vipClientPercentage."</td>";
  $vipClientTotalRow.="</tr>";
  $objectiveClientTotalRow.="<tr class='totalrow'>";
    $objectiveClientTotalRow.="<td class='hidden'></td>";
    $objectiveClientTotalRow.="<td>Total</td>";
    $objectiveClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$objectiveClientTotal."</span></td>";
    $objectiveClientTotalRow.="<td> </td>";
    for ($i=0;$i<count($objectiveClientSalesPerPeriod);$i++){
      $objectiveClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$objectiveClientSalesPerPeriod[$i]."</span></td>";
    }
    $objectiveClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$objectiveClientTotal."</span></td>";
    $objectiveClientTotalRow.="<td class='centered percentage'>".$objectiveClientPercentage."</td>";
  $objectiveClientTotalRow.="</tr>";
  $normalClientTotalRow.="<tr class='totalrow'>";
    $normalClientTotalRow.="<td class='hidden'></td>";
    $normalClientTotalRow.="<td>Total</td>";
    $normalClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$normalClientTotal."</span></td>";
    $normalClientTotalRow.="<td> </td>";
    for ($i=0;$i<count($normalClientSalesPerPeriod);$i++){
      //echo "currency id is ".$currencyId."<br/>";
      //echo "usdcurrency id is ".CURRENCY_USD."<br/>";
      //echo "calculated class is  ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."<br/>";
      
      $normalClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$normalClientSalesPerPeriod[$i]."</span></td>";
    }
    $normalClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$normalClientTotal."</span></td>";
    $normalClientTotalRow.="<td class='centered percentage'>".$normalClientPercentage."</td>";
  $normalClientTotalRow.="</tr>";
  
  $allClientTable="";
  $allClientTable.="<tr>";
    $allClientTable.="<td>Clientes VIP</td>";
    $allClientTable.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$vipClientTotal."</span></td>";
    $allClientTable.="<td class='centered percentage'>".$vipClientPercentage."</td>";
  $allClientTable.="</tr>";
  $allClientTable.="<tr>";
    $allClientTable.="<td>Clientes Objetivo</td>";
    $allClientTable.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$objectiveClientTotal."</span></td>";
    $allClientTable.="<td class='centered percentage'>".$objectiveClientPercentage."</td>";
  $allClientTable.="</tr>";
  $allClientTable.="<tr>";
    $allClientTable.="<td>Clientes Normales</td>";
    $allClientTable.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".$normalClientTotal."</span></td>";
    $allClientTable.="<td class='centered percentage'>".$normalClientPercentage."</td>";
  $allClientTable.="</tr>";
  
  $allClientTotalRow="";
  $allClientTotalRow.="<tr class='totalrow'>";
    $allClientTotalRow.="<td>Total</td>";
    $allClientTotalRow.="<td class='centered ".($currencyId==CURRENCY_USD?"USDcurrency":"CScurrency")."'><span class='currency'></span><span class='amountright'>".($currencyId==CURRENCY_USD?$salesArray[0]['totalUSD']:$salesArray[0]['totalCS'])."</span></td>";
    $allClientTotalRow.="<td class='centered percentage'>100</td>";
  $allClientTotalRow.="</tr>";
  
  $allClientTable="<table id='clientes_resumen".($userId>0?"_".$users[$userId]:"")."'>"."<tbody>".$allClientTotalRow.$allClientTable.$allClientTotalRow."</tbody></table>";
  $vipClientTable="<table id='clientes_VIP".($userId>0?"_".$users[$userId]:"")."'>".$tableHeader."<tbody>".$vipClientTotalRow.$vipClientTableRows.$vipClientTotalRow."</tbody></table>";
  $objectiveClientTable="<table id='clientes_objetivo".($userId>0?"_".$users[$userId]:"")."'>".$tableHeader."<tbody>".$objectiveClientTotalRow.$objectiveClientTableRows.$objectiveClientTotalRow."</tbody></table>";
  $normalClientTable="<table id='clientes_normales".($userId>0?"_".$users[$userId]:"")."'>".$tableHeader."<tbody>".$normalClientTotalRow.$normalClientTableRows.$normalClientTotalRow."</tbody></table>";
  
  
  echo $this->Form->create('Report'); 
		echo "<fieldset>";
      //echo "<legend>".__('Filtros')."</legend>";
      echo "<div class='container-fluid'>";
        echo "<div class='row'>";
          echo "<div class='col-md-3'>";     
            echo $this->Form->input('Report.startdate',['type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate,'minYear'=>2015,'maxYear'=>date('Y')]);
            echo $this->Form->input('Report.enddate',['type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate,'minYear'=>2015,'maxYear'=>date('Y')]);
            echo "<br/>";			
            echo $this->Form->input('Report.user_id',['label'=>__('Ejecutivo de Venta'),'default'=>$userId,'empty'=>['0'=>__('Seleccione Ejecutivo de Venta')]]);
            echo $this->Form->input('Report.currency_id',['label'=>__('Visualizar Totales'),'options'=>$currencies,'default'=>$currencyId]);
          echo "</div>";
          echo "<div class='col-md-6'>";     
            echo "<h3>Condiciones para clientes objetivo</h3>";
            echo "<p class='comment'>Se aplican dos condiciones para identificar clientes como clientes objetivo: monto total de venta y cantidad de meses que compraron</p>";
            echo $this->Form->input('Report.cutoff_amount',['label'=>__('Monto total vendido'),'default'=>$cutoffAmount]);
            echo $this->Form->input('Report.frequency_number',['label'=>__('Frecuencia (meses por año)'),'default'=>$frequencyNumber]);
            echo "<p class='comment'>Condiciones inclusivas significa que el cliente tiene que satisfacer todas condiciones a la vez</p>";
            echo "<p class='comment'>Condiciones exclusivas significa que el cliente tiene que satisfacer por lo menos una condición</p>";
            echo $this->Form->input('Report.bool_or_condition_id',['label'=>__('Procesar condiciones'),'default'=>$boolOrConditionId]);
          echo "</div>";
          echo "<div class='col-md-3'>";     
            echo $allClientTable;
          echo "</div>";
        echo "</div>";  
    echo "</fieldset>";
    echo "<button id='previousyear' class='yearswitcher'>Año Previo</button>";
    echo "<button id='nextyear' class='yearswitcher'>Año Siguiente</button>";
    echo "<br/>";
  echo $this->Form->Submit(__('Refresh'));  
	echo $this->Form->end(); 
	
	echo $this->Html->link(__('Guardar como Excel'), ['action' => 'guardarReporteVentasPorCliente'], ['class' => 'btn btn-primary']); 
	
  $startDateTime=new DateTime($startDate);
	$endDateTime=new DateTime($endDate);
  
  echo "<p class='comment'>Ventas están calculadas en base al subtotal sin IVA</p>";
  echo "<p class='comment'>Porcentajes son calculadas en base al precio de ventas total sobre todas categorías</p>";
  echo "<p class='comment'>Se muestran todos los clientes que han tenido ventas en el año</p>";
  if ($userId>0){
    echo "<p class='comment'>Se muestran todos los clientes para los cuales facturó el vendedor, también clientes no asociados</p>";
  }
  
  echo "<h2>Clientes VIP".($userId>0?" para vendedor ".$users[$userId]:"")."</h2>";
  echo $vipClientTable;
  echo "<h2>Clientes Objectivo".($userId>0?" para vendedor ".$users[$userId]:"")."</h2>";
  echo $objectiveClientTable;
  echo "<h2>Clientes Normales".($userId>0?" para vendedor ".$users[$userId]:"")."</h2>";
  echo $normalClientTable;
  
	$_SESSION['reporteVentasPorCliente'] = $allClientTable.$vipClientTable.$objectiveClientTable.$normalClientTable;
?>
</div>
<script>
	function formatNumbers(){
		$("td.number").each(function(){
			$(this).number(true,0);
		});
	}
	function formatPercentages(){
		$("td.percentage").each(function(){
			$(this).number(true,2);
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
