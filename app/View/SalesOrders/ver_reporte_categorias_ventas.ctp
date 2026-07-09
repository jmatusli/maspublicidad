<div class="sales index" style='width:100%;'>

<?php
	echo "<h2>".__('Reporte orden de venta por categoria')."</h2>";
?>


<!-- Panel de filtros -->
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fas fa-filter"></i> Filtros del Reporte
        </h4>
    </div>
    <div class="panel-body">
        <!-- Modal para cambiar estado -->
        <div class="modal fade" id="modalCambiarEstado" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document" style="width: 900px !important; max-width: 95vw !important; margin: 30px auto;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Gestión de Estados</h4>
                    </div>
                    <div class="modal-body">
                        <form id="formCambiarEstado">
                            <input type="hidden" id="salesOrderId" name="sales_order_id">
                            
                            <!-- Tabs de navegación -->
                            <ul class="nav nav-tabs" id="estadoTabs" role="tablist">
                                <li class="active" role="presentation">
                                    <a href="#tabCambiarEstado" id="cambiarEstadoTab" role="tab" data-toggle="tab" aria-controls="tabCambiarEstado" aria-selected="true">
                                        <i class="fas fa-exchange-alt"></i> Cambiar Estado
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#tabCrearEstado" id="crearEstadoTab" role="tab" data-toggle="tab" aria-controls="tabCrearEstado" aria-selected="false">
                                        <i class="fas fa-plus-circle"></i> Crear Nuevo Estado
                                    </a>
                                </li>
                            </ul>
                            
                            <!-- Contenido de los tabs -->
                            <div class="tab-content" id="estadoTabContent" style="margin-top: 20px;">
                                <!-- Tab 1: Cambiar Estado -->
                                <div class="tab-pane fade in active" id="tabCambiarEstado" role="tabpanel" aria-labelledby="cambiarEstadoTab">
                                    <div class="row" style="display: flex; flex-wrap: nowrap; margin: 0;">
                                        <!-- Columna Izquierda: Información -->
                                        <div style="flex: 0 0 50%; padding: 0 15px 0 0;">
                                            <!-- Información de la Orden y Cliente -->
                                            <div class="panel panel-info" style="margin-bottom: 15px; height: 100%;">
                                                <div class="panel-heading">
                                                    <h5 class="panel-title" style="margin: 0;">
                                                        <i class="fas fa-info-circle"></i> Información de la Orden
                                                    </h5>
                                                </div>
                                                <div class="panel-body" style="padding: 15px;">
                                                    <div style="margin-bottom: 15px;">
                                                        <strong><i class="fas fa-file-alt"></i> Orden:</strong>
                                                        <div id="modalOrdenInfo" class="form-control-static" style="margin: 5px 0; padding: 8px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">-</div>
                                                    </div>
                                                    <div style="margin-bottom: 15px;">
                                                        <strong><i class="fas fa-sync-alt"></i> Estado Actual:</strong>
                                                        <div id="estadoActual" class="estado-actual" style="margin: 5px 0;">-</div>
                                                    </div>
                                                    <div>
                                                        <strong><i class="fas fa-user"></i> Cliente:</strong>
                                                        <div id="modalClienteInfo" class="form-control-static" style="margin: 5px 0; padding: 8px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">-</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Columna Derecha: Controles -->
                                        <div style="flex: 0 0 50%; padding: 0 0 0 15px;">
                                            <div style="display: flex; flex-direction: column; height: 100%;">
                                                <div style="margin-bottom: 20px;">
                                                    <div class="form-group">
                                                        <label for="nuevoEstado" style="font-weight: bold; display: block; margin-bottom: 5px; white-space: nowrap;">
                                                            <i class="fas fa-exchange-alt" style="margin-right: 8px;"></i>Seleccionar Nuevo Estado:
                                                        </label>
                                                        <select id="nuevoEstado" name="production_order_state_id" class="form-control" style="height: 45px;">
                                                            <option value="">Cargando estados...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div class="form-group" style="height: 100%;">
                                                        <label for="descripcion" style="font-weight: bold; display: block; margin-bottom: 5px; white-space: nowrap;">
                                                            <i class="fas fa-comment-alt" style="margin-right: 8px;"></i>Descripción del Cambio:
                                                        </label>
                                                        <textarea class="form-control" id="descripcion" name="descripcion" style="height: calc(100% - 30px); min-height: 120px; resize: vertical;" placeholder="Ingrese una descripción para este cambio de estado..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tab 2: Crear Nuevo Estado -->
                                <div class="tab-pane fade" id="tabCrearEstado" role="tabpanel" aria-labelledby="crearEstadoTab">
                                    <div style="display: flex; flex-wrap: nowrap; margin: 0;">
                                        <!-- Columna Izquierda: Input -->
                                        <div style="flex: 0 0 70%; padding: 0 15px 0 0;">
                                            <div class="form-group">
                                                <label for="nuevoEstadoNombre" style="font-weight: bold; font-size: 16px; display: block; margin-bottom: 8px; white-space: nowrap;">
                                                    <i class="fas fa-plus-circle" style="margin-right: 8px;"></i>Nombre del Nuevo Estado:
                                                </label>
                                                <input type="text" id="nuevoEstadoNombre" name="nuevo_estado" class="form-control" style="height: 50px; font-size: 16px;" placeholder="Ingrese el nombre del nuevo estado">
                                            </div>
                                        </div>
                                        <!-- Columna Derecha: Nota -->
                                        <div style="flex: 0 0 30%; padding: 0 0 0 15px;">
                                            <div class="alert alert-info" style="margin-top: 25px; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                                                <i class="fas fa-info-circle"></i> 
                                                <strong>Nota:</strong><br>
                                                El nuevo estado quedará disponible para futuros cambios de estado de otras órdenes.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="guardarEstado()">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
	echo $this->Form->create('Report', array('id' => 'filterForm')); 
		echo "<fieldset>";
		echo $this->Form->input('Report.product_category_id',array('label'=>__('Departamento'),'options'=>$productCategories,'default'=>$product_category_id,'empty'=>array('0'=>__('Seleccione Departamento'))));
		echo "<br/>";		
		echo $this->Form->input('Report.client_id',array('label'=>'Cliente','default'=>'0','empty'=>array('0'=>'Seleccione Cliente')));
		echo "<br/>";
		echo $this->Form->input('Report.estado_id',array('label'=>'Estado','options'=>array(),'default'=>'0','empty'=>array('0'=>'Seleccione Estado'),'id'=>'filtroEstado'));
		echo "</fieldset>";
		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";
		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";
		echo "<br/>";
		echo $this->Form->end(__('Refresh'), array('id' => 'refreshButton')); 
		echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteCategoriasVentas'), array( 'class' => 'btn btn-primary')); 
