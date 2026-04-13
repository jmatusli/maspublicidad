<?php
App::uses('AppHelper', 'View/Helper');

class PaginationHelper extends AppHelper {
    
    public $helpers = array('Html', 'Js', 'Form');
    
    /**
     * Renderiza un componente de paginación AJAX completo
     * 
     * @param string $dataSourceUrl URL del endpoint AJAX para obtener datos
     * @param array $columns Configuración de columnas
     * @param array $options Opciones adicionales
     * @return string HTML del componente
     */
    public function renderAjaxTable($dataSourceUrl, $columns = array(), $options = array()) {
        $defaultOptions = array(
            'tableId' => 'ajaxDataTable',
            'loadingText' => 'Cargando...',
            'noDataText' => 'No hay datos disponibles',
            'pageSize' => 50,
            'showInfo' => true,
            'showPagination' => true,
            'showSearch' => false,
            'cssClass' => 'table table-striped table-bordered',
            'containerClass' => 'pagination-container'
        );
        
        $options = array_merge($defaultOptions, $options);
        
        // Generar HTML del componente
        $html = $this->_generateContainerHtml($options);
        $html .= $this->_generateTableHtml($columns, $options);
        $html .= $this->_generatePaginationHtml($options);
        
        return $html;
    }
    
    /**
     * Genera el contenedor principal
     */
    private function _generateContainerHtml($options) {
        return '<div id="' . $options['containerClass'] . '" class="' . $options['containerClass'] . '">';
    }
    
    /**
     * Genera la estructura de la tabla
     */
    private function _generateTableHtml($columns, $options) {
        $html = '<div id="table-container-' . $options['tableId'] . '" style="margin: 0 20px; overflow-x: auto; position: relative;">';
        $html .= '<div id="loading-' . $options['tableId'] . '" class="loading-indicator" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 99999; background: rgba(51, 122, 183, 0.3); color: white; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(51, 122, 183, 0.2); border: 1px solid rgba(46, 109, 164, 0.4); font-size: 16px; font-weight: 600; width: 70%; text-align: center;">';
        $html .= '<i class="fa fa-spinner fa-spin"></i> ' . $options['loadingText'];
        $html .= '</div>';
        
        $html .= '<table id="' . $options['tableId'] . '" class="' . $options['cssClass'] . '">';
        $html .= '<thead><tr>';
        
        foreach ($columns as $column) {
            $html .= '<th style="width: ' . (isset($column['width']) ? $column['width'] : 'auto') . ';">';
            $html .= $column['title'];
            $html .= '</th>';
        }
        
        $html .= '</tr></thead>';
        $html .= '<tbody></tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Genera los controles de paginación
     */
    private function _generatePaginationHtml($options) {
        $html = '';
        
        if ($options['showInfo']) {
            $html .= '<div id="info-' . $options['tableId'] . '" class="pagination-info" style="margin: 10px 0; padding: 10px; background: #f5f5f5; border-radius: 5px; display: none;">';
            $html .= '<strong>Mostrando <span id="showing-from-' . $options['tableId'] . '">0</span> - <span id="showing-to-' . $options['tableId'] . '">0</span> de <span id="total-records-' . $options['tableId'] . '">0</span> registros</strong>';
            $html .= ' | Página <span id="current-page-' . $options['tableId'] . '">1</span> de <span id="total-pages-' . $options['tableId'] . '">1</span>';
            $html .= '</div>';
        }
        
        if ($options['showPagination']) {
            $html .= '<div id="pagination-controls-' . $options['tableId'] . '" class="pagination-controls" style="margin: 20px 0; text-align: center; display: none;">';
            $html .= '<button id="btn-prev-' . $options['tableId'] . '" class="btn btn-default" style="margin: 0 5px;" disabled>« Anterior</button>';
            $html .= '<span id="page-numbers-' . $options['tableId'] . '"></span>';
            $html .= '<button id="btn-next-' . $options['tableId'] . '" class="btn btn-default" style="margin: 0 5px;">Siguiente »</button>';
            $html .= '</div>';
        }
        
        $html .= '</div>'; // Cerrar container
        
        return $html;
    }
}
?>
