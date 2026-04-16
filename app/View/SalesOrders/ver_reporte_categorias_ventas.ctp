<div class="sales index" style='width:100%;'>

<?php	

	echo "<h2>".__('Reporte orden de venta por categoria')."</h2>";

	

	echo $this->Form->create('Report', array('id' => 'filterForm')); 

		echo "<fieldset>";

			echo $this->Form->input('Report.startdate',array('type'=>'date','label'=>__('Start Date'),'dateFormat'=>'DMY','default'=>$startDate));

			echo $this->Form->input('Report.enddate',array('type'=>'date','label'=>__('End Date'),'dateFormat'=>'DMY','default'=>$endDate));

			echo "<br/>";			

			echo $this->Form->input('Report.product_category_id',array('label'=>__('Departamento'),'default'=>$product_category_id,'empty'=>array('0'=>__('Seleccione Departamento'))));

		echo "</fieldset>";

		echo "<button id='previousmonth' class='monthswitcher'>Mes Previo</button>";

		echo "<button id='nextmonth' class='monthswitcher'>Mes Siguiente</button>";

	echo "<br/>";

	echo $this->Form->end(__('Refresh'), array('id' => 'refreshButton')); 

	

	echo $this->Html->link(__('Guardar como Excel'), array('action' => 'guardarReporteCategoriasVentas'), array( 'class' => 'btn btn-primary')); 

?>

</div>



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

        'field' => 'ProductCategory.0.name_category',

        'width' => '20%'

    ),

    array(

        'title' => __('Producto'),

        'field' => 'SalesOrderProduct.0.sale_description',

        'width' => '20%'

    ),

    array(

        'title' => __('Cantidad'),

        'field' => 'SalesOrderProduct.0.product_quantity',

        'width' => '10%',

        'cssClass' => 'number centered'

    ),

    array(

        'title' => __('Acciones'),

        'field' => 'Acciones',

        'width' => '5%'

    )

);



// Opciones del componente

$options = array(

    'tableId' => 'reporteCategoriasTable',

    'containerClass' => 'reporte-container',

    'cssClass' => 'table table-striped table-bordered',

    'pageSize' => 50,

    'showInfo' => true,

    'showPagination' => true,

    'loadingText' => 'Cargando datos...',

    'noDataText' => 'No se encontraron registros'

);



// URL del endpoint AJAX - Usar el nuevo endpoint dedicado a datos

$dataSourceUrl = $this->Html->url(array('controller' => 'sales_orders', 'action' => 'getReporteCategoriasVentasData'));



// Renderizar el componente de paginación

echo $this->Pagination->renderAjaxTable($dataSourceUrl, $columns, $options);

?>



<script>