?>


<?php

// Configuración de columnas para el componente de paginación
$columns = array(
    array(
        'title' => __('Fecha'),
        'field' => 'SalesOrder.sales_order_date',
        'width' => '10%'
    ),
    array(
        'title' => __('Código Orden'),
        'field' => 'SalesOrder.sales_order_code',
        'width' => '15%'
    ),
    array(
        'title' => __('Cliente'),
        'field' => 'Client.name',
        'width' => '20%'
    ),
    array(
        'title' => __('Categoría'),
        'field' => 'ProductCategory.name_category',
        'width' => '20%'
    ),
    array(
        'title' => __('Descripción'),
        'field' => 'SalesOrderProduct.0.sale_description',
        'width' => '20%'
    ),
    array(
        'title' => __('Cantidad'),
        'field' => 'SalesOrderProduct.0.product_quantity',
        'width' => '10%'
    ),
    array(
        'title' => __('Acciones'),
        'field' => 'Acciones',
        'width' => '5%'
    )
);

// URL para el componente de paginación
$dataSourceUrl = $this->Html->url('/sales_orders/getReporteCategoriasVentasData');

$this->set(compact('columns', 'dataSourceUrl'));
?>

<!-- Contenedor de la tabla -->
<div id="table-container-reporteCategoriasTable" class="pagination-component-container">
    <table id="reporteCategoriasTable" class="table table-striped table-hover">
        <thead>
            <tr>
                <?php foreach ($columns as $column): ?>
                    <th style="width: <?php echo $column['width']; ?>;">
                        <?php echo $column['title']; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se cargarán aquí vía AJAX -->
        </tbody>
    </table>
</div>

<style>
.pagination-component-container {
    margin: 20px;
}

.pagination-component-container table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.pagination-component-container th,
.pagination-component-container td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}

.pagination-component-container th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.pagination-component-container tbody tr:hover {
    background-color: #f5f5f5;
}

.pagination-component-container .pagination-info {
    margin: 10px 0;
    text-align: center;
}

.pagination-component-container .pagination-controls {
    margin: 10px 0;
    text-align: center;
}

.pagination-component-container .pagination-controls button {
    margin: 0 5px;
}

.pagination-component-container .pagination-controls button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.pagination-component-container .pagination-controls button {
    padding: 5px 10px;
    margin: 0 2px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.pagination-component-container .pagination-controls button:hover:not(:disabled) {
    background-color: #f0f0f0;
    border-color: #ccc;
}

.pagination-component-container .pagination-controls button.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination-component-container .pagination-controls button.active:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.pagination-component-container .pagination-controls span {
    margin: 0 5px;
    color: #666;
}

.pagination-component-container .pagination-controls #goToPage {
    padding: 5px;
    margin: 0 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
    text-align: center;
}

#reporteCategoriasTable {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 12px;
}

#reporteCategoriasTable th,
#reporteCategoriasTable td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}

#reporteCategoriasTable th {
    background-color: #f8f9fa;
    font-weight: bold;
}

#reporteCategoriasTable tbody tr:hover {
    background-color: #f5f5f5;
}

