<?php
	define('COMPANY_NAME','Mas Publicidad');
	define('COMPANY_URL','www.maspublicidad.com.ni');
	define('COMPANY_MAIL','info@maspublicidad.com.ni');
	define('COMPANY_ADDRESS','De los semáforos de ENEL Central 200 mts al lago');
	define('COMPANY_PHONE','2277-5313');
	define('COMPANY_RUC','J0310000167575');
	
	define('DEFAULT_EMAIL','info@maspublicidad.com.ni');

	define('CURRENCY_CS','1');
	define('CURRENCY_USD','2');

	define('ROLE_ADMIN','5');
	define('ROLE_ASSISTANT','6');
	define('ROLE_SALES_EXECUTIVE','7');
	define('ROLE_DEPARTMENT_SUPERVISOR_PRODUCTION','8');
  define('ROLE_DEPARTMENT_BOSS','8');
	define('ROLE_OPERATOR','9');
	define('ROLE_DEPARTMENT_SUPERVISOR_SALES','10');
  
	define('NA','N/A');
	
	define('PRODUCT_STATUS_REGISTERED','1');
	define('PRODUCT_STATUS_AUTHORIZED','2');
	define('PRODUCT_STATUS_AWAITING_PURCHASE','3');
	define('PRODUCT_STATUS_AWAITING_RECEPTION','4');
	define('PRODUCT_STATUS_AWAITING_PRODUCTION','5');
	define('PRODUCT_STATUS_READY_FOR_DELIVERY','6');
	define('PRODUCT_STATUS_DELIVERED','7');
  
  define('PRODUCTION_ORDER_STATE_AWAITING_PREVIOUS','1');
  define('PRODUCTION_ORDER_STATE_AWAITING_PURCHASE','2');
	define('PRODUCTION_ORDER_STATE_AWAITING_PRODUCTION','3');
	define('PRODUCTION_ORDER_STATE_IN_PRODUCTION','4');
  define('PRODUCTION_ORDER_STATE_SENT_NEXT_DEPARTMENT','5');
	define('PRODUCTION_ORDER_STATE_READY_FOR_DELIVERY','6');
  define('PRODUCTION_ORDER_STATE_DELIVERED','7');
  
  define('DEPARTMENTS_MAX','5');
  define('INSTRUCTIONS_MAX','5');
  
	define('HOLIDAY_TYPE_SOLICITADO','1');
	define('HOLIDAY_TYPE_PROGRAMADO','2');
	define('HOLIDAY_TYPE_AUSENCIA_LABORAL','3');
	define('HOLIDAY_TYPE_FERIADO','4');
	
	define('INVENTORY_PRODUCT_LINE_PER_METER','11');
	
	define('ACTION_TYPE_CALL','1');
	define('ACTION_TYPE_VISIT','2');
	define('ACTION_TYPE_OTHER','3');
	
  
	/*
	define('MOVEMENT_PURCHASE','4');
	define('MOVEMENT_SALE','5');
	
	define('PRODUCT_TYPE_PREFORMA','10');
	define('PRODUCT_TYPE_CAP','9');
	define('PRODUCT_TYPE_BOTTLE','11');
	
	define('CASH_RECEIPT_TYPE_CREDIT','1');
	define('CASH_RECEIPT_TYPE_REMISSION','2');
	define('CASH_RECEIPT_TYPE_OTHER','3');
	
	define('PRODUCTION_RESULT_CODE_A','1');
	define('PRODUCTION_RESULT_CODE_B','2');
	define('PRODUCTION_RESULT_CODE_C','3');
		
	define('ACCOUNTING_CODE_CASHBOXES','4'); // accounting code 101-001
	define('ACCOUNTING_CODE_CASHBOX_MAIN','5'); // accounting code 101-001-001
	define('ACCOUNTING_CODE_BANKS','11'); // accounting code 101-003
	define('ACCOUNTING_CODE_CUENTAS_COBRAR_CLIENTES','17'); // accounting code 101-004-001
	define('ACCOUNTING_CODE_INVENTORY','29'); // accounting code 101-005
	define('ACCOUNTING_CODE_INVENTORY_RAW_MATERIAL','91'); // accounting code 101-005-001
	define('ACCOUNTING_CODE_INVENTORY_FINISHED_PRODUCT','92'); // accounting code 101-005-002
	define('ACCOUNTING_CODE_INVENTORY_OTHER_MATERIAL','93'); // accounting code 101-005-003
	define('ACCOUNTING_CODE_PROVIDERS','34'); // accounting code 201-001
	define('ACCOUNTING_CODE_INGRESOS_VENTA','50'); // accounting code 401
	define('ACCOUNTING_CODE_INGRESOS_DESCUENTOS','55'); // accounting code 402
	define('ACCOUNTING_CODE_INGRESOS_OTROS','58'); // accounting code 403	
	define('ACCOUNTING_CODE_COSTS','60'); // accounting code 500
	define('ACCOUNTING_CODE_COSTOS_VENTA','61'); // accounting code 501
	define('ACCOUNTING_CODE_SPENDING_OPERATIONS','64'); // accounting code 600
	define('ACCOUNTING_CODE_GASTOS_ADMIN','65'); // accounting code 601
	define('ACCOUNTING_CODE_GASTOS_VENTA','73'); // accounting code 602
	define('ACCOUNTING_CODE_GASTOS_FINANCIEROS','74'); // accounting code 603
	define('ACCOUNTING_CODE_GASTOS_PRODUCCION','79'); // accounting code 604
	define('ACCOUNTING_CODE_GASTOS_OTROS','75'); // accounting code 605
	
	define('ACCOUNTING_CODE_RETENCIONES_POR_COBRAR','85'); // accounting code 101-004-004
	define('ACCOUNTING_CODE_IVA_POR_PAGAR','84'); // accounting code 201-002-3
	define('ACCOUNTING_CODE_CUENTAS_OTROS_INGRESOS','59'); // accounting code 403-001
	define('ACCOUNTING_CODE_INGRESOS_DIFERENCIA_CAMBIARIA','88'); // accounting code 403-002
	define('ACCOUNTING_CODE_DESCUENTO_SOBRE_VENTA','86'); // accounting code 602-002
	define('ACCOUNTING_CODE_GASTO_DIFERENCIA_CAMBIARIA','87'); // accounting code 603-001
	
	define('ACCOUNTING_CODE_BANKS_CS','12'); // accounting code 101-003-001
	define('ACCOUNTING_CODE_BANKS_USD','14'); // accounting code 101-003-002
	
	define('ACCOUNTING_CODE_BANK_CS','83'); // accounting code 101-003-001-001
	define('ACCOUNTING_CODE_BANK_USD','153'); // accounting code 101-003-002-001
	
	define('ACCOUNTING_CODE_ACTIVOS','1'); // accounting code 100
	define('ACCOUNTING_CODE_PASIVOS','32'); // accounting code 200
	
	define('ACCOUNTING_REGISTER_TYPE_CD','2'); 
	define('ACCOUNTING_REGISTER_TYPE_CP','3'); 
	*/
	
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = [	
		'Session',
		//'DebugKit.Toolbar',
		'Acl',
    'Auth' => [
      'authorize' => [
        'Actions' => ['actionPath' => 'controllers'],
      ],
    ],
      //'AclMenu.Menu'
      /******* for original auth use
      'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'locations',
                'action' => 'display',
				'home'
            ),
            'logoutRedirect' => array(
                'controller' => 'users',
                'action' => 'login'
            ),
			'authorize' =>  array('Controller')
        )
		*****************/
	];
	public $helpers = [ 
		'Html', 
		'Form', 
		'Session',
		'MenuBuilder.MenuBuilder' => [
			'authVar' => 'user',
			'authModel' => 'User',
			'authField' => 'role_id',
		],
	];
	
	/*
	function isAuthorized($user=null){
		$this->loadModel('User');
		
		if ($action_name==null){
			$action_name= $this->params['action'];
		}
		if ($controller_name==null){
			$controller_name= $this->params['controller'];
		}
		
		echo "controller is ".$controller_name." and action name is ".$action_name."<br/>";
		return true;
		//$foundUser=$this->User->find('first',array(
		//	'conditions'=>array(
		//		'User.id'=>$user['id'],
		//	),
		//));
		//if (empty($foundUser)){
		//	return false;
		//}
		//else {
		//	if ($foundUser['User']['modified']!=$user['modified']){
		//		return false;			
		//	}
		//	else {
		//		return true;
		//	}
		//}
	}
	*/
	function recordUserActivity($userName,$userEvent){
		$this->request->data['UserLog']['user_id'] = $this->Auth->User('id');;
		$this->request->data['UserLog']['username'] = $userName;
		$this->request->data['UserLog']['event'] = $this->normalizeChars($userEvent);
		$this->request->data['UserLog']['created'] = date("Y-m-d H:i:s");
		
		$this->loadModel('UserLog');
		$this ->UserLog->create();
		$this->UserLog->save($this->request->data);
	}
	
	function recordUserAction($item_id=null,$action_name=null,$controller_name=null){
		if ($item_id==null){
			$item_id=0;
			if (!empty($this->params['pass'])){
				$item_id=$this->params['pass']['0'];
			}
		}
		if ($action_name==null){
			$action_name= $this->params['action'];
		}
		if ($controller_name==null){
			$controller_name= $this->params['controller'];
		}
		//echo "action name is ".$action_name."<br/>";
		//pr($this->params);
		//echo "controller is ".$currentController."<br/>";
		//echo "action is ".$currentAction."<br/>";
		//echo "parameter is ".$currentParameter."<br/>";
		
    
		$this->loadModel('UserAction');
		$userActionData=[];
		$userActionData['UserAction']['user_id']=$this->Auth->User('id');
		$userActionData['UserAction']['controller_name']=$controller_name;
		$userActionData['UserAction']['action_name']=$action_name;
		$userActionData['UserAction']['item_id']=$item_id;
		$userActionData['UserAction']['action_datetime']= date("Y-m-d H:i:s");
		$this ->UserAction->create();
		$this->UserAction->save($userActionData);
	}
		
  public function beforeFilter() {
		//Configure AuthComponent
		$this->Auth->authError = "No tiene permiso para ver este funcionalidad";
    $this->Auth->loginAction = [
      'controller' => 'users',
      'action' => 'login'
    ];
    $this->Auth->logoutRedirect = [
      'controller' => 'users',
      'action' => 'login'
    ];
		$this->Auth->loginRedirect = [
		  'controller' => 'quotations',
		  'action' => 'index',
		  'home'
		];
		//pr($this->Auth);
		$user = $this->Auth->user();
		$this->set(compact('user'));
		//pr($user);
		$userid=$user['id'];
		$this->set(compact('userid'));
		$username=$user['username'];
		$this->set(compact('username'));
		
		$userrole = $this->Auth->User('role_id');
		//pr($userrole);
		$userhomepage = $this->userhome($userrole);
		//pr($userhomepage);
		$this->set(compact('userrole','userhomepage'));
		
		$this->loadModel('ExchangeRate');
		$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate(date('Y-m-d'));
		$currentExchangeRate=$exchangeRate['ExchangeRate']['rate'];
		$this->set(compact('currentExchangeRate'));
	
    //$this->Auth->allow();
		/*
		if ($this->Session->check('Config.language')) {
            Configure::write('Config.language', $this->Session->read('Config.language'));
        }
		*/
		
		$this->loadModel('User');
		$recipientUsers=$this->User->find('list',array(
			//'fields'=>array('User.id','CONCAT(User.first_name," ",User.last_name) AS completename'),
			'conditions'=>array(
				'User.id != '=>$userid,
			),
		));
		$this->set(compact('recipientUsers'));
		//pr($recipientUsers);
		
		$this->loadModel('MessageRecipient');
		$unreadMessages=$this->MessageRecipient->find('list',array(
			'conditions'=>array(
				'MessageRecipient.recipient_user_id'=>$userid,
				'MessageRecipient.bool_read'=>false,
			),
		));
		$this->set(compact('unreadMessages'));
		
		// Define your menu for MenuBuilder
		$menu = array(
      'main-menu' => [
				[
          'title' => 'Ventas',
          'url' => ['controller' => 'quotations', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'quotationmenu',
        ],
			  [
          'title' => 'Producción',
          'url' => ['controller' => 'salesOrders', 'action' => 'reporteProduccionPendiente'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'productionmenu',
        ],
        [
          'title' => 'Reportes',
          'url' => ['controller' => 'quotations', 'action' => 'verReporteGestionDeVentas'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'reportmenu',
        ],
				[
          'title' => 'Productos',
          'url' => ['controller' => 'products', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'productmenu',
        ],
				[
          'title' => __('Clients'),
          'url' => ['controller' => 'clients', 'action' => 'index'],
					//'url' => ['controller' => 'contacts', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'clientmenu',
        ],
				[
          'title' => 'Inventario',
          'url' => ['controller' => 'stock_items', 'action' => 'inventario'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'inventorymenu',
        ],
        [
          'title' => __('Configuration'),
          'url' => ['controller' => 'users', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'configurationmenu',
        ],
				[
          'title' => __('Employees'),
          'url' => ['controller' => 'employees', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'employeemenu',
        ],
        [
          'title' => 'Días de Vacaciones',
          'url' => ['controller' => 'employeeHolidays', 'action' => 'index'],
					'permissions'=>[ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'employeemenu',
        ],
        [
          'title' => __('Tasks'),
          'url' => ['controller' => 'tasks', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'taskmenu',
        ],
				[
          'title' => __('Admin Manual'),
          'url' => ['controller' => 'users', 'action' => 'adminmanual'],
					'target'=>'_blank',
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'adminmanual',
        ],
				[
          'title' => __('Manual de Vendedor'),
          'url' => ['controller' => 'users', 'action' => 'vendormanual'],
					'target'=>'_blank',
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'vendormanual',
        ],
      ],
			'sub-menu-quotations' => [
				[
          'title' => 'Cotizaciones',
          'url' => ['controller' => 'quotations', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'quotations',
				],
				[
          'title' => 'Ordenes de Venta',
          'url' => ['controller' => 'sales_orders', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'salesorders',
        ],
        [
          'title' => 'Facturas',
          'url' => ['controller' => 'invoices', 'action' => 'resumen'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'invoices',
        ],
				[
          'title' => 'Cuentas por Cobrar',
          'url' => ['controller' => 'invoices', 'action' => 'cuentasPorCobrar'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'cuentasporcobrar',
        ],
        [
          'title' => 'Transcurso Facturas',
          'url' => ['controller' => 'invoices', 'action' => 'verReporteTranscursoFacturas'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'transcursofacturas',
        ],
				[
          'title' => 'Recibos de Caja',
          'url' => ['controller' => 'invoices', 'action' => 'resumenRecibos'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'resumenrecibos',
        ],
				[
          'title' => 'Metas de Venta',
          'url' => ['controller' => 'sales_objectives', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN],
					'activesetter' => 'salesobjectives',
        ],
				[
          'title' => 'Comisiones por Vendedor',
          'url' => ['controller' => 'invoices', 'action' => 'comisionesPorVendedor'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'comisionesporvendedor',
        ],
				[
          'title' => 'Pagos de Comisiones Pendientes',
          'url' => ['controller' => 'vendor_commission_payments', 'action' => 'resumenPagosPendientes'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'resumenpagospendientes',
        ],
				[
          'title' => 'Pagos de Comisiones',
          'url' => ['controller' => 'vendor_commission_payments', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'vendorcommissionpayments',
        ],
      ],
			'sub-menu-production' => [
				[
          'title' => 'Producción Pendiente',
          'url' => ['controller' => 'salesOrders', 'action' => 'reporteProduccionPendiente'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'reporteproductionpendiente',
				],
        [
          'title' => 'Ordenes de Producción',
          'url' => ['controller' => 'productionOrders', 'action' => 'resumen'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'productionorders',
				],
        [
          'title' => 'Estados de Producción',
          'url' => ['controller' => 'productionOrderStates', 'action' => 'resumen'],
					'permissions'=>[ROLE_ADMIN],
					'activesetter' => 'productionorderstates',
        ],
        [
          'title' => __('Departamentos'),
          'url' => ['controller' => 'departments', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'departments',
        ],
        [
          'title' =>'Asociar Usuarios y Departamentos',
          'url' => ['controller' => 'departmentUsers', 'action' => 'asociarUsuariosDepartamentos'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'departments',
        ],
      /*
        [
          'title' => __('Production Orders'),
          'url' => ['controller' => 'production_orders', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'productionorders',
				],
				[
          'title' => 'Compras Pendientes',
          'url' => ['controller' => 'purchase_orders', 'action' => 'resumenComprasPendientes'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'resumencompraspendientes',
				],
				[
          'title' => __('Purchase Orders'),
          'url' => ['controller' => 'purchase_orders', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN],
					'activesetter' => 'purchaseorders',
				],
				[
          'title' => 'Recepción de Orden de Compra',
          'url' => ['controller' => 'purchase_orders', 'action' => 'recibirOrdenDeCompra'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'recibirordendecompra',
				],
				[
          'title' => 'Producción Pendiente',
          'url' => ['controller' => 'production_order_products', 'action' => 'verProduccionPendiente'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_PRODUCTION],
					'activesetter' => 'verproduccionpendiente',
				],
				[
          'title' => __('Production Processes'),
          'url' => ['controller' => 'production_processes', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_PRODUCTION],
					'activesetter' => 'productionprocesses',
				],
				[
          'title' => 'Reporte de Producción',
          'url' => ['controller' => 'production_process_products', 'action' => 'verReporteProduccion'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_PRODUCTION],
					'activesetter' => 'verreporteproduccion',
				],
      */  
      ],			
			'sub-menu-reports' => [
				[
					'title' => 'Gestión De Ventas',
					'url' => ['controller' => 'quotations', 'action' => 'verReporteGestionDeVentas'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'gestiondeventas',
				],
				[
					'title' => 'Cotizaciones por Categoría y Producto',
					'url' => ['controller' => 'quotations', 'action' => 'verReporteCotizacionesPorCategoria'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'quotationspercategory',
				],
				[
					'title' => 'Cotizaciones por Cliente',
					'url' => ['controller' => 'quotations', 'action' => 'verReporteCotizacionesPorCliente'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'quotationsperclient',
				],
				[
					'title' => 'Cotizaciones por Ejecutivo',
					'url' => ['controller' => 'quotations', 'action' => 'verReporteCotizacionesPorEjecutivo'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'quotationsperexecutive',
				],
				[
          'title' => 'Ordenes de Venta por Estado',
          'url' => ['controller' => 'sales_orders', 'action' => 'verReporteOrdenesDeVentaPorEstado'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'salesordersperstatus',
        ],
				[
          'title' => 'Facturas por Ejecutivo',
          'url' => ['controller' => 'invoices', 'action' => 'verReporteFacturasPorEjecutivo'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'invoicesperexecutive',
        ],
        [
          'title' => 'Ventas por Cliente (Cierre)',
          'url' => ['controller' => 'invoices', 'action' => 'reporteVentasPorCliente'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'ventasporcliente',
        ],
        [
          'title' => 'Ventas Anuales por Cliente',
          'url' => ['controller' => 'invoices', 'action' => 'reporteVentasAnualesPorCliente'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'ventasanualesporcliente',
        ],
      ],			
			'sub-menu-products' => [
        [
          'title' => 'Productos',
          'url' => ['controller' => 'products', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'products',
        ],
        [
          'title' => 'Categorías de Producto',
          'url' => ['controller' => 'product_categories', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'productcategories',
        ],
				[
          'title' => 'Proveedores',
          'url' => ['controller' => 'providers', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'providers',
        ],
      ],
			'sub-menu-clients' => [
        [
          'title' => 'Clientes',
          'url' => ['controller' => 'clients', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'clients',
        ],
				[
          'title' => 'Contactos',
          'url' => ['controller' => 'contacts', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'contacts',
        ],
				[
          'title' => 'Objetivos para Clientes VIP',
          'url' => ['controller' => 'vip_client_objectives', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'vipclientobjectives',
        ],
				[
          'title' => 'Asociar Clientes y Usuarios',
          'url' => ['controller' => 'clients', 'action' => 'asociarClientesUsuarios'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'asociarclientesusuarios',
        ],
				[
          'title' => 'Reasignar Clientes',
          'url' => ['controller' => 'clients', 'action' => 'reasignarClientes'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'reasignarclientes',
        ],
				[
          'title' => 'Correos',
          'url' => ['controller' => 'system_emails', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES,ROLE_SALES_EXECUTIVE],
					'activesetter' => 'systememails',
        ],
			],
			'sub-menu-inventory' => array(
				array(
          'title' => __('Entradas'),
          'url' => array('controller' => 'entries', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'entries',
        ),
				array(
          'title' => __('Inventario'),
          'url' => array('controller' => 'stock_items', 'action' => 'inventario'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES),
					'activesetter' => 'inventory',
        ),
				array(
          'title' => __('Salidas'),
          'url' => array('controller' => 'remissions', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'remissions',
        ),
        array(
          'title' => __('Productos de Inventario'),
          'url' => array('controller' => 'inventory_products', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'inventoryproducts',
        ),
        array(
          'title' => __('Líneas de Producto'),
          'url' => array('controller' => 'inventory_product_lines', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'inventoryproductlines',
        ),
				array(
          'title' => __('Proveedores de Inventario'),
          'url' => array('controller' => 'inventory_providers', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'inventoryproviders',
        ),
				/*
				array(
                    'title' => __('Clientes de Inventario'),
                    'url' => array('controller' => 'inventory_clients', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'inventoryclients',
                ),
				*/
				/*
				array(
                    'title' => __('Contactos de Inventario'),
                    'url' => array('controller' => 'inventory_contacts', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'inventorycontacts',
                ),
				*/
            ),			
			'sub-menu-configuration' => [
        [
          'title' => __('Usuarios'),
          'url' => ['controller' => 'users', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'users',
        ],
        [
          'title' => 'Logs de Usuarios',
          'url' => ['controller' => 'userLogs', 'action' => 'resumen'],
					'permissions'=>[ROLE_ADMIN],
					'activesetter' => 'userlogs',
        ],
				[
          'title' => __('Papeles'),
          'url' => ['controller' => 'roles', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN],
					'activesetter' => 'roles',
        ],
				[
          'title' => 'Permisos',
          'url' => array('controller' => 'users', 'action' => 'rolePermissions'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'rolepermissions',
        ],
        [
          'title' => 'Permisos de Producción',
          'url' => array('controller' => 'users', 'action' => 'roleProductionPermissions'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'roleproductionpermissions',
        ],
        [
          'title' => 'Permisos de Config',
          'url' => array('controller' => 'users', 'action' => 'roleConfigPermissions'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'roleconfigpermissions',
        ],
        [
          'title' => 'Derechos Individuales',
          'url' => array('controller' => 'pageRights', 'action' => 'resumen'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'pagerights',
        ],
        [
          'title' => __('Asignar Derechos'),
          'url' => ['controller' => 'userPageRights', 'action' => 'resumen'],
					'permissions'=>[ROLE_ADMIN],
					'activesetter' => 'userpagerights',
        ],
				[
          'title' => __('Companies'),
          'url' => array('controller' => 'companies', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'companies',
        ],
				[
          'title' => __('Tasas de Cambio'),
          'url' => array('controller' => 'exchange_rates', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'exchangerates',
        ],
				[
          'title' => __('Razones de Caída'),
          'url' => array('controller' => 'rejected_reasons', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'rejectedreasons',
				],
				[
          'title' => __('Action Types'),
          'url' => array('controller' => 'action_types', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'actiontypes',
				],
				[
					'title' => __('Operation Locations'),
					'url' => array('controller' => 'operation_locations', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'operationlocations',
				],
				[
					'title' => __('Payment Modes'),
					'url' => array('controller' => 'payment_modes', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'paymentmodes',
				],
				[
					'title' => __('Machines'),
					'url' => array('controller' => 'machines', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'machines',
				],
      ],
			'sub-menu-employees' => [
        [
          'title' => 'Empleados',
          'url' => ['controller' => 'employees', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'employees',
        ],
				[
          'title' => 'Días de vacaciones',
          'url' => ['controller' => 'employeeHolidays', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT,ROLE_DEPARTMENT_SUPERVISOR_SALES],
					'activesetter' => 'employeeholidays',
        ],
				[
          'title' => 'Motivos de vacaciones',
          'url' => ['controller' => 'holiday_types', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'holidaytypes',
        ],
			],
      'sub-menu-tasks' => [
				[
          'title' => __('Tasks'),
          'url' => ['controller' => 'tasks', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'tasks',
				],
				[
          'title' => __('Task Types'),
          'url' => ['controller' => 'task_types', 'action' => 'index'],
					'permissions'=>[ROLE_ADMIN,ROLE_ASSISTANT],
					'activesetter' => 'tasktypes',
        ],
      ],
    );
		$currentController= $this->params['controller'];
		$currentAction= $this->params['action'];
		$currentParameter=0;
		$this->set(compact('currentController','currentAction','currentParameter'));
		if (!empty($this->params['pass'])){
		
			$currentParameter=$this->params['pass']['0'];
		}
		
		//pr($this->params);
		//echo "controller is ".$currentController."<br/>";
		//echo "action is ".$currentAction."<br/>";
		//echo "parameter is ".$currentParameter."<br/>";
		
		$sub="NA";
		$activeMenu="NA";
		$activeSub="NA";
		$activeSecond="NA";
		if (($currentAction=="index"||$currentAction=="view"||$currentAction=="add"||$currentAction=="edit")){
			switch($currentController){
				case "quotations": 
					$activeMenu="quotationmenu";
					$activeSub="quotations";
					$sub="sub-menu-quotations";
					break;
				case "sales_objectives": 
					$activeMenu="quotationmenu";
					$activeSub="salesobjectives";
					$sub="sub-menu-quotations";
					break;
				case "sales_orders": 
					$activeMenu="quotationmenu";
					$activeSub="salesorders";
					$sub="sub-menu-quotations";
					break;
        case "salesOrders": 
					$activeMenu="quotationmenu";
					$activeSub="salesorders";
					$sub="sub-menu-quotations";
					break;  
				case "invoices": 
					$activeMenu="quotationmenu";
					$activeSub="invoices";
					$sub="sub-menu-quotations";
					break;
				case "purchase_orders":
					$activeMenu="productionmenu";
					$activeSub="purchaseorders";
					$sub="sub-menu-production";
					break;
				case "production_processes":
					$activeMenu="productionmenu";
					$activeSub="productionprocesses";
					$sub="sub-menu-production";
					break;
				case "vendor_commission_payments": 
					$activeMenu="quotationmenu";
					$activeSub="vendorcommissionpayments";
					$sub="sub-menu-quotations";
					break;
				case "entries": 
					$activeMenu="inventorymenu";
					$activeSub="entries";
					$sub="sub-menu-inventory";
					break;
				case "remissions": 
					$activeMenu="inventorymenu";
					$activeSub="remissions";
					$sub="sub-menu-inventory";
					break;
				case "inventory_product_lines": 
					$activeMenu="inventorymenu";
					$activeSub="inventoryproductlines";
					$sub="sub-menu-inventory";
					break;
				case "departments": 
					$activeMenu="productionmenu";
					$activeSub="departments";
					$sub="sub-menu-production";
					break;
				case "inventory_products": 
					$activeMenu="inventorymenu";
					$activeSub="inventoryproducts";
					$sub="sub-menu-inventory";
					break;
				case "inventory_providers": 
					$activeMenu="inventorymenu";
					$activeSub="inventoryproviders";
					$sub="sub-menu-inventory";
					break;
				case "inventory_clients": 
					$activeMenu="inventorymenu";
					$activeSub="inventoryclients";
					$sub="sub-menu-inventory";
					break;
				case "inventory_contacts": 
					$activeMenu="inventorymenu";
					$activeSub="inventorycontacts";
					$sub="sub-menu-inventory";
					break;
					
				case "products": 
					$activeMenu="productmenu";
					$activeSub="products";
					$sub="sub-menu-products";
					break;	
				case "product_categories": 
					$activeMenu="productmenu";
					$activeSub="productcategories";
					$sub="sub-menu-products";
					break;	
				case "departments": 
					$activeMenu="productmenu";
					$activeSub="departments";
					$sub="sub-menu-production";
					break;
				case "providers": 
					$activeMenu="productmenu";
					$activeSub="providers";
					$sub="sub-menu-products";
					break;
				
				case "clients": 
					$activeMenu="clientmenu";
					$activeSub="clients";
					$sub="sub-menu-clients";
					break;
				case "contacts": 
					$activeMenu="clientmenu";
					$activeSub="contacts";
					$sub="sub-menu-clients";
					break;
				case "system_emails": 
					$activeMenu="clientmenu";
					$activeSub="systememails";
					$sub="sub-menu-clients";
					break;
				case "vip_client_objectives": 
					$activeMenu="clientmenu";
					$activeSub="vipclientobjectives";
					$sub="sub-menu-clients";
					break;
				case "users": 
					$activeMenu="configurationmenu";
					$activeSub="users";
					$sub="sub-menu-configuration";
					break;
				case "roles": 
					$activeMenu="configurationmenu";
					$activeSub="roles";
					$sub="sub-menu-configuration";
					break;
				case "companies": 
					$activeMenu="configurationmenu";
					$activeSub="companies";
					$sub="sub-menu-configuration";
					break;
					
				case "exchange_rates": 
					$activeMenu="configurationmenu";
					$activeSub="exchangerates";
					$sub="sub-menu-configuration";
					break;
				case "rejected_reasons": 
					$activeMenu="configurationmenu";
					$activeSub="rejectedreasons";
					$sub="sub-menu-configuration";
					break;
				case "action_types": 
					$activeMenu="configurationmenu";
					$activeSub="actiontypes";
					$sub="sub-menu-configuration";
					break;
				case "operation_locations":
					$activeMenu="configurationmenu";
					$activeSub="operationlocations";
					$sub="sub-menu-configuration";
					break;
				case "payment_modes":
					$activeMenu="configurationmenu";
					$activeSub="paymentmodes";
					$sub="sub-menu-configuration";
					break;
				case "machines":
					$activeMenu="configurationmenu";
					$activeSub="machines";
					$sub="sub-menu-configuration";
					break;
					
				case "employees": 
					$activeMenu="employeemenu";
					$activeSub="employees";
					$sub="sub-menu-employees";
					break;
				case "employeeHolidays": 
					$activeMenu="employeemenu";
					$activeSub="employeeholidays";
					$sub="sub-menu-employees";
					break;
				case "holiday_types": 
					$activeMenu="employeemenu";
					$activeSub="holidaytypes";
					$sub="sub-menu-employees";
					break;
        case "tasks": 
					$activeMenu="taskmenu";
					$activeSub="tasks";
					$sub="sub-menu-tasks";
					break;
        case "task_types": 
					$activeMenu="taskmenu";
					$activeSub="tasktypes";
					$sub="sub-menu-tasks";
					break;
			}
		}
    if (($currentAction=="resumen" || $currentAction=="detalle" || $currentAction=="crear" || $currentAction=="editar")){
			switch($currentController){
        case "invoices": 
					$activeMenu="quotationmenu";
					$activeSub="invoices";
					$sub="sub-menu-quotations";
					break;
        case "pageRights":
          $activeMenu="configuration";
          $activeSub="pagerights";
          $sub="sub-menu-configuration";
		      break;
        case "productionOrders":
					$activeMenu="productionmenu";
					$activeSub="productionorders";
					$sub="sub-menu-production";
					break;  
        case "productionOrderStates":
          $activeMenu="configuration";
          $activeSub="productionorderstates";
          $sub="sub-menu-production";
		      break;  
        case "userPageRights":
          $activeMenu="configuration";
          $activeSub="userpagerights";
          $sub="sub-menu-configuration";
		      break;
        case "userLogs": 
					$activeMenu="configurationmenu";
					$activeSub="userlogs";
					$sub="sub-menu-configuration";
					break;
      }
    }    
		
		else if ($currentAction=="cuentasPorCobrar" && $currentController=="invoices"){
			$activeMenu="quotationmenu";
			$activeSub="cuentasporcobrar";
			$sub="sub-menu-quotations";
		}
    else if ($currentAction=="editarReferencia" && $currentController=="invoices"){
			$activeMenu="quotationmenu";
			$activeSub="invoices";
			$sub="sub-menu-quotations";
		}
    else if ($currentAction=="verReporteTranscursoFacturas" && $currentController=="invoices"){
			$activeMenu="quotationmenu";
			$activeSub="transcursofacturas";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="resumenRecibos" && $currentController=="invoices"){
			$activeMenu="quotationmenu";
			$activeSub="resumenrecibos";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="comisionesPorVendedor" && $currentController=="invoices"){
			$activeMenu="quotationmenu";
			$activeSub="comisionesporvendedor";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="resumenPagosPendientes" && $currentController=="vendor_commission_payments"){
			$activeMenu="quotationmenu";
			$activeSub="resumenpagospendientes";
			$sub="sub-menu-quotations";
		}
		
    else if ($currentAction=="reporteProduccionPendiente" && $currentController=="salesOrders"){
			$activeMenu="productionmenu";
			$activeSub="reporteproduccionpendiente";
			$sub="sub-menu-production";
		}
		else if ($currentAction=="resumenComprasPendientes" && $currentController=="purchase_orders"){
			$activeMenu="productionmenu";
			$activeSub="resumencompraspendientes";
			$sub="sub-menu-production";
		}
		else if ($currentAction=="recibirOrdenDeCompra" && $currentController=="purchase_orders"){
			$activeMenu="productionmenu";
			$activeSub="recibirordendecompra";
			$sub="sub-menu-production";
		}
		else if ($currentAction=="verProduccionPendiente" && $currentController=="production_order_products"){
			$activeMenu="productionmenu";
			$activeSub="verproduccionpendiente";
			$sub="sub-menu-production";
		}
		else if ($currentAction=="verReporteProduccion" && $currentController=="production_process_products"){
			$activeMenu="productionmenu";
			$activeSub="verreporteproduccion";
			$sub="sub-menu-production";
		}
		
		else if ($currentAction=="verReporteGestionDeVentas" && $currentController=="quotations"){
			$activeMenu="reportmenu";
			$activeSub="gestiondeventas";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteCotizacionesPorCategoria" && $currentController=="quotations"){
			$activeMenu="reportmenu";
			$activeSub="quotationspercategory";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteCotizacionesPorEjecutivo" && $currentController=="quotations"){
			$activeMenu="reportmenu";
			$activeSub="quotationsperexecutive";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteCotizacionesPorDepartamento" && $currentController=="quotations"){
			$activeMenu="reportmenu";
			$activeSub="quotationspercategory";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteCotizacionesPorProducto" && $currentController=="quotations"){
			$activeMenu="reportmenu";
			$activeSub="quotationsperproduct";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteCotizacionesPorCliente" && $currentController=="quotations"){
			$activeMenu="reportmenu";
			$activeSub="quotationsperclient";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteOrdenesDeVentaPorEstado" && $currentController=="sales_orders"){
			$activeMenu="reportmenu";
			$activeSub="salesordersperstatus";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="verReporteFacturasPorEjecutivo" && $currentController=="invoices"){
			$activeMenu="reportmenu";
			$activeSub="invoicesperexecutive";
			$sub="sub-menu-reports";
		}
    else if ($currentAction=="reporteVentasPorCliente" && $currentController=="invoices"){
			$activeMenu="reportmenu";
			$activeSub="ventasporcliente";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="reporteVentasAnualesPorCliente" && $currentController=="invoices"){
			$activeMenu="reportmenu";
			$activeSub="ventasanualesporcliente";
			$sub="sub-menu-reports";
		}
		else if ($currentAction=="asociarClientesUsuarios" && $currentController=="clients"){		
			$activeMenu="clientmenu";
			$activeSub="asociarclientesusuarios";
			$sub="sub-menu-clients";
		}
		else if ($currentAction=="reasignarClientes" && $currentController=="clients"){		
			$activeMenu="clientmenu";
			$activeSub="reasignarclientes";
			$sub="sub-menu-clients";
		}
		
		else if ($currentAction=="inventario" && $currentController=="stock_items"){		
			$activeMenu="inventorymenu";
			$activeSub="inventory";
			$sub="sub-menu-inventory";
		}
		
		else if ($currentAction=="rolePermissions" && $currentController=="users"){		
			$activeMenu="configurationmenu";
			$activeSub="rolepermissions";
			$sub="sub-menu-configuration";
		}
    else if ($currentAction=="roleProductionPermissions" && $currentController=="users"){		
			$activeMenu="configurationmenu";
			$activeSub="roleproductionpermissions";
			$sub="sub-menu-configuration";
		}
    else if ($currentAction=="roleConfigPermissions" && $currentController=="users"){		
			$activeMenu="configurationmenu";
			$activeSub="roleconfigpermissions";
			$sub="sub-menu-configuration";
		}
		
		
		$active=[];
		$active['activeMenu']=$activeMenu;
		$active['activeSub']=$activeSub;
		$active['activeSecond']=$activeSecond;
		//pr($sub);
		//pr($active);
    // For default settings name must be menu
    $this->set(compact('menu','active','sub'));
		
		$modificationInfo=NA;
		
		if($currentAction=="edit"||$currentAction=="view"||$currentAction=="editClient"||$currentAction=="editProvider"||$currentAction=="viewClient"||$currentAction=="viewProvider"||$currentAction=="editSale"||$currentAction=="editPurchase"||$currentAction=="editRemission"||$currentAction=="viewSale"||$currentAction=="viewPurchase"||$currentAction=="viewRemission"){
			$this->loadModel('UserAction');
			$userActions=$this->UserAction->find('all',array(
				'fields'=>array(
					'UserAction.action_name','UserAction.action_datetime',
					'UserAction.user_id','User.username',
				),
				'conditions'=>array(
					'UserAction.controller_name'=>$currentController,
					'UserAction.item_id'=>$currentParameter,
				),
				'order'=>'action_datetime DESC',
			));
			//pr($userActions);
			if (!empty($userActions)){
				
				$lastAction="";
				if ($userActions[0]['UserAction']['action_name']=="add"){
					$lastAction="Grabado por ";
				}
				elseif ($userActions[0]['UserAction']['action_name']=="edit"){
					$lastAction="Modificado por ";
				}
				
				$lastAction.=$userActions[0]['User']['username']." ";
				
				$actionDateTime=new DateTime($userActions[0]['UserAction']['action_datetime']);
				$lastAction.=$actionDateTime->format('d-m-Y H:i:s');
				$modificationInfo="";
				//$modificationInfo="<ul class='nav pull-right' style='position:absolute;right:300px;top:30px;'>";
				//$modificationInfo.="<div class='btn-group'>";
				//	$modificationInfo.="<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'> Action<span class='caret'></span></a>";
				
				//$modificationInfo.="<ul class='nav pull-right'>";
				$modificationInfo.="<ul class='nav'>";
					$modificationInfo.="<li class='dropdown'>";
						$modificationInfo.="<a class='dropdown-toggle' data-toggle='dropdown' href='#'>";
							$modificationInfo.=$lastAction;
							$modificationInfo.="<i class='icon-angle-down'></i>";
						$modificationInfo.="</a>";
						
						if (count($userActions)>1){
							
							$modificationInfo.="<ul class='dropdown-menu'>";
							for ($i=1;$i<count($userActions);$i++){
								$actionInfo="";
								if ($userActions[$i]['UserAction']['action_name']=="add"){
									$actionInfo="Grabado por ";
								}
								elseif ($userActions[$i]['UserAction']['action_name']=="edit"){
									$actionInfo="Modificado por ";
								}
								$actionInfo.=$userActions[$i]['User']['username']." ";
								$actionDateTime=new DateTime($userActions[$i]['UserAction']['action_datetime']);
								$actionInfo.=$actionDateTime->format('d-m-Y H:i:s');
							
							
								$modificationInfo.="<li>";
									$modificationInfo.="<i class='icon-key'></i>";
									$modificationInfo.=$actionInfo;
								$modificationInfo.="</li>";
							}	
							$modificationInfo.="</ul>";
						}
						
						
					$modificationInfo.="</li>";
				$modificationInfo.="</ul>";			
				//$modificationInfo.="</div>";
			}
		}
		$this->set(compact('modificationInfo'));
		
		if (!(($currentController=='pages')&&($currentAction=='display'))){
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/add";		
			$userId=$this->Auth->User('id');
			//pr($userid);
			if (!empty($userid)){
				$bool_add_permission=$this->hasPermission($this->Auth->User('id'),$aco_name);
			}
			else {
				$bool_add_permission=false;
			}
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/crear";		
			$userid=$this->Auth->User('id');
			//pr($userid);
			if (!empty($userid)){
				$bool_crear_permission=$this->hasPermission($this->Auth->User('id'),$aco_name);
			}
			else {
				$bool_crear_permission=false;
			}
			$bool_add_permission=$bool_add_permission || $bool_crear_permission;
      $this->set(compact('bool_add_permission'));
			
			
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/edit";		
			//pr($userid);
			if (!empty($userid)){
				$bool_edit_permission=$this->hasPermission($this->Auth->User('id'),$aco_name);
			}
			else {
				$bool_edit_permission=false;
			}
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/editar";		
			//pr($userid);
			if (!empty($userid)){
				$bool_editar_permission=$this->hasPermission($this->Auth->User('id'),$aco_name);
			}
			else {
				$bool_editar_permission=false;
			}
			$bool_edit_permission=$bool_edit_permission || $bool_editar_permission;
      $this->set(compact('bool_edit_permission'));
			
			
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/delete";		
			if (!empty($userid)){
				$bool_delete_permission=$this->hasPermission($this->Auth->User('id'),$aco_name);
			}
			else {
				$bool_delete_permission=false;
			}
			//echo "bool delete permission is ".$bool_delete_permission."<br/>";
			$this->set(compact('bool_delete_permission'));
			
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/annul";		
			if (!empty($userid)){
				$bool_annul_permission=$this->hasPermission($this->Auth->User('id'),$aco_name);
			}
			else {
				$bool_annul_permission=false;
			}
			//echo "bool annul permission is ".$bool_annul_permission."<br/>";
			$this->set(compact('bool_annul_permission'));
		}
		/*
		$exchangeRateUpdateNeeded=false;
		$this->loadModel('ExchangeRate');
		$exchangeRateDuration=$this->ExchangeRate->getLatestExchangeRateDuration();
		//echo "exchange rate duration is ".$exchangeRateDuration."<br/>";
		if ($exchangeRateDuration>31){
			$exchangeRateUpdateNeeded=true;
		}
		if($exchangeRateUpdateNeeded){
			echo "<script>alert('Se venció la tasa de cambio, por favor introduzca la nueva tasa de cambio!');</script>";
		}
		*/
	}

	public function hasPermission($user_id,$aco_name){
		$this->loadModel('User');
		$user=$this->User->read(null,$user_id);
		//pr($user);
		//pr($aco_name);
		if (!empty($user)){
			return $this->Acl->check(array('Role'=>array('id'=>$user['User']['role_id'])),$aco_name);
		}
		else {
			return false;
		}
	}
	
	public function userhome($userrole){
		switch ($userrole){
			case ROLE_ADMIN:
			case ROLE_SALES_EXECUTIVE:
      case ROLE_DEPARTMENT_SUPERVISOR_SALES:
				return [
					'controller' => 'quotations',
					'action' => 'index'
				];
				break;
			case ROLE_ASSISTANT:
				$_SESSION['userId']=0;
				$_SESSION['invoiceDisplay']=2;
				return [
					'controller' => 'sales_orders',
					'action' => 'index'
				];
				break;
			default:
				//echo "redirecting to loginpage!<br/>";
				return [
				  'controller' => 'users',
				  'action' => 'login'
				];
				break;
		}
	}

	public function get_date($month, $year, $week, $day, $direction) {
		if($direction > 0)
			$startday = 1;
		else
			// t gives number of days in given month, from 28 to 31
			// mktime(hour, minute, second, month, day, year, daylightsavingtime)
			$startday = date('t', mktime(0, 0, 0, $month, 1, $year));

		$start = mktime(0, 0, 0, $month, $startday, $year);
		// N gives numberic representation of weekday 1 (for Monday) through 7 (for Sunday)
		$weekday = date('N', $start);

		if($direction * $day >= $direction * $weekday)
			$offset = -$direction * 7;
		else
			$offset = 0;

		$offset += $direction * ($week * 7) + ($day - $weekday);
		return mktime(0, 0, 0, $month, $startday + $offset, $year);
	}
/*	
	public function saveAccountingRegisterData($AccountingRegisterDataArray,$bool_new){
		$this->loadModel('AccountingRegister');
		$this->loadModel('AccountingCode');
		$this->loadModel('Currency');
		
		try {
			//pr($AccountingRegisterDataArray);
			$datasource=$this->AccountingRegister->getDataSource();
			$datasource->begin();
			if ($bool_new){
				$this->AccountingRegister->create();
			}
			
			if (!$this->AccountingRegister->save($AccountingRegisterDataArray)) {
				pr($this->validateErrors($this->AccountingRegister));
				echo "Error al guardar el asiento contable";
				throw new Exception();
			}
			
			$accounting_register_id=$this->AccountingRegister->id;
			$accounting_register_accounting_register_type_id=$AccountingRegisterDataArray['AccountingRegister']['accounting_register_type_id'];
			$accounting_register_register_code=$AccountingRegisterDataArray['AccountingRegister']['register_code'];
			$accounting_register_concept=$AccountingRegisterDataArray['AccountingRegister']['concept'];
			$accounting_register_date=$AccountingRegisterDataArray['AccountingRegister']['register_date'];
			$accounting_register_currency_id=$AccountingRegisterDataArray['AccountingRegister']['currency_id'];
			//$linkedCurrency=$this->Currency->read(null,$accounting_register_currency_id);
			//$currency_abbreviation=$linkedCurrency['Currency']['abbreviation'];
			$currency_abbreviation="C$";
			foreach ($AccountingRegisterDataArray['AccountingMovement'] as $accountingMovement){
				//pr($accountingMovement);
				$accounting_movement_amount=0;
				$bool_debit=true;
				
				if (!empty($accountingMovement['debit_amount'])){
					$accounting_movement_amount = round($accountingMovement['debit_amount'],2);
					$bool_debit=true;
				}
				else if (!empty($accountingMovement['credit_amount'])){
					$accounting_movement_amount = round($accountingMovement['credit_amount'],2);
					$bool_debit=false;
				}
				
				$accounting_movement_code_id = $accountingMovement['accounting_code_id'];
				$accounting_movement_concept = $accountingMovement['concept'];
				
				//echo "just before the saving part of accountingmovements.<br/>";
				//echo "accounting movement code id".$accounting_movement_code_id."<br/>";
				//echo "accounting movement amount".$accounting_movement_amount."<br/>";
				if ($accounting_movement_code_id>0 && $accounting_movement_amount>0){
					$accountingCode=$this->AccountingCode->read(null,$accounting_movement_code_id);
					$accounting_movement_code_description = $accountingCode['AccountingCode']['description'];
					
					$logmessage="Registro de cuenta contable ".$accounting_movement_code_description." (Monto:".$accounting_movement_amount." ".$currency_abbreviation.") para Registro Contable ".$accounting_register_concept;
					//echo $logmessage."<br/>";
					// SAVE ACCOUNTING MOVEMENT
					$AccountingMovementItemData['accounting_register_id']=$accounting_register_id;
					$AccountingMovementItemData['accounting_code_id']=$accounting_movement_code_id;
					$AccountingMovementItemData['concept']=$accounting_movement_concept;
					
					
					$AccountingMovementItemData['amount']=$accounting_movement_amount;
					$AccountingMovementItemData['currency_id']=$accounting_register_currency_id;
					
					$AccountingMovementItemData['bool_debit']=$bool_debit;
					//echo "saved item data";
					//pr($AccountingMovementItemData);
					$this->AccountingRegister->AccountingMovement->create();
					if (!$this->AccountingRegister->AccountingMovement->save($AccountingMovementItemData)) {
						pr($this->validateErrors($this->AccountingMovement));
						echo "problema al guardar el movimiento contable";
						throw new Exception();
					}
					
					// SAVE THE USERLOG FOR ACCOUNTING MOVEMENT
					$this->recordUserActivity($this->Session->read('User.username'),$logmessage);
				}
			}			
			$datasource->commit();
			$this->Session->setFlash(__('Se guardó el comprobante.'),'default',array('class' => 'success'));
			return $accounting_register_id;
			
		}
		catch(Exception $e){
			$datasource->rollback();
			$this->Session->setFlash(__('No se podía guardar el comprobante. Por favor intente de nuevo.'),'default',array('class' => 'error-message'));
			return false;
		}
	}
*/	

	/**
	 * Function: sanitize
	 * Returns a sanitized string, typically for URLs.
	 *
	 * Parameters:
	 *     $string - The string to sanitize.
	 *     $force_lowercase - Force the string to lowercase?
	 *     $anal - If set to *true*, will remove all non-alphanumeric characters.
	 */
	 /*
	function sanitize($string, $force_lowercase = true, $anal = false) {
		
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
					   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
					   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
		
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace('/\s+/', "-", $clean);
		$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
		return ($force_lowercase) ?
			(function_exists('mb_strtolower')) ?
				mb_strtolower($clean, 'UTF-8') :
				strtolower($clean) :
			$clean;
		
		//return transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove',"A æ Übérmensch på høyeste nivå! И я люблю PHP! есть. ﬁ ¦")
	}
	*/
	public static function normalizeChars($s) {
		$replace = array(
			'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',
			'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',
			'Þ'=>'B',
			'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',
			'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',
			'Ğ'=>'G',
			'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',
			'Ł'=>'L',
			'Ñ'=>'N', 'Ń'=>'N',
			'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',
			'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',
			'Ț'=>'T',
			'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',
			'Ý'=>'Y',
			'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',
			'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',
			'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',
			'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',
			'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',
			'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',
			'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',
			'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',
			'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',
			'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',
			'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',
			'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',
			'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',
			'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',
			'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',
			'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',
			'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',
			'ק'=>'q',
			'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',
			'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',
			'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',
			'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',
			'в'=>'v', 'ו'=>'v', 'В'=>'v',
			'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',
			'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',
			'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
		);
		return strtr($s, $replace);
	}
	
	function uploadFiles($folder, $formdata, $itemId = null,$boolCheckPermitted=true) {	
		// setup dir names absolute and relative	
		$folder_url = WWW_ROOT.$folder;	
		$rel_url = $folder;		
		// create the folder if it does not exist	
		if(!is_dir($folder_url)) {		
			//$folder=new Folder();
			//$folder->create($folder_url);
			mkdir($folder_url,0777, true);	
		}			
		// if itemId is set create an item folder	
		if($itemId) {
			// set new absolute folder		
			$folder_url = WWW_ROOT.$folder.'/'.$itemId; 		
			// set new relative folder		
			$rel_url = $folder.'/'.$itemId;		
			// create directory		
			if(!is_dir($folder_url)) {
				//$folder=new Folder();
				//$folder->create($folder_url);
				mkdir($folder_url,0777, true);		
			}	
		}		
		// list of permitted file types, this is only images but documents can be added	
		
		$permitted = array('image/jpg','image/jpeg','image/png','application/pdf');		
		// loop through and deal with the files	
		foreach($formdata as $file) {		
			//pr($file);
			// replace spaces with underscores		
			$filename =$this->normalizeChars($file['name']);
			$filename = str_replace(' ', '_', $filename);
			$filename = str_replace('\'', '_', $filename);
			// assume filetype is false		
			$typeOK = false;		
			// check filetype is ok		
			if ($boolCheckPermitted){
				foreach($permitted as $type) {			
					if($type == $file['type']) {				
						$typeOK = true;				
						break;		
					}		
				}				
			}
			else {
				$typeOK = true;		
			}
			// if file type ok upload the file		
			if($typeOK) {			
				// switch based on error code			
				switch($file['error']) {				
					case 0:					
						// check filename already exists					
						if(!file_exists($folder_url.'/'.$filename)) {						
							// create full filename						
							$full_url = $folder_url.'/'.$filename;						
							$url = $rel_url.'/'.$filename;						
							// upload the file						
							$success = move_uploaded_file($file['tmp_name'], $url);					
						} 
						else {						
							// create unique filename and upload file						
							ini_set('date.timezone', 'Europe/London');						
							$now = date('Y-m-d-His');						
							$full_url = $folder_url.'/'.$now.$filename;						
							$url = $rel_url.'/'.$now.$filename;						
							$success = move_uploaded_file($file['tmp_name'], $url);					
						}					
						// if upload was successful					
						if($success) {						
							// save the url of the file						
							$result['urls'][] = $url;					
						} 
						else {						
							$result['errors'][] = "Error al cargar $filename.";					
						}					
						break;				
					case 3:					
						// an error occured					
						$result['errors'][] = "Error uploading $filename. Please try again.";					
						break;				
					default:					
						// an error occured					
						$result['errors'][] = "System error uploading $filename. Contact webmaster.";					
						break;			
					}		
			} 
			elseif($file['error'] == 4) {			
				// no file was selected for upload			
				$result['nofiles'][] = "No file Selected";		
			} 
			else {			
				// unacceptable file type			
				$result['errors'][] = "$filename cannot be uploaded. Acceptable file types: jpg, jpeg, png, pdf.";		
			}	
		}
		return $result;
	}
	
	public function recreateStockItemLogs($id = null) {
		$this->loadModel('StockItem');
		$this->loadModel('StockItemLog');
		$this->loadModel('StockMovement');
		$this->StockItem->id = $id;
		if (!$this->StockItem->exists()) {
			throw new NotFoundException(__('Invalid stock item'));
		}
		
		$stockItem=$this->StockItem->find('first',array(
			'conditions'=>array('StockItem.id'=>$id),
			'contain'=>array(
				'StockItemLog',
				'InventoryProduct'=>array(
					'InventoryProductLine',
				),
			),
		));
		//pr($stockItem);
		$datasource=$this->StockItem->getDataSource();
		try{
			$datasource->begin();
			foreach ($stockItem['StockItemLog'] as $stockItemLog){
				$this->StockItemLog->id=$stockItemLog['id'];
				if (!$this->StockItemLog->delete()) {
					echo "problema eliminando los estados de lote";
					pr($this->validateErrors($this->StockItemLog));
					throw new Exception();
				}
			}
			$datasource->commit();
		}
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			return false;
		}
		// recreate all stockitemlogs
		
		
		$creationmovement=[];
		$movements=[];
		
		$creationmovement=$this->StockMovement->find('first',array(
			'conditions'=>array(
				'StockMovement.stock_item_id'=>$id,
				'bool_input'=>true,
			),
		));
		$movements=$this->StockMovement->find('all',array(
			'conditions'=>array(
				'StockMovement.stock_item_id'=>$id,
				'bool_input'=>false,
			),
			'order'=>'movement_date, StockMovement.id',
		));
			
		
		try {
			$datasource->begin();

			$stockItemLogData=[];
			$stockItemLogData['stock_item_id']=$id;
			$stockItemLogData['stock_item_date']=$creationmovement['StockMovement']['movement_date'];
			$stockItemLogData['inventory_product_id']=$creationmovement['StockMovement']['inventory_product_id'];
			$stockItemLogData['product_quantity']=$creationmovement['StockMovement']['product_quantity'];
			$stockItemLogData['measuring_unit_id']=$creationmovement['StockMovement']['measuring_unit_id'];
			$stockItemLogData['product_unit_cost']=$creationmovement['StockMovement']['product_unit_cost'];
			$stockItemLogData['currency_id']=$creationmovement['StockMovement']['currency_id'];
			$stockItemLogData['stock_movement_id']=$creationmovement['StockMovement']['id'];
			
			$this->StockItemLog->create();
			if (!$this->StockItemLog->save($stockItemLogData)) {
				echo "problema guardando los estado de lote";
				pr($this->validateErrors($this->StockItemLog));
				throw new Exception();
			}
				
			$remainingQuantityStockItem=$stockItem['StockItem']['product_original_quantity'];		
			
			foreach ($movements as $movement){
				$remainingQuantityStockItem-=$movement['StockMovement']['product_quantity'];
				$this->StockItem->StockItemLog->create();
				$stockItemLogData['stock_item_id']=$id;
				$stockItemLogData['stock_item_date']=$movement['StockMovement']['movement_date'];
				$stockItemLogData['inventory_product_id']=$movement['StockMovement']['inventory_product_id'];
				$stockItemLogData['product_quantity']=$remainingQuantityStockItem;
				$stockItemLogData['measuring_unit_id']=$movement['StockMovement']['measuring_unit_id'];
				$stockItemLogData['product_unit_cost']=$creationmovement['StockMovement']['product_unit_cost'];
				$stockItemLogData['currency_id']=$movement['StockMovement']['currency_id'];
				$stockItemLogData['stock_movement_id']=$movement['StockMovement']['id'];
	
				$this->StockItemLog->create();
				if (!$this->StockItemLog->save($stockItemLogData)) {
					echo "problema guardando los estado de lote";
					pr($this->validateErrors($this->StockItemLog));
					throw new Exception();
				}
			}
			$datasource->commit();
			return true;
		}
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			return false;
		}
	}
	
}