// Componente de Paginación
(function($) {
    var PaginationComponent = function(options) {
        this.options = options;
        this.currentPage = 1;
        this.totalPages = 1;
        this.totalRecords = 0;
        this.isLoading = false;
        this.init();
    };
    
    PaginationComponent.prototype = {
        init: function() {
            this.bindEvents();
            this.loadPage(1);
        },
        
        bindEvents: function() {
            var self = this;
            $("#btn-prev-" + this.options.tableId).click(function() {
                if (self.currentPage > 1) {
                    self.loadPage(self.currentPage - 1);
                }
            });
            
            $("#btn-next-" + this.options.tableId).click(function() {
                if (self.currentPage < self.totalPages) {
                    self.loadPage(self.currentPage + 1);
                }
            });
        },
        
        loadPage: function(page, additionalData) {
            if (this.isLoading) return;
            
            this.isLoading = true;
            this.showLoading();
            
            var self = this;
            var formData = additionalData || {};
            formData.page = page;
            
            $.ajax({
                url: this.options.dataSourceUrl,
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        self.updateTable(response.data);
                        self.updatePagination(response.pagination);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error AJAX:", xhr.responseText);
                },
                complete: function() {
                    self.isLoading = false;
                    self.hideLoading();
                    
                    var $refreshBtn = $("#refreshButton");
                    if ($refreshBtn.length) {
                        $refreshBtn.html("Refresh");
                        $refreshBtn.prop("disabled", false);
                    }
                }
            });
        },
        
        reload: function(formData) {
            this.loadPage(1, formData);
        },
        
        updateTable: function(data) {
            var tbody = $("#" + this.options.tableId + " tbody");
            tbody.empty();
            
            if (!data || data.length === 0) {
                tbody.append("<tr><td colspan=\"" + this.options.columns.length + "\" style=\"text-align: center;\">" + this.options.noDataText + "</td></tr>");
                return;
            }
            
            var self = this;
            $.each(data, function(index, row) {
                self.renderRow(row, index, tbody);
            });
        },
        
        renderRow: function(row, index, tbody) {
            var tr = $('<tr>');
            
            // Render each column
            this.options.columns.forEach(function(column, colIndex) {
                var td = $('<td>');
                
                if (column.cssClass) {
                    td.addClass(column.cssClass);
                }
                
                var content = '';
                
                // Manejar cada columna específica
                if (column.field === 'SalesOrder.sales_order_date') {
                    var orderDate = new Date(row.SalesOrder.sales_order_date);
                    content = orderDate.toLocaleDateString("es-ES");
                } else if (column.field === 'SalesOrder.sales_order_code') {
                    content = '<a  target="_blank">' + row.SalesOrder.sales_order_code + '</a>';
                } else if (column.field === 'Client.name') {
                    content = row.Client && row.Client.name ? row.Client.name : "Sin cliente";
                } else if (column.field === 'ProductCategory.0.name_category') {
                    content = row.ProductCategory && row.ProductCategory[0] && row.ProductCategory[0].name_category ? row.ProductCategory[0].name_category : "Sin categoría";
                } else if (column.field === 'SalesOrderProduct.0.sale_description') {
                    content = row.SalesOrderProduct && row.SalesOrderProduct[0] && row.SalesOrderProduct[0].sale_description ? row.SalesOrderProduct[0].sale_description : "N/A";
                } else if (column.field === 'SalesOrderProduct.0.product_quantity') {
                    content = '<span>' + (row.SalesOrderProduct && row.SalesOrderProduct[0] && row.SalesOrderProduct[0].product_quantity ? row.SalesOrderProduct[0].product_quantity : "0") + '</span>';
                } else if (column.field === 'Acciones') {
                    content = '<button class="btn btn-sm btn-primary" onclick="abrirModalEstado(' + JSON.stringify(row).replace(/"/g, '&quot;') + ')">Gestión de Estados</button>';
                } else if (row[column.field]) {
                    content = row[column.field];
                }
                
                td.html(content);
                tr.append(td);
            });
            
            tbody.append(tr);
        },
        
        updatePagination: function(pagination) {
            this.currentPage = pagination.currentPage;
            this.totalPages = pagination.totalPages;
            this.totalRecords = pagination.totalRecords;
            
            // Actualizar información
            if (this.options.showInfo && pagination) {
                $("#showing-from-" + this.options.tableId).text(pagination.showingFrom);
                $("#showing-to-" + this.options.tableId).text(pagination.showingTo);
                $("#total-records-" + this.options.tableId).text(pagination.totalRecords);
                $("#current-page-" + this.options.tableId).text(pagination.currentPage);
                $("#total-pages-" + this.options.tableId).text(pagination.totalPages);
                $("#info-" + this.options.tableId).show();
            }
            
            // Actualizar botones
            if (this.options.showPagination) {
                $("#btn-prev-" + this.options.tableId).prop("disabled", pagination.currentPage <= 1);
                $("#btn-next-" + this.options.tableId).prop("disabled", pagination.currentPage >= pagination.totalPages);
                
                // Generar números de página
                var startPage = Math.max(1, pagination.currentPage - 3);
                var endPage = Math.min(pagination.totalPages, pagination.currentPage + 3);
                var pageNumbersHtml = "";
                
                for (var i = startPage; i <= endPage; i++) {
                    if (i === pagination.currentPage) {
                        pageNumbersHtml += "<span class=\"btn btn-primary\" style=\"margin: 0 5px;\">" + i + "</span>";
                    } else {
                        pageNumbersHtml += "<button class=\"btn btn-default\" style=\"margin: 0 5px;\" onclick=\"paginationComponent" + this.options.tableId + ".loadPage(" + i + ")\">" + i + "</button>";
                    }
                }
                
                $("#page-numbers-" + this.options.tableId).html(pageNumbersHtml);
                $("#pagination-controls-" + this.options.tableId).show();
            }
        },
        
        showLoading: function() {
            $("#loading-" + this.options.tableId).show();
        },
        
        hideLoading: function() {
            $("#loading-" + this.options.tableId).hide();
        }
    };
    
 
    window["paginationComponentreporteCategoriasTable"] = new PaginationComponent({
        dataSourceUrl: "/maspublicidad/sales_orders/getReporteCategoriasVentasData",
        tableId: "reporteCategoriasTable",
        columns: [{"title":"Fecha","field":"SalesOrder.sales_order_date","width":"10%"},{"title":"C\u00f3digo Orden","field":"SalesOrder.sales_order_code","width":"15%"},{"title":"Cliente","field":"Client.name","width":"20%"},{"title":"Categor\u00eda","field":"ProductCategory.0.name_category","width":"20%"},{"title":"Producto","field":"SalesOrderProduct.0.sale_description","width":"20%"},{"title":"Cantidad","field":"SalesOrderProduct.0.product_quantity","width":"10%","cssClass":"number centered"},{"title":"Acciones","field":"Acciones","width":"5%"}],
        pageSize: 50,
        showInfo: true,
        showPagination: true,
        loadingText: "Cargando...",
        noDataText: "No se encontraron registros"
    });
 
    
})(jQuery);