#reporteCategoriasTable td:nth-child(1),
#reporteCategoriasTable th:nth-child(1) {
    width: 10% !important;
    min-width: 10% !important;
    max-width: 10% !important;
}

#reporteCategoriasTable td:nth-child(2),
#reporteCategoriasTable th:nth-child(2) {
    width: 15% !important;
    min-width: 15% !important;
    max-width: 15% !important;
}

#reporteCategoriasTable td:nth-child(3),
#reporteCategoriasTable th:nth-child(3) {
    width: 20% !important;
    min-width: 20% !important;
    max-width: 20% !important;
}

#reporteCategoriasTable td:nth-child(4),
#reporteCategoriasTable th:nth-child(4) {
    width: 20% !important;
    min-width: 20% !important;
    max-width: 20% !important;
}

#reporteCategoriasTable td:nth-child(5),
#reporteCategoriasTable th:nth-child(5) {
    width: 20% !important;
    min-width: 20% !important;
    max-width: 20% !important;
}

#reporteCategoriasTable td:nth-child(6),
#reporteCategoriasTable th:nth-child(6) {
    width: 10% !important;
    min-width: 10% !important;
    max-width: 10% !important;
}

#reporteCategoriasTable td:nth-child(7),
#reporteCategoriasTable th:nth-child(7) {
    width: 5% !important;
    min-width: 5% !important;
    max-width: 5% !important;
}

.table tbody tr:hover {
    background-color: #e9ecef;
}

/* Estilos para bloques de órdenes */
.table tbody tr.order-product {
    background-color: #ffffff !important;
}

.table tbody tr.order-product:hover {
    background-color: #f8f9fa !important;
}

.table tbody tr.order-separator {
    background-color: rgba(108, 117, 125, 0.3) !important;
    border-top: 4px solid rgba(73, 80, 87, 0.5) !important;
    border-bottom: 4px solid rgba(73, 80, 87, 0.5) !important;
    height: 15px !important;
    min-height: 15px !important;
    line-height: 15px !important;
}

.table tbody tr.order-separator td {
    background-color: rgba(108, 117, 125, 0.3) !important;
    padding: 15px !important;
    font-size: 0 !important;
    line-height: 0 !important;
    height: 15px !important;
    min-height: 15px !important;
    border-top: 4px solid rgba(73, 80, 87, 0.5) !important;
    border-bottom: 4px solid rgba(73, 80, 87, 0.5) !important;
}

/* Forzar color del separador */
.table tbody tr.order-separator,
.table tbody tr.order-separator td,
.table tbody tr.order-separator td:hover {
    background-color: rgba(108, 117, 125, 0.3) !important;
    background-image: none !important;
}

#table-container-reporteCategoriasTable {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    margin: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
}


.monthswitcher {
    margin: 0 10px;
    padding: 5px 10px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
}

.monthswitcher:hover {
    background: #e0e0e0;
}

/* Overlay de carga */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
}

.loading-content {
    text-align: center;
    color: #fff;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px auto;
}

.loading-text {
    font-size: 18px;
    font-weight: 500;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Spinner para botones */
.button-spinner {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 2px solid #fff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

/* Estilo para botón durante carga */
input[type="submit"].loading {
    position: relative;
    padding-left: 25px !important;
    opacity: 0.8;
    cursor: wait;
}

input[type="submit"].loading::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 12px;
    border: 2px solid #fff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

#refreshButton:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.number.centered {
    text-align: center;
}

/* Estilos específicos para el modal de gestión de estados */
#modalCambiarEstado .modal-dialog {
    width: 900px !important;
    max-width: 95vw !important;
    margin: 30px auto;
}

#modalCambiarEstado .modal-content {
    min-height: 400px;
}

#modalCambiarEstado .modal-body {
    padding: 20px;
    min-height: 350px;
}

#modalCambiarEstado .panel {
    margin-bottom: 15px;
}

#modalCambiarEstado .panel-body {
    padding: 15px;
}

#modalCambiarEstado .form-group {
    margin-bottom: 15px;
}

#modalCambiarEstado .form-control-static {
    padding: 8px 12px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    min-height: 36px;
    display: block;
    vertical-align: top;
}

/* Asegurar que el flexbox funcione correctamente */
#modalCambiarEstado .tab-pane {
    min-height: 300px;
}

#modalCambiarEstado .panel {
    height: 100%;
    display: flex;
    flex-direction: column;
}

#modalCambiarEstado .panel-body {
    flex: 1;
}

/* Estilo para el estado actual */
.estado-actual {
    font-weight: bold;
    color: #0056b3;
    background-color: #e7f3ff;
    padding: 8px 12px;
    border-radius: 4px;
    border-left: 4px solid #0056b3;
}

/* Mejorar el textarea */
#modalCambiarEstado textarea.form-control {
    min-height: 80px;
    resize: vertical;
}

