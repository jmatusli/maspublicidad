<?php
	define('COMPANY_NAME','Mas Publicidad');
	define('COMPANY_URL','www.maspublicidad.com.ni');
	define('COMPANY_MAIL','info@maspublicidad.com.ni');
	define('COMPANY_ADDRESS','De los semáforos de ENEL Central 200 mts al lago');
	define('COMPANY_PHONE','2277-5313');
	define('COMPANY_RUC','J0310000167575');

	define('CURRENCY_CS','1');
	define('CURRENCY_USD','2');

	define('ROLE_ADMIN','5');
	define('ROLE_ASSISTANT','6');
	define('ROLE_SALES_EXECUTIVE','7');
	
	define('NA','N/A');
	
	define('PRODUCT_STATUS_REGISTERED','1');
	define('PRODUCT_STATUS_IN_PRODUCTION','2');
	define('PRODUCT_STATUS_READY_FOR_DELIVERY','3');
	
	/*
	
	define('MOVEMENT_PURCHASE','4');
	define('MOVEMENT_SALE','5');
	
	define('PRODUCT_TYPE_PREFORMA','10');
	define('PRODUCT_TYPE_CAP','9');
	define('PRODUCT_TYPE_BOTTLE','11');
	
	define('CATEGORY_RAW','1');
	define('CATEGORY_PRODUCED','2');
	define('CATEGORY_OTHER','3');
	
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

	public $components = array(	
		'Session',
		//'DebugKit.Toolbar',
		'Acl',
        'Auth' => array(
            'authorize' => array(
                'Actions' => array('actionPath' => 'controllers')
            )
        ),
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
	);
	public $helpers = array( 
		'Html', 
		'Form', 
		'Session',
		'MenuBuilder.MenuBuilder' => array(
			'authVar' => 'user',
			'authModel' => 'User',
			'authField' => 'role_id',
		),
	);
	
	function recordUserActivity($userName,$userEvent){
		$this->request->data['UserLog']['user_id'] = $this->Auth->User('id');;
		$this->request->data['UserLog']['username'] = $userName;
		$this->request->data['UserLog']['event'] = $userEvent;
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
		$userActionData=array();
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
        $this->Auth->loginAction = array(
          'controller' => 'users',
          'action' => 'login'
        );
        $this->Auth->logoutRedirect = array(
          'controller' => 'users',
          'action' => 'login'
        );
		$this->Auth->loginRedirect = array(
		  'controller' => 'quotations',
		  'action' => 'index',
		  'home'
		);
		
		$user = $this->Auth->user();
		$this->set(compact('user'));
		//pr($user);
		
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
		
		// Define your menu for MenuBuilder
		
        $menu = array(
            'main-menu' => array(
				array(
                    'title' => __('Cotizaciones'),
                    'url' => array('controller' => 'quotations', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'quotationmenu',
                ),
				array(
                    'title' => __('Ordenes de Venta'),
                    'url' => array('controller' => 'sales_orders', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'salesordermenu',
                ),
                array(
                    'title' => __('Facturas'),
                    'url' => array('controller' => 'invoices', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'invoicemenu',
                ),
				array(
                    'title' => __('Productos'),
                    'url' => array('controller' => 'products', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'productmenu',
                ),
				array(
                    'title' => __('Clients'),
                    'url' => array('controller' => 'clients', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'clientmenu',
                ),
                array(
                    'title' => __('Configuration'),
                    'url' => array('controller' => 'users', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'configurationmenu',
                ),
				array(
                    'title' => __('Employees'),
                    'url' => array('controller' => 'employees', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'employeemenu',
                ),
            ),
			'sub-menu-quotations' => array(
				array(
                    'title' => __('Cotizaciones'),
                    'url' => array('controller' => 'quotations', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'quotations',
                ),
                array(
                    'title' => __('Por Ejecutivo'),
                    'url' => array('controller' => 'quotations', 'action' => 'verReporteCotizacionesPorEjecutivo'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'quotationsperexecutive',
                ),
                array(
                    'title' => __('Por Categoría y Producto'),
                    'url' => array('controller' => 'quotations', 'action' => 'verReporteCotizacionesPorCategoria'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'quotationspercategory',
                ),
				/*array(
                    'title' => __('Por Producto'),
                    'url' => array('controller' => 'quotations', 'action' => 'verReporteCotizacionesPorProducto'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'quotationsperproduct',
                ),
				*/
				array(
                    'title' => __('Por Cliente'),
                    'url' => array('controller' => 'quotations', 'action' => 'verReporteCotizacionesPorCliente'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'quotationsperclient',
                ),
            ),
			'sub-menu-salesorders' => array(
				array(
                    'title' => __('Ordenes de Venta'),
                    'url' => array('controller' => 'sales_orders', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'salesorders',
                ),
                array(
                    'title' => __('Por Estado'),
                    'url' => array('controller' => 'sales_orders', 'action' => 'verReporteOrdenesDeVentaPorEstado'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'salesordersperstatus',
                ),
            ),
			'sub-menu-invoices' => array(
				array(
                    'title' => __('Facturas'),
                    'url' => array('controller' => 'invoices', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'invoices',
                ),
                array(
                    'title' => __('Por Ejecutivo'),
                    'url' => array('controller' => 'invoices', 'action' => 'verReporteFacturasPorEjecutivo'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT),
					'activesetter' => 'invoicesperexecutive',
                ),
            ),
			'sub-menu-products' => array(
                array(
                    'title' => __('Productos'),
                    'url' => array('controller' => 'products', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'products',
                ),
                array(
                    'title' => __('Categorías de Producto'),
                    'url' => array('controller' => 'product_categories', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'productcategories',
                ),
				array(
                    'title' => __('Proveedores'),
                    'url' => array('controller' => 'providers', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'providers',
                ),
            ),
			'sub-menu-clients' => array(
                array(
                    'title' => __('Clientes'),
                    'url' => array('controller' => 'clients', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'clients',
                ),
				array(
                    'title' => __('Contactos'),
                    'url' => array('controller' => 'contacts', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'contacts',
                ),
			),
			'sub-menu-configuration' => array(
                array(
                    'title' => __('Usuarios'),
                    'url' => array('controller' => 'users', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'users',
                ),
				array(
                    'title' => __('Permisos de Usuarios'),
                    'url' => array('controller' => 'users', 'action' => 'rolePermissions'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'rolepermissions',
                ),
				array(
                    'title' => __('Tasas de Cambio'),
                    'url' => array('controller' => 'exchange_rates', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN),
					'activesetter' => 'exchangerates',
                ),
            ),
			'sub-menu-employees' => array(
                array(
                    'title' => __('Empleados'),
                    'url' => array('controller' => 'employees', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'employees',
                ),
				array(
                    'title' => __('Días de vacaciones'),
                    'url' => array('controller' => 'employee_holidays', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'employeeholidays',
                ),
				array(
                    'title' => __('Motivos de vacaciones'),
                    'url' => array('controller' => 'holiday_types', 'action' => 'index'),
					'permissions'=>array(ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE),
					'activesetter' => 'holidaytypes',
                ),
			),
        );
		$currentController= $this->params['controller'];
		$currentAction= $this->params['action'];
		$currentParameter=0;
		if (!empty($this->params['pass'])){
		
			$currentParameter=$this->params['pass']['0'];
		}
		/*
		//pr($this->params);
		echo "controller is ".$currentController."<br/>";
		echo "action is ".$currentAction."<br/>";
		echo "parameter is ".$currentParameter."<br/>";
		*/
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
				case "sales_orders": 
					$activeMenu="salesordermenu";
					$activeSub="quotations";
					$sub="sub-menu-salesorders";
					break;
				case "invoices": 
					$activeMenu="invoicemenu";
					$activeSub="invoices";
					$sub="sub-menu-invoices";
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
				case "users": 
					$activeMenu="configurationmenu";
					$activeSub="users";
					$sub="sub-menu-configuration";
					break;
				case "exchange_rates": 
					$activeMenu="configurationmenu";
					$activeSub="exchangerates";
					$sub="sub-menu-configuration";
					break;
				case "employees": 
					$activeMenu="employeemenu";
					$activeSub="employees";
					$sub="sub-menu-employees";
					break;
				case "employee_holidays": 
					$activeMenu="employeemenu";
					$activeSub="employeeholidays";
					$sub="sub-menu-employees";
					break;
				case "holiday_types": 
					$activeMenu="employeemenu";
					$activeSub="holidaytypes";
					$sub="sub-menu-employees";
					break;
			}
		}
		
		else if ($currentAction=="verReporteCotizacionesPorEjecutivo" && $currentController=="quotations"){
			$activeMenu="quotationmenu";
			$activeSub="quotationsperexecutive";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="verReporteCotizacionesPorCategoria" && $currentController=="quotations"){
			$activeMenu="quotationmenu";
			$activeSub="quotationspercategory";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="verReporteCotizacionesPorProducto" && $currentController=="quotations"){
			$activeMenu="quotationmenu";
			$activeSub="quotationsperproduct";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="verReporteCotizacionesPorCliente" && $currentController=="quotations"){
			$activeMenu="quotationmenu";
			$activeSub="quotationsperclient";
			$sub="sub-menu-quotations";
		}
		else if ($currentAction=="verReporteOrdenesDeVentaPorEstado" && $currentController=="sales_orders"){
			$activeMenu="salesordermenu";
			$activeSub="salesordersperstatus";
			$sub="sub-menu-salesorders";
		}
		else if ($currentAction=="verReporteFacturasPorEjecutivo" && $currentController=="invoices"){
			$activeMenu="invoicemenu";
			$activeSub="invoicesperexecutive";
			$sub="sub-menu-invoices";
		}
		else if ($currentAction=="rolePermissions" && $currentController=="users"){		
			$activeMenu="configurationmenu";
			$activeSub="rolepermissions";
			$sub="sub-menu-configuration";
		}
		
		$active=array();
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
			//pr($aco_name);
			$userid=$this->Session->read('User.id');
			//pr($userid);
			if (!empty($userid)){
				$bool_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
			}
			else {
				$bool_add_permission=false;
			}
			//echo "bool add permission is ".$bool_add_permission."<br/>";
			$this->set(compact('bool_add_permission'));
			
			
			$userid=$this->Session->read('User.id');
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/edit";		
			//pr($userid);
			if (!empty($userid)){
				$bool_edit_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
			}
			else {
				$bool_edit_permission=false;
			}
			//echo "bool edit permission is ".$bool_edit_permission."<br/>";
			$this->set(compact('bool_edit_permission'));
			
			$aco_name=Inflector::camelize(Inflector::pluralize($currentController))."/delete";		
			if (!empty($userid)){
				$bool_delete_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
			}
			else {
				$bool_delete_permission=false;
			}
			//echo "bool delete permission is ".$bool_delete_permission."<br/>";
			$this->set(compact('bool_delete_permission'));
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
			case ROLE_ASSISTANT:
			case ROLE_SALES_EXECUTIVE:
				return array(
					'controller' => 'quotations',
					'action' => 'index'
				);
			default:
				//echo "redirecting to loginpage!<br/>";
				return array(
				  'controller' => 'users',
				  'action' => 'login'
				);
				break;
		}
	}
/*	
	public function recreateStockItemLogs($id = null) {
		$this->StockItem->id = $id;
		if (!$this->StockItem->exists()) {
			throw new NotFoundException(__('Invalid stock item'));
		}
		//$this->request->allowMethod('post', 'delete');
		$this->loadModel('StockItemLog');
		$stockItem=$this->StockItem->find('first',array(
			'conditions'=>array('StockItem.id'=>$id),
			'contain'=>array(
				'StockItemLog',
				'Product'=>array(
					'ProductType'=>array(
						'fields'=>'ProductType.product_category_id',
					)
				)
			)
		));
		//pr($stockItem);
		$datasource=$this->StockItem->getDataSource();
		try{
			$datasource->begin();
			foreach ($stockItem['StockItemLog'] as $stockItemLog){
				//pr($stockItemLog);
				$this->StockItemLog->id=$stockItemLog['id'];
				$logsuccess=$this->StockItemLog->delete();
				if (!$logsuccess) {
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
		$this->loadModel('StockMovement');
		$this->loadModel('ProductionMovement');
		
		$creationmovement=array();
		$reclassificationcreationmovement=array();
		$movements=array();
		$exitedrawmovements=array();
		$stockMovementUsed=false;
		$productionMovementUsed=false;
		$productionMovementAndRawExitUsed=false;
		
		$categoryid=$stockItem['Product']['ProductType']['product_category_id'];
		switch ($categoryid){
			case CATEGORY_RAW:
				$creationmovement=$this->StockMovement->find('first',array(
					'conditions'=>array(
						'StockMovement.stockitem_id'=>$id,
						'bool_input'=>true,
					),
				));
				
				$movements=$this->ProductionMovement->find('all',array(
					'conditions'=>array(
						'ProductionMovement.stockitem_id'=>$id,
						'bool_input'=>true,
					),
					'order'=>'movement_date, ProductionMovement.id',
				));
				$productionMovementUsed=true;
				$exitedrawmovements=$this->StockMovement->find('all',array(
					'conditions'=>array(
						'StockMovement.stockitem_id'=>$id,
						'bool_input'=>false,
					),
					'order'=>'movement_date, StockMovement.id',
				));
				if (count($exitedrawmovements)>0){
					$productionMovementAndRawExitUsed=true;
				}
				break;
			case CATEGORY_PRODUCED:
				$creationmovement=$this->ProductionMovement->find('first',array(
					'conditions'=>array(
						'ProductionMovement.stockitem_id'=>$id,
						'bool_input'=>false,
					),
				));
				if (empty($creationmovement)){
					$reclassificationcreationmovement=$this->StockMovement->find('first',array(
					'conditions'=>array(
						'StockMovement.stockitem_id'=>$id,
						'bool_input'=>true,
						'bool_reclassification'=>true,
					),
				));
				}
				$movements=$this->StockMovement->find('all',array(
					'conditions'=>array(
						'StockMovement.stockitem_id'=>$id,
						'bool_input'=>false,
					),
					'order'=>'movement_date, StockMovement.id',
				));
				$stockMovementUsed=true;
				break;
			case CATEGORY_OTHER:
				$creationmovement=$this->StockMovement->find('first',array(
					'conditions'=>array(
						'StockMovement.stockitem_id'=>$id,
						'bool_input'=>true,
					),
				));
				$movements=$this->StockMovement->find('all',array(
					'conditions'=>array(
						'StockMovement.stockitem_id'=>$id,
						'bool_input'=>false,
					),
					'order'=>'movement_date, StockMovement.id',
				));
				$stockMovementUsed=true;
				break;
		}
		
		//pr($creationmovement);
		//pr($movements);
		
		$StockItemLogData=array();
		try {
			$datasource->begin();
			
			switch ($categoryid){
				case CATEGORY_RAW:
				case CATEGORY_OTHER:
					$StockItemLogData['stockitem_id']=$id;
					$StockItemLogData['stock_movement_id']=$creationmovement['StockMovement']['id'];
					$StockItemLogData['stockitem_date']=$creationmovement['StockMovement']['movement_date'];
					$StockItemLogData['product_id']=$creationmovement['StockMovement']['product_id'];
					$StockItemLogData['product_quantity']=$creationmovement['StockMovement']['product_quantity'];
					$StockItemLogData['product_unit_price']=$creationmovement['StockMovement']['product_unit_price'];

					$this->StockItemLog->clear();
					$this->StockItemLog->create();
					$logsuccess=$this->StockItemLog->save($StockItemLogData);
					if (!$logsuccess) {
						echo "problema guardando los estado de lote";
						pr($this->validateErrors($this->StockItemLog));
						throw new Exception();
					}
					break;
				case CATEGORY_PRODUCED:
					if (!empty($creationmovement)){
						$StockItemLogData['stockitem_id']=$id;
						$StockItemLogData['production_movement_id']=$creationmovement['ProductionMovement']['id'];
						$StockItemLogData['stockitem_date']=$creationmovement['ProductionMovement']['movement_date'];
						$StockItemLogData['product_id']=$creationmovement['ProductionMovement']['product_id'];
						$StockItemLogData['product_quantity']=$creationmovement['ProductionMovement']['product_quantity'];
						$StockItemLogData['product_unit_price']=$creationmovement['ProductionMovement']['product_unit_price'];
						$StockItemLogData['production_result_code_id']=$creationmovement['ProductionMovement']['production_result_code_id'];
						$this->StockItemLog->clear();
						$this->StockItemLog->create();
						$logsuccess=$this->StockItemLog->save($StockItemLogData);
						if (!$logsuccess) {
							pr($StockItemLogData);
							echo "problema guardando los estado de lote";
							pr($this->validateErrors($this->StockItemLog));
							throw new Exception();
						}
					}
					else {
						$StockItemLogData['stockitem_id']=$id;
						$StockItemLogData['production_movement_id']=$reclassificationcreationmovement['StockMovement']['id'];
						$StockItemLogData['stockitem_date']=$reclassificationcreationmovement['StockMovement']['movement_date'];
						$StockItemLogData['product_id']=$reclassificationcreationmovement['StockMovement']['product_id'];
						$StockItemLogData['product_quantity']=$reclassificationcreationmovement['StockMovement']['product_quantity'];
						$StockItemLogData['product_unit_price']=$reclassificationcreationmovement['StockMovement']['product_unit_price'];
						$StockItemLogData['production_result_code_id']=$reclassificationcreationmovement['StockMovement']['production_result_code_id'];
						$this->StockItemLog->clear();
						$this->StockItemLog->create();
						$logsuccess=$this->StockItemLog->save($StockItemLogData);
						if (!$logsuccess) {
							pr($StockItemLogData);
							echo "problema guardando los estado de lote";
							pr($this->validateErrors($this->StockItemLog));
							throw new Exception();
						}
					}
					break;
			}
			$remainingQuantityStockItem=$stockItem['StockItem']['original_quantity'];		
			
			if ($productionMovementAndRawExitUsed){
				$amountrawregistered=0;
				foreach ($movements as $movement){
					for ($r=$amountrawregistered;$r<count($exitedrawmovements);$r++){
						if ($movement['ProductionMovement']['movement_date']>$exitedrawmovements[$r]['StockMovement']['movement_date']){
							$remainingQuantityStockItem-=$exitedrawmovements[$r]['StockMovement']['product_quantity'];
							$StockItemLogData['stockitem_id']=$id;
							$StockItemLogData['stock_movement_id']=$exitedrawmovements[$r]['StockMovement']['id'];
							$StockItemLogData['production_movement_id']=null;
							$StockItemLogData['stockitem_date']=$exitedrawmovements[$r]['StockMovement']['movement_date'];
							$StockItemLogData['product_id']=$exitedrawmovements[$r]['StockMovement']['product_id'];
							$StockItemLogData['product_quantity']=$remainingQuantityStockItem;
							switch ($categoryid){
								case CATEGORY_RAW:
								case CATEGORY_OTHER:
									$StockItemLogData['product_unit_price']=$creationmovement['StockMovement']['product_unit_price'];
									break;
								case CATEGORY_PRODUCED:
									$StockItemLogData['product_unit_price']=$creationmovement['ProductionMovement']['product_unit_price'];
									break;
							}
							//$StockItemLogData['production_result_code_id']=$exitedrawmovement['StockMovement']['production_result_code_id'];
							$amountrawregistered++;
						}
					}
					$remainingQuantityStockItem-=$movement['ProductionMovement']['product_quantity'];
					$StockItemLogData['stockitem_id']=$id;
					$StockItemLogData['stock_movement_id']=null;
					$StockItemLogData['production_movement_id']=$movement['ProductionMovement']['id'];
					$StockItemLogData['stockitem_date']=$movement['ProductionMovement']['movement_date'];
					$StockItemLogData['product_id']=$movement['ProductionMovement']['product_id'];
					$StockItemLogData['product_quantity']=$remainingQuantityStockItem;
					switch ($categoryid){
						case CATEGORY_RAW:
						case CATEGORY_OTHER:
							$StockItemLogData['product_unit_price']=$creationmovement['StockMovement']['product_unit_price'];
							break;
						case CATEGORY_PRODUCED:
							$StockItemLogData['product_unit_price']=$creationmovement['ProductionMovement']['product_unit_price'];
							break;
					}
					$StockItemLogData['production_result_code_id']=$movement['ProductionMovement']['production_result_code_id'];

					$this->StockItemLog->clear();
					$this->StockItemLog->create();
					$logsuccess=$this->StockItemLog->save($StockItemLogData);
					if (!$logsuccess) {
						echo "problema guardando los estado de lote";
						pr($this->validateErrors($this->StockItemLog));
						throw new Exception();
					}
				}
				for ($k=$amountrawregistered;$k<count($exitedrawmovements);$k++){
					$remainingQuantityStockItem-=$exitedrawmovements[$k]['StockMovement']['product_quantity'];
					$StockItemLogData['stockitem_id']=$id;
					$StockItemLogData['stock_movement_id']=$exitedrawmovements[$k]['StockMovement']['id'];
					$StockItemLogData['production_movement_id']=null;
					$StockItemLogData['stockitem_date']=$exitedrawmovements[$k]['StockMovement']['movement_date'];
					$StockItemLogData['product_id']=$exitedrawmovements[$k]['StockMovement']['product_id'];
					$StockItemLogData['product_quantity']=$remainingQuantityStockItem;
					switch ($categoryid){
						case CATEGORY_RAW:
						case CATEGORY_OTHER:
							$StockItemLogData['product_unit_price']=$creationmovement['StockMovement']['product_unit_price'];
							break;
						case CATEGORY_PRODUCED:
							$StockItemLogData['product_unit_price']=$creationmovement['ProductionMovement']['product_unit_price'];
							break;
					}
					//$StockItemLogData['production_result_code_id']=$exitedrawmovements[$k]['StockMovement']['production_result_code_id'];
					$this->StockItemLog->clear();
					$this->StockItemLog->create();
					$logsuccess=$this->StockItemLog->save($StockItemLogData);
					if (!$logsuccess) {
						echo "problema guardando los estado de lote";
						pr($this->validateErrors($this->StockItemLog));
						throw new Exception();
					}
				}
			}
			else {
				foreach ($movements as $movement){
					if ($productionMovementUsed){
						$remainingQuantityStockItem-=$movement['ProductionMovement']['product_quantity'];
						$StockItemLogData['stockitem_id']=$id;
						$StockItemLogData['stock_movement_id']=null;
						$StockItemLogData['production_movement_id']=$movement['ProductionMovement']['id'];
						$StockItemLogData['stockitem_date']=$movement['ProductionMovement']['movement_date'];
						$StockItemLogData['product_id']=$movement['ProductionMovement']['product_id'];
						$StockItemLogData['product_quantity']=$remainingQuantityStockItem;
						switch ($categoryid){
							case CATEGORY_RAW:
							case CATEGORY_OTHER:
								$StockItemLogData['product_unit_price']=$creationmovement['StockMovement']['product_unit_price'];
								break;
							case CATEGORY_PRODUCED:
								$StockItemLogData['product_unit_price']=$creationmovement['ProductionMovement']['product_unit_price'];
								break;
						}
						$StockItemLogData['production_result_code_id']=$movement['ProductionMovement']['production_result_code_id'];
					}
					if ($stockMovementUsed){
						$remainingQuantityStockItem-=$movement['StockMovement']['product_quantity'];
						$this->StockItem->StockItemLog->create();
						$StockItemLogData['stockitem_id']=$id;
						$StockItemLogData['stock_movement_id']=$movement['StockMovement']['id'];
						$StockItemLogData['production_movement_id']=null;
						$StockItemLogData['stockitem_date']=$movement['StockMovement']['movement_date'];
						$StockItemLogData['product_id']=$movement['StockMovement']['product_id'];
						$StockItemLogData['product_quantity']=$remainingQuantityStockItem;
						switch ($categoryid){
							case CATEGORY_RAW:
							case CATEGORY_OTHER:
								$StockItemLogData['product_unit_price']=$creationmovement['StockMovement']['product_unit_price'];
								break;
							case CATEGORY_PRODUCED:
								if (!empty($creationmovement)){
									$StockItemLogData['product_unit_price']=$creationmovement['ProductionMovement']['product_unit_price'];
								}
								else {
									$StockItemLogData['product_unit_price']=$reclassificationcreationmovement['StockMovement']['product_unit_price'];
								}
								break;
						}
						$StockItemLogData['production_result_code_id']=$movement['StockMovement']['production_result_code_id'];
					}
					$this->StockItemLog->clear();
					$this->StockItemLog->create();
					$logsuccess=$this->StockItemLog->save($StockItemLogData);
					if (!$logsuccess) {
						echo "problema guardando los estado de lote";
						pr($this->validateErrors($this->StockItemLog));
						throw new Exception();
					}
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
*/
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
	function uploadFiles($folder, $formdata, $itemId = null) {	
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
			$filename = str_replace(' ', '_', $file['name']);		
			// assume filetype is false		
			$typeOK = false;		
			// check filetype is ok		
			foreach($permitted as $type) {			
				if($type == $file['type']) {				
					$typeOK = true;				
					break;		
				}		
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
}