function formatNumbers(){
	$("td.number").each(function(){
		$(this).number(true,0);
	});
}


$(document).ready(function(){
	formatNumbers();
	// Verificar si el componente existe
	if (typeof window["paginationComponentreporteCategoriasTable"] !== 'undefined') {
		
		// Forzar carga inicial con datos del formulario
		var formData = $('#filterForm').serialize();

		window["paginationComponentreporteCategoriasTable"].loadPage(1, formData);
	}/* else {
		console.error("ERROR: Componente de paginación no inicializado");
	}*/
	
	// Manejar envío del formulario
	$('#filterForm').on('submit', function(e) {
		e.preventDefault();
		
		// Mostrar loading en el botón
		var $refreshBtn = $('#refreshButton');
		var originalText = $refreshBtn.html();
		$refreshBtn.html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
		$refreshBtn.prop('disabled', true);
		
		// Obtener datos del formulario
		var formData = $('#filterForm').serialize();
		
		// Recargar el componente con los filtros
		window["paginationComponentreporteCategoriasTable"].reload(formData);
		
		// Restaurar el botón después de un tiempo (en caso de que no se complete)
		setTimeout(function() {
			$refreshBtn.html(originalText);
			$refreshBtn.prop('disabled', false);
		}, 5000);
	});
	
	
	// Botones de mes
	$('#previousmonth, #nextmonth').click(function() {
		setTimeout(function() {
			var formData = $('#filterForm').serialize();
			window["paginationComponentreporteCategoriasTable"].reload(formData);
		}, 100);
	});
});

</script>


<style>

.pagination-container {

    margin: 20px 0;

}



.pagination-info {

    font-size: 14px;

    color: #666;

}



.pagination-controls {

    clear: both;

}



.pagination-controls .btn {

    padding: 6px 12px;

    font-size: 12px;

    line-height: 1.4;

}



.table {
    margin-bottom: 20px;
    width: 100% !important;
    border-collapse: collapse;
    background-color: #fff;
    border: 1px solid #ddd;
}

.table th, .table td {
    padding: 8px 12px;
    border: 1px solid #ddd;
    vertical-align: middle;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: bold;
    border-bottom: 2px solid #dee2e6;
}

.table tbody tr:nth-child(odd) {
    background-color: #f8f9fa;
}

/* Forzar anchos específicos de columnas */
#reporteCategoriasTable th:nth-child(1),
#reporteCategoriasTable td:nth-child(1) {
    width: 10% !important;
    min-width: 10% !important;
    max-width: 10% !important;
}

#reporteCategoriasTable th:nth-child(2),
#reporteCategoriasTable td:nth-child(2) {
    width: 15% !important;
    min-width: 15% !important;
    max-width: 15% !important;
}

#reporteCategoriasTable th:nth-child(3),
#reporteCategoriasTable td:nth-child(3) {
    width: 20% !important;
    min-width: 20% !important;
    max-width: 20% !important;
}

#reporteCategoriasTable th:nth-child(4),
#reporteCategoriasTable td:nth-child(4) {
    width: 20% !important;
    min-width: 20% !important;
    max-width: 20% !important;
}

#reporteCategoriasTable th:nth-child(5),
#reporteCategoriasTable td:nth-child(5) {
    width: 20% !important;
    min-width: 20% !important;
    max-width: 20% !important;
}

#reporteCategoriasTable th:nth-child(6),
#reporteCategoriasTable td:nth-child(6) {
    width: 10% !important;
    min-width: 10% !important;
    max-width: 10% !important;
}