/* Input más grande para crear estado */
#modalCambiarEstado .input-lg {
    font-size: 16px;
    height: 46px;
}
</style>

<script>
function abrirModalEstado(rowData) {
    console.log('abrirModalEstado llamado con rowData:', rowData);
    // Limpiar formulario antes de cargar los datos de la orden; reset() tambien limpia inputs hidden.
    $('#formCambiarEstado')[0].reset();

    // Extraer datos de la orden
    var salesOrder = rowData.SalesOrder || {};
    var firstProduct = (rowData.SalesOrderProduct && rowData.SalesOrderProduct[0]) ? rowData.SalesOrderProduct[0] : {};
    var salesOrderId = salesOrder.id || salesOrder.sales_order_id || firstProduct.sales_order_id || rowData.sales_order_id || '';
    var salesOrderCode = salesOrder.sales_order_code || 'Orden sin codigo';
    var salesOrderDate = salesOrder.sales_order_date || '';
    var clientName = rowData.Client ? rowData.Client.name : 'Cliente no disponible';
    var clientEmail = rowData.Client ? (rowData.Client.email || 'Sin email') : 'Sin email';
    var currentState = 'Sin estado asignado';
    if (rowData.SalesOrderProduct && rowData.SalesOrderProduct[0]) {
        console.log('SalesOrderProduct[0]:', firstProduct);
        // Usar el nombre del estado desde los datos del endpoint
        if (firstProduct.SalesOrderProductStatus && firstProduct.SalesOrderProductStatus.status) {
            currentState = firstProduct.SalesOrderProductStatus.status;
            console.log('Estado actual desde SalesOrderProductStatus.status:', currentState);
        } else {
            var statusId = firstProduct.sales_order_product_status_id;
            console.log('Estado actual desde sales_order_product_status_id:', statusId);
            if (statusId) {
                currentState = 'Estado ID: ' + statusId;
            }
        }
    }
    console.log('Estado actual a mostrar:', currentState);
    
    // Establecer ID de la orden despues del reset para que no quede vacio al guardar.
    $('#salesOrderId').val(salesOrderId);
    
    // Mostrar información de la orden inmediatamente
    $('#modalOrdenInfo').html(
        '<strong>' + salesOrderCode + '</strong><br>' +
        '<small>Fecha: ' + (salesOrderDate ? new Date(salesOrderDate).toLocaleDateString('es-ES') : '-') + '</small>'
    );
    
    // Mostrar información del cliente inmediatamente
    $('#modalClienteInfo').html(
        '<strong>' + clientName + '</strong><br>&nbsp;&nbsp;' +
        '<small>' + clientEmail + '</small>'
    );
    
    // Mostrar estado actual inmediatamente
    $('#estadoActual').text(currentState);
    
    // Resetear al primer tab
    $('#cambiarEstadoTab').tab('show');
    
    // Cargar estados disponibles
    cargarEstados();
    
    // Abrir modal
    $('#modalCambiarEstado').modal('show');
}

// Limpiar formulario al cerrar modal
$('#modalCambiarEstado').on('hidden.bs.modal', function () {
    $('#formCambiarEstado')[0].reset();
    $('#salesOrderId').val('');
    // Resetear al primer tab
    $('#cambiarEstadoTab').tab('show');
});

