<script>
	$('body').on('change','.assignment',function(){
		$(this).closest('tr').find('.changed').val(1);
	});
</script>
<div class="clients asociarusariosdepartamentos fullwidth" style="overflow-x:auto">
<?php 
	echo $this->Form->create('DepartmentUser');
	echo "<fieldset>";
		echo $this->Form->input('role_id',['default'=>$selectedRoleId,'empty'=>[0=>'-- Papel --']]);
    echo $this->Form->input('user_id',['label'=>'Usuario','default'=>$selectedUserId,'empty'=>[0=>'-- Usuario --']]);
		echo $this->Form->input('department_id',['label'=>'Departamento','default'=>$selectedDepartmentId,'empty'=>[0=>'-- Departamento --']]);
		echo $this->Form->Submit(__('Actualizar'),['id'=>'refresh','name'=>'refresh']);
		echo "<legend>".__('Asociar Usuarios con Departamentos')."</legend>";
		echo "<br/>";
    echo $this->Html->link(__('Guardar como Excel'),['action' => 'guardarAsociacionesUsuariosDepartamentos'],['class' => 'btn btn-primary']); 
		echo "<p class='comment'>Cuando se cambia la asociación entre un usuario y un departamento, se guardarán las asociaciones de todos usuarios con este departamento</p>";
    
    $excelOutput='';
    
    $tableHead="";
    $tableHead.="<thead>";
      $tableHead.="<tr>";
        $tableHead.="<th>".__('Users')."</th>";
        foreach ($selectedDepartments as $departmentId=>$departmentValue){
          $tableHead.="<th>".$this->Html->link($departmentValue,['controller'=>'plants','action'=>'detalle',$departmentId])."</th>";
        }
      $tableHead.="</tr>";
    $tableHead.="</thead>";
    $excelHead="";
    $excelHead.="<thead>";
      $excelHead.="<tr>";
        $excelHead.="<th>".__('User')."</th>";
        foreach ($selectedDepartments as $departmentId=>$departmentValue){
          $excelHead.="<th>".$departmentValue."</th>";
        }
      $excelHead.="</tr>";
    $excelHead.="</thead>";
    
    
    foreach ($selectedRoles as $selectedRole){
      if (empty($selectedRole['User'])){
        echo '<h2>No hay usuarios (seleccionados) para papel '.$selectedRole['Role']['name'].'</h2>';
      }
      else {
        echo '<h2>Asignaciones para papel '.$selectedRole['Role']['name'].'</h2>';
      
        $tableBody="<tbody>";
        for ($u=0;$u<count($selectedRole['User']);$u++){
          //pr($selectedRole['User'][$u]);
          $tableBody.="<tr>";
            $tableBody.="<td>";
              $tableBody.=$this->Html->link($selectedRole['User'][$u]['username'],['controller'=>'users','action'=>'view',$selectedRole['User'][$u]['id']]);
              $tableBody.=$this->Form->input('User.'.$selectedRole['User'][$u]['id'].'.bool_changed',['type'=>'hidden','label'=>false,'value'=>0,'class'=>'changed']);
            $tableBody.="</td>";
            if (empty($selectedRole['User'][$u]['Department'])){
            foreach ($selectedDepartments as $departmentId=>$departmentValue){
                $tableBody.="<td>";
                  $tableBody.=$this->Form->input('User.'.$selectedRole['User'][$u]['id'].'.Department.'.$departmentId.'.bool_assigned',[
                    'type'=>'checkbox',
                    'label'=>false,
                    'checked'=>false,
                    'class'=>'assignment',
                  ]);
                $tableBody.="</td>";
              }
            }
            else {
              foreach ($selectedDepartments as $departmentId=>$departmentValue){
                $tableBody.="<td>";
                  $tableBody.=$this->Form->input('User.'.$selectedRole['User'][$u]['id'].'.Department.'.$departmentId.'.bool_assigned',[
                    'type'=>'checkbox',
                    'label'=>false,
                    'checked'=>$selectedRole['User'][$u]['Department'][$departmentId],
                    'class'=>'assignment',
                  ]);
                $tableBody.="</td>";
              }
            }
          $tableBody.="</tr>";			
        }
        $tableBody.="</tbody>";
        $excelBody="</tbody>";
        $excelBody="<tbody>";
        for ($u=0;$u<count($selectedRole['User']);$u++){
          //pr($selectedRole['User'][$u]);
          $excelBody.="<tr>";
            $excelBody.="<td>";
              $excelBody.=$this->Html->link($selectedRole['User'][$u]['username'],['controller'=>'users','action'=>'view',$selectedRole['User'][$u]['id']]);
              $excelBody.=$this->Form->input('User.'.$selectedRole['User'][$u]['id'].'.bool_changed',['type'=>'hidden','label'=>false,'value'=>0,'class'=>'changed']);
            $excelBody.="</td>";
            if (empty($selectedRole['User'][$u]['Department'])){
              foreach ($selectedDepartments as $departmentId=>$departmentValue){
                $excelBody.="<td>0</td>";
              }
            }
            else {
              foreach ($selectedDepartments as $departmentId=>$departmentValue){
                $excelBody.="<td>".($selectedRole['User'][$u]['Department'][$departmentId]?"1":"0")."</td>";
              }
            }
          $excelBody.="</tr>";			
        }
        $excelBody.="</tbody>";
        $table="<table>".$tableHead.$tableBody."</table>";
        echo $table;
        $excelOutput.="<table id='asoc_usuario_planta'>".$excelHead.$excelBody."</table>";
      }
    }  
    $_SESSION['resumenAsociacionesUsuariosDepartamentos'] = $excelOutput;
   
	echo "</fieldset>";
	echo $this->Form->Submit(__('Guardar'),['id'=>'submit','name'=>'submit']);	
  echo $this->Form->End();

?>
</div>