#reporteCategoriasTable th:nth-child(7),
#reporteCategoriasTable td:nth-child(7) {
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
    display: flex;
    align-items: center;
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
                                <div style="flex: 0 0 40%; padding: 0 15px 0 0;">
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
                                <div style="flex: 0 0 60%; padding: 0 0 0 15px;">
                                    <div style="display: flex; flex-direction: column; height: 100%;">
                                        <div style="margin-bottom: 20px;">
                                            <div class="form-group">
                                                <label for="nuevoEstado" style="font-weight: bold;">
                                                    <i class="fas fa-exchange-alt"></i> Seleccionar Nuevo Estado:
                                                </label>
                                                <select id="nuevoEstado" name="production_order_state_id" class="form-control" style="height: 45px;">
                                                    <option value="">Cargando estados...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div style="flex: 1;">
                                            <div class="form-group" style="height: 100%;">
                                                <label for="descripcion" style="font-weight: bold;">
                                                    <i class="fas fa-comment-alt"></i> Descripción del Cambio:
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
                                <div style="flex: 0 0 65%; padding: 0 15px 0 0;">
                                    <div class="form-group">
                                        <label for="nuevoEstadoNombre" style="font-weight: bold; font-size: 16px;">
                                            <i class="fas fa-plus-circle"></i> Nombre del Nuevo Estado:
                                        </label>
                                        <input type="text" id="nuevoEstadoNombre" name="nuevo_estado" class="form-control" style="height: 50px; font-size: 16px;" placeholder="Ingrese el nombre del nuevo estado">
                                    </div>
                                </div>
                                <!-- Columna Derecha: Nota -->
                                <div style="flex: 0 0 35%; padding: 0 0 0 15px;">
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

<script>
function abrirModalEstado(rowData) {
    // Extraer datos de la orden
    var salesOrderId = rowData.SalesOrder.id;
    var salesOrderCode = rowData.SalesOrder.sales_order_code;
    var salesOrderDate = rowData.SalesOrder.sales_order_date;
    var clientName = rowData.Client ? rowData.Client.name : 'Cliente no disponible';
    var clientEmail = rowData.Client ? (rowData.Client.email || 'Sin email') : 'Sin email';
    var currentState = rowData.ProductionOrderState ? rowData.ProductionOrderState.name_state : 'Sin estado asignado';
    
    // Establecer ID de la orden
    $('#salesOrderId').val(salesOrderId);
    
    // Mostrar información de la orden inmediatamente
    $('#modalOrdenInfo').html(
        '<strong>' + salesOrderCode + '</strong><br>' +
        '<small>Fecha: ' + new Date(salesOrderDate).toLocaleDateString('es-ES') + '</small>'
    );
    
    // Mostrar información del cliente inmediatamente
    $('#modalClienteInfo').html(
        '<strong>' + clientName + '</strong><br>' +
        '<small>' + clientEmail + '</small>'
    );
    
    // Mostrar estado actual inmediatamente
    $('#estadoActual').text(currentState);
    
    // Resetear al primer tab
    $('#cambiarEstadoTab').tab('show');
    // Limpiar formulario
    $('#formCambiarEstado')[0].reset();
    
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

    // Mostrar estado de carga
    var select = $('#nuevoEstado');
    select.empty();
    select.append('<option value="">Cargando estados...</option>');
    
    $.ajax({
        url: '/maspublicidad/production_order_states/getEstados',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
       
            
            select.empty();
            select.append('<option value="">Seleccione un estado...</option>');
            
            if (response.success && response.estados && response.estados.length > 0) {

                $.each(response.estados, function(index, estado) {
                    select.append('<option value="' + estado.id + '">' + estado.name_state + '</option>');
                });
                
            } else {
                select.append('<option value="">No hay estados disponibles</option>');
            }
        },
        error: function(xhr, status, error) {
            
            console.error('RESPONSE TEXT:', xhr.responseText);
   
            
            select.empty();
            select.append('<option value="">Error al cargar estados</option>');
        }
    });
}

function guardarEstado() {
    var salesOrderId = $('#salesOrderId').val();
    var tabActivo = $('#estadoTabs li.active a').attr('href');
    var estadoId = $('#nuevoEstado').val();
    var nuevoEstadoNombre = $('#nuevoEstadoNombre').val();
    var descripcion = $('#descripcion').val();
    
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
        production_order_state_id: (accionSeleccionada === 'seleccionar') ? estadoId : null,
        nuevo_estado: (accionSeleccionada === 'crear') ? nuevoEstadoNombre : null,
        descripcion: (accionSeleccionada === 'seleccionar') ? descripcion : null
    };
    
    $.ajax({
        url: '/maspublicidad/sales_orders/cambiarEstadoOrden',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#modalCambiarEstado').modal('hide');
                alert('Estado actualizado correctamente');
                // Recargar la tabla
                PaginationComponent.reloadTable();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error al comunicarse con el servidor');
        }
    });
}
</script>