function cargarEstados() {
    // Mostrar estado de carga en ambos selects
    var selectModal = $('#nuevoEstado');
    var selectFiltro = $('#filtroEstado');
    
    selectModal.empty();
    selectModal.append('<option value="">Cargando estados...</option>');
    
    selectFiltro.empty();
    selectFiltro.append('<option value="">Cargando estados...</option>');
    
    $.ajax({
        url: "<?php echo $this->Html->url('/sales_order_product_statuses/getEstados'); ?>",
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Llenar select del modal
            selectModal.empty();
            selectModal.append('<option value="">Seleccione un estado...</option>');
            
            // Llenar select de filtros
            selectFiltro.empty();
            selectFiltro.append('<option value="0">Seleccione Estado</option>');
            
            if (response.success && response.estados && response.estados.length > 0) {
                var estadosFiltro = response.estados_filtro ? response.estados_filtro : response.estados;

                $.each(response.estados, function(index, estado) {
                    // Agregar al select del modal
                    selectModal.append('<option value="' + estado.id + '">' + estado.status + '</option>');
                });

                $.each(estadosFiltro, function(index, estado) {
                    // Agregar al select de filtros
                    selectFiltro.append('<option value="' + estado.id + '">' + estado.status + '</option>');
                });
            } else {
                selectModal.append('<option value="">No hay estados disponibles</option>');
                selectFiltro.append('<option value="">No hay estados disponibles</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('RESPONSE TEXT:', xhr.responseText);
            selectModal.empty();
            selectModal.append('<option value="">Error al cargar estados</option>');
            selectFiltro.empty();
            selectFiltro.append('<option value="">Error al cargar estados</option>');
        }
    });
}

function guardarEstado() {
    var salesOrderId = $('#salesOrderId').val();
    var tabActivo = $('#estadoTabs li.active a').attr('href');
    var estadoId = $('#nuevoEstado').val();
    var nuevoEstadoNombre = $('#nuevoEstadoNombre').val();
    var descripcion = $('#descripcion').val();

    if (!salesOrderId) {
        alert('No se pudo identificar la orden de venta. Cierre el modal e intente abrirlo nuevamente.');
        return;
    }
    
    // Determinar qué acción se está realizando según el tab activo
    var accionSeleccionada;
    if (tabActivo === '#tabCambiarEstado') {
        accionSeleccionada = 'seleccionar';
        if (!estadoId) {
            alert('Por favor seleccione un estado existente');
            return;
        }
    } else if (tabActivo === '#tabCrearEstado') {
        accionSeleccionada = 'crear';
        if (!nuevoEstadoNombre.trim()) {
            alert('Por favor ingrese un nombre para el nuevo estado');
            return;
        }
    }
    
    var data = {
        sales_order_id: salesOrderId,
        sales_order_product_status_id: (accionSeleccionada === 'seleccionar') ? estadoId : null,
        nuevo_estado: (accionSeleccionada === 'crear') ? nuevoEstadoNombre : null,
        descripcion: (accionSeleccionada === 'seleccionar') ? descripcion : null
    };
    
    $.ajax({
        url: "<?php echo $this->Html->url('/sales_orders/cambiarEstadoOrden'); ?>",
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#modalCambiarEstado').modal('hide');
                alert(response.message);
                if (accionSeleccionada === 'crear') {
                    cargarEstados();
                }
                // Recargar la tabla forzando nueva consulta
                var formData = $('#filterForm').serialize();
                formData += '&_=' + Date.now();
                window["paginationComponentreporteCategoriasTable"].reload(formData);
                // Actualizar resumen
                setTimeout(function() {
                    cargarResumenEstados(formData);
                }, 1000);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al comunicarse con el servidor');
        }
    });
}

function filtrarPorEstado(estadoId) {
    // Mostrar indicador de carga en la tarjeta
    mostrarCargaTarjeta(estadoId);
    
    // Actualizar URL sin recargar la página
    var url = new URL(window.location);
    url.searchParams.set('estado', estadoId);
    window.history.pushState({}, '', url.toString());
    
    // Las tarjetas de estado son filtros INDEPENDIENTES que ignoran filtros de departamento/cliente
    // Solo enviar el estado, sin otros filtros
    var formData = 'estado=' + estadoId + '&_=' + Date.now();
    
    window["paginationComponentreporteCategoriasTable"].reload(formData);
    cargarResumenEstados(formData);
    
    // Actualizar resaltado de tarjetas
    resaltarTarjetaActiva();
}

function limpiarFiltroEstado() {
    // Eliminar parámetro de estado de la URL
    var url = new URL(window.location);
    url.searchParams.delete('estado');
    window.history.pushState({}, '', url.toString());
    
    // Al limpiar filtro de estado, restaurar los filtros del formulario (departamento/cliente)
    var formData = $('#filterForm').serialize();
    
    // Eliminar _method=POST si existe para evitar conflictos
    formData = formData.replace(/_method=POST&?/, '');
    
    formData += '&_=' + Date.now(); // Evitar cache
    
    window["paginationComponentreporteCategoriasTable"].reload(formData);
    cargarResumenEstados(formData);
    
    // Actualizar resaltado de tarjetas
    resaltarTarjetaActiva();
}

function mostrarCargaTarjeta(estadoId) {
    // Mostrar overlay de pantalla completa
    mostrarOverlayCarga();
}

function ocultarCargaTarjeta() {
    // Ocultar overlay de pantalla completa
    ocultarOverlayCarga();
}

function mostrarOverlayCarga() {
    // Crear overlay si no existe
    if ($('#loading-overlay').length === 0) {
        $('body').append(`
            <div id="loading-overlay" class="loading-overlay">
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Cargando datos...</div>
                </div>
            </div>
        `);
    }
    
    // Mostrar overlay con fade in manteniendo el centrado flex
    $('#loading-overlay').stop(true, true).css('display', 'flex').hide().fadeIn(200);
}

function ocultarOverlayCarga() {
    // Ocultar overlay con fade out
    $('#loading-overlay').fadeOut(200, function() {
        $(this).remove();
    });
}

function restaurarBotonActualizar() {
    // Restaurar botón de actualizar a su estado original
    $('input[type="submit"][value="Actualizar"]').removeClass('loading').prop('disabled', false);
}

function resaltarTarjetaActiva() {
    // Ocultar cualquier indicador de carga primero
    ocultarCargaTarjeta();
    
    // Obtener el estado actual de la URL
    var urlParams = new URLSearchParams(window.location.search);
    var estadoActivo = urlParams.get('estado');
    
    // Quitar resaltado de todas las tarjetas
    $('.estado-tarjeta').removeClass('active').css('opacity', '0.7');
    
    // Resaltar tarjeta activa
    if (estadoActivo) {
        $('#tarjeta-estado-' + estadoActivo).addClass('active').css('opacity', '1');
        // Mostrar botón de limpiar filtro
        $('#btnLimpiarFiltroEstado').show();
    } else {
        // Si no hay estado activo, resaltar "Mostrar todos"
        $('#tarjeta-todos').addClass('active').css('opacity', '1');
        // Ocultar botón de limpiar filtro
        $('#btnLimpiarFiltroEstado').hide();
    }
}

// Cargar estados al cargar la página
$(document).ready(function() {
    cargarEstados();
    resaltarTarjetaActiva();
    
    // Botón de refresh - apuntar al input correcto
    $('input[type="submit"][value="Actualizar"]').click(function(e) {
        e.preventDefault();
        
        // Agregar clase de carga al botón
        $(this).addClass('loading').prop('disabled', true);
        
        mostrarOverlayCarga();
        
        var formData = $('#filterForm').serialize();
        formData = formData.replace(/_method=POST&?/, '');
        
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('estado')) {
            formData += '&estado=' + urlParams.get('estado');
        }
        
        formData += '&_=' + Date.now();
        
        window["paginationComponentreporteCategoriasTable"].reload(formData);
        cargarResumenEstados(formData);
    });
});

// Componente de paginación personalizado
class PaginationComponent {
    constructor(options) {
        this.dataSourceUrl = options.dataSourceUrl;
        this.tableId = options.tableId;
        this.currentPage = 1;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.pageSize = 50;
        this.columns = options.columns;
        this.loading = false;
        this.currentFormData = '';
        
        this.init();
    }
    
    init() {
        this.bindEvents();
    }
    
    bindEvents() {
        // Evitar registrar eventos múltiples
        if (this.eventsBound) {
            return;
        }
        this.eventsBound = true;
        
        // Función auxiliar para obtener la instancia actual del componente
        const getComponent = () => window["paginationComponentreporteCategoriasTable"];
        
        // Eventos de paginación - usar delegación de eventos
        $(document).on('click', '.pagination-page-link', (e) => {
            console.log('Click en página:', $(e.currentTarget).data('page'));
            e.preventDefault();
            const page = $(e.currentTarget).data('page');
            const component = getComponent();
            if (component) {
                console.log('Llamando loadPage con página:', page);
                component.loadPage(page);
            } else {
                console.error('Componente no encontrado');
            }
        });
        
        // Eventos de botones de paginación - delegación
        $(document).on('click', '.pagination-first', (e) => {
            console.log('Click en Primero');
            e.preventDefault();
            const component = getComponent();
            if (component) {
                component.loadPage(1);
            }
        });
        
        $(document).on('click', '.pagination-prev', (e) => {
            console.log('Click en Anterior');
            e.preventDefault();
            const component = getComponent();
            if (component) {
                component.loadPage(component.currentPage - 1);
            }
        });
        
        $(document).on('click', '.pagination-next', (e) => {
            console.log('Click en Siguiente');
            e.preventDefault();
            const component = getComponent();
            if (component) {
                component.loadPage(component.currentPage + 1);
            }
        });
        
        $(document).on('click', '.pagination-last', (e) => {
            console.log('Click en Último');
            e.preventDefault();
            const component = getComponent();
            if (component) {
                component.loadPage(component.totalPages);
            }
        });
        
        $(document).on('click', '.pagination-go', (e) => {
            console.log('Click en Ir');
            e.preventDefault();
            const component = getComponent();
            if (component) {
                const targetPage = parseInt($('#goToPage').val());
                if (targetPage >= 1 && targetPage <= component.totalPages) {
                    component.loadPage(targetPage);
                } else {
                    alert('Por favor ingrese un número de página válido entre 1 y ' + component.totalPages);
                }
            }
        });
        
        // Eventos de ordenamiento
        $(document).on('click', '.sort-header', (e) => {
            const field = $(e.currentTarget).data('field');
            const currentOrder = $(e.currentTarget).data('order');
            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            
            $(e.currentTarget).removeClass('asc desc').addClass(newOrder);
            
            const component = getComponent();
            if (component) {
                component.loadPage(1, null, field, newOrder);
            }
        });
    }
    
    loadPage(page, formData = null, sortField = null, sortOrder = null) {
        console.log('loadPage llamado con página:', page, 'formData:', formData);
        this.loading = true;
        this.showLoading();
        
        // Guardar formData si se proporciona
        if (formData) {
            this.currentFormData = formData;
        }
        
        // Construir URL con parámetros
        let url = this.dataSourceUrl;
        const params = new URLSearchParams();
        
        params.append('page', page);
        params.append('limit', this.pageSize);
        
        if (sortField) {
            params.append('sort', sortField);
            params.append('order', sortOrder);
        }
        
        // Usar el formData guardado si no se proporciona uno nuevo
        const formDataToUse = formData || this.currentFormData;
        console.log('formDataToUse:', formDataToUse);
        if (formDataToUse) {
            const formDataParams = new URLSearchParams(formDataToUse);
            for (const [key, value] of formDataParams) {
                params.append(key, value);
            }
        }
        
        url += '?' + params.toString();
        console.log('URL AJAX:', url);
        
        // Realizar llamada AJAX
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                console.log('Respuesta AJAX exitosa:', response);
                this.loading = false;
                this.renderTable(response);
                this.updatePagination(response.pagination);
                this.currentPage = page;
                // El overlay se ocultará en updatePagination, que es el último paso
            },
            error: (xhr, status, error) => {
                console.error('Error AJAX:', xhr, status, error);
                this.loading = false;
                this.hideLoading();
                console.error('Error loading data:', error);
                this.showError('Error al cargar los datos');
                // Ocultar overlay incluso en caso de error
                ocultarCargaTarjeta();
            }
        });
    }
    
    reload(formData = null) {
        this.loadPage(1, formData);
    }
    
    getNestedValue(obj, path) {
        // Convertir 'SalesOrder.sales_order_date' a ['SalesOrder', 'sales_order_date']
        const keys = path.split('.');
        let value = obj;
        
        for (const key of keys) {
            if (key === '0' && Array.isArray(value)) {
                // Acceder al primer elemento del array
                value = value[0];
            } else if (value && typeof value === 'object' && key in value) {
                value = value[key];
            } else {
                return ''; // Retornar vacío si no se encuentra
            }
        }
        
        return value || '';
    }
    
    renderTable(data) {
        console.log('renderTable llamado con data:', data);
        const table = $(`#${this.tableId}`);
        console.log('Tabla encontrada:', table.length);
        const tbody = table.find('tbody');
        tbody.empty();
        
        if (!data.data || data.data.length === 0) {
            tbody.append('<tr><td colspan="' + this.columns.length + '">No se encontraron datos</td></tr>');
            // NO ocultar overlay aquí, dejar que updatePagination lo haga
            return;
        }
        
        console.log('Renderizando', data.data.length, 'filas');
        data.data.forEach((row, index) => {
            const tr = $('<tr>');
            
            this.columns.forEach(column => {
                const td = $('<td>');
                
                let content = '';
                if (column.field === 'Acciones') {
                    content = '<button class="btn btn-sm btn-primary" onclick="abrirModalEstado(' + JSON.stringify(row).replace(/"/g, '&quot;') + ')">Gestión de Estados</button>';
                } else if (column.field === 'ProductCategory.name_category') {
                    // Acceso especial para categoría
                    content = row.ProductCategory && row.ProductCategory[0] ? row.ProductCategory[0].name_category : '';
                } else {
                    // Acceder a datos anidados correctamente
                    content = this.getNestedValue(row, column.field);
                }
                
                td.html(content);
                tr.append(td);
            });
            
            tbody.append(tr);
        });
        
        // NO ocultar overlay aquí, dejar que updatePagination lo haga al final
    }
    
    updatePagination(pagination) {
        console.log('updatePagination llamado con pagination:', pagination);
        this.totalPages = pagination.totalPages;
        this.totalRecords = pagination.totalRecords;
        this.currentPage = pagination.currentPage;
        
        // Limpiar mensajes de paginación anteriores
        $('.pagination-info').remove();
        $('.pagination-controls').remove();
        
        // Crear contenedor de información
        const infoDiv = $('<div class="pagination-info"></div>');
        infoDiv.html(`Mostrando ${pagination.showingFrom} a ${pagination.showingTo} de ${pagination.totalRecords} registros (Página ${pagination.currentPage} de ${pagination.totalPages})`);
        $('.pagination-component-container').append(infoDiv);
        
        // Crear contenedor de controles de paginación
        const controlsDiv = $('<div class="pagination-controls"></div>');
        
        // Botón Primero
        const firstBtn = $('<button type="button" class="pagination-first">Primero</button>');
        firstBtn.prop('disabled', this.currentPage === 1);
        controlsDiv.append(firstBtn);
        
        // Botón Anterior
        const prevBtn = $('<button type="button" class="pagination-prev">Anterior</button>');
        prevBtn.prop('disabled', this.currentPage === 1);
        controlsDiv.append(prevBtn);
        
        // Números de página (mostrar máximo 5 páginas alrededor de la actual)
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);
        
        if (startPage > 1) {
            const page1Btn = $('<button type="button" class="pagination-page-link" data-page="1">1</button>');
            controlsDiv.append(page1Btn);
            if (startPage > 2) {
                controlsDiv.append('<span>...</span>');
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = $('<button type="button" class="pagination-page-link" data-page="' + i + '">' + i + '</button>');
            if (i === this.currentPage) {
                pageBtn.addClass('active');
            }
            controlsDiv.append(pageBtn);
        }
        
        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                controlsDiv.append('<span>...</span>');
            }
            const lastPageBtn = $('<button type="button" class="pagination-page-link" data-page="' + this.totalPages + '">' + this.totalPages + '</button>');
            controlsDiv.append(lastPageBtn);
        }
        
        // Botón Siguiente
        const nextBtn = $('<button type="button" class="pagination-next">Siguiente</button>');
        nextBtn.prop('disabled', this.currentPage === this.totalPages);
        controlsDiv.append(nextBtn);
        
        // Botón Último
        const lastBtn = $('<button type="button" class="pagination-last">Último</button>');
        lastBtn.prop('disabled', this.currentPage === this.totalPages);
        controlsDiv.append(lastBtn);
        
        // Input para ir a página específica
        const goToDiv = $('<div style="display: inline-block; margin-left: 10px;">');
        goToDiv.html('<label for="goToPage">Ir a página:</label> ');
        const pageInput = $('<input type="number" id="goToPage" min="1" max="' + this.totalPages + '" value="' + this.currentPage + '" style="width: 50px;">');
        const goBtn = $('<button type="button" class="pagination-go">Ir</button>');
        goToDiv.append(pageInput);
        goToDiv.append(goBtn);
        controlsDiv.append(goToDiv);
        
        $('.pagination-component-container').append(controlsDiv);
        console.log('Controles de paginación agregados');
        
        // Restaurar botón de actualizar
        restaurarBotonActualizar();
        
        // Ocultar overlay DESPUÉS de actualizar la paginación también
        ocultarCargaTarjeta();
    }
    
    showLoading() {
        mostrarOverlayCarga();
    }
    
    hideLoading() {
        ocultarOverlayCarga();
    }
    
    showError(message) {
        $('.pagination-component-container').append('<div class="error">' + message + '</div>');
    }
}

// Inicializar el componente
window["paginationComponentreporteCategoriasTable"] = new PaginationComponent({
    dataSourceUrl: "<?php echo $dataSourceUrl ?>",
    tableId: "reporteCategoriasTable",
    columns: <?php echo json_encode($columns); ?>
});

$(document).ready(function() {
    formatNumbers();
    // Verificar si el componente existe
    if (typeof window["paginationComponentreporteCategoriasTable"] !== 'undefined') {
        
        // Forzar carga inicial con datos del formulario
        var formData = $('#filterForm').serialize();
        
        // Agregar parámetro de estado si existe en la URL
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('estado')) {
            formData += '&estado=' + urlParams.get('estado');
        }

        window["paginationComponentreporteCategoriasTable"].loadPage(1, formData);
        cargarResumenEstados(formData);
    }
});

// Botones de mes
$('#previousmonth, #nextmonth').click(function() {
    setTimeout(function() {
        var formData = $('#filterForm').serialize();
        window["paginationComponentreporteCategoriasTable"].reload(formData);
        cargarResumenEstados(formData);
    }, 100);
});


// Formulario de filtros
$('#filterForm').submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    
    // Eliminar _method=POST si existe para evitar conflictos
    formData = formData.replace(/_method=POST&?/, '');
    
    // Agregar filtro de estado si hay uno activo
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('estado')) {
        formData += '&estado=' + urlParams.get('estado');
    }
    
    // Agregar timestamp para evitar cache
    formData += '&_=' + Date.now();
    
    window["paginationComponentreporteCategoriasTable"].reload(formData);
    cargarResumenEstados(formData);
});

// Función para formatear números
function formatNumbers() {
    $('.number').each(function() {
        const num = $(this).text();
        if (num && !isNaN(num)) {
            $(this).text(Number(num).toLocaleString('es-ES'));
        }
    });
}

function cargarResumenEstados(formData) {
    var resumenData = formData || $('#filterForm').serialize();
    resumenData = resumenData.replace(/_method=POST&?/, '');
    resumenData = resumenData.replace(/&?_=\d+/, '');

    $.ajax({
        url: "<?php echo $this->Html->url('/sales_orders/getResumenEstados'); ?>",
        type: 'GET',
        data: resumenData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Actualizar tarjetas con los datos
                $('#count-esperando').text(response.data['3'] ? response.data['3'].count : 0);
                $('#percent-esperando').text(response.data['3'] ? response.data['3'].porcentaje + '%' : '0%');
                
                $('#count-produccion').text(response.data['2'] ? response.data['2'].count : 0);
                $('#percent-produccion').text(response.data['2'] ? response.data['2'].porcentaje + '%' : '0%');
                
                $('#count-entregado').text(response.data['4'] ? response.data['4'].count : 0);
                $('#percent-entregado').text(response.data['4'] ? response.data['4'].porcentaje + '%' : '0%');
                
                $('#count-anulado').text(response.data['5'] ? response.data['5'].count : 0);
                $('#percent-anulado').text(response.data['5'] ? response.data['5'].porcentaje + '%' : '0%');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en cargarResumenEstados:', xhr.responseText);
        }
    });
}

</script>

</div>
