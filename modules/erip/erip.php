<?php
if (!defined('_PS_VERSION_'))
	exit;

class ERIP extends PaymentModule {

	public function __construct(){
		$this->name = 'erip';
		$this->tab = 'payments_gateways';
		$this->author = 'EasyPay';
		$this->version = '1.0';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6'); 

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('ЕРИП');
		$this->description = $this->l('Приём платежей через систему ЕРИП');
		$this->confirmUninstall = $this->l('Вы уверены, что хотите удалить модуль ЕРИП?');
	}
	
	// Установка модуля
	public function install(){
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		return parent::install() &&
			$this->registerHook('payment') &&
			Configuration::updateValue('ERIP_MODULE_NAME', 'ERIP') &&
			Configuration::updateValue('ERIP_MER_NO', '') &&
			Configuration::updateValue('ERIP_WEB_KEY', '') &&
			Configuration::updateValue('ERIP_EXPIRES', '2') && 
			Configuration::updateValue('ERIP_VALIDATION_PAGE', 'http://'.Tools::safeOutput($_SERVER['HTTP_HOST']).
				__PS_BASE_URI__.'modules/erip/validation.php');

	}

	// Удаление модуля
	public function uninstall(){
		return parent::uninstall() && 
			Configuration::deleteByName('ERIP_MER_NO') && 
			Configuration::deleteByName('ERIP_MODULE_NAME') && 
			Configuration::deleteByName('ERIP_EXPIRES') && 
			Configuration::deleteByName('ERIP_VALIDATION_PAGE') && 
			Configuration::deleteByName('ERIP_WEB_KEY');
	}

	// Сохранение значений из конфигурации
	public function getContent(){
		$output = null;
		$check = true;
		$array = array('ERIP_MER_NO','ERIP_WEB_KEY','ERIP_EXPIRES');

		if (Tools::isSubmit('submit'.$this->name)){
			foreach ($array as $value){
				$conf_value = strval(Tools::getValue($value));
				if (!$conf_value  || empty($conf_value) || !Validate::isGenericName($conf_value)){
						$output .= $this->displayError( $this->l('Неверное значение поля') );
						$check = false;
				}elseif ($check){
					Configuration::updateValue($value, $conf_value); 
				}
			}
			if ($check){ 
				$output .= $this->displayConfirmation($this->l('Настройки сохранены'));
			}
			
		}
		return $output.$this->displayForm();
	}

	// Форма страницы конфигурации
	public function displayForm(){
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$fields_form[0]['form'] = array(

			'legend' => array(
				'title' =>  null,
				'image' => ''
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Номер Поставщика:'),
					'name' => 'ERIP_MER_NO',
					'size' => 30,
					'required' => true,
					'desc' => 'Выдается Администратором при подключении к EasyPay.'),
				array(
					'type' => 'text',
					'label' => $this->l('Ключ для подписи счетов:'),
					'name' => 'ERIP_WEB_KEY',
					'size' => 30,
					'required' => true,
					'desc' => 'Web_key (выдается Администратором при подключении к EasyPay).'), 
				array(
					'type' => 'text',
					'label' => $this->l('Срок действия счета:'),
					'name' => 'ERIP_EXPIRES',
					'size' => 30,
					'required' => true,
					'desc' => 'Число от 1 до 30, если период задан в днях или от 600 до 3600*24, если период задан в секундах.')),
			
			'submit' => array(
				'title' => $this->l('Сохранить'),
				'class' => 'button'));
		 
		$helper = new HelperForm();

		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		 
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		
		$helper->title = $this->displayName;
		$helper->show_toolbar = false;        
		$helper->toolbar_scroll = false;      
		$helper->submit_action = 'submit'.$this->name;
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Сохранить'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules')),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Назад к списку')));

		$helper->fields_value['ERIP_MER_NO']  = Configuration::get('ERIP_MER_NO');
		$helper->fields_value['ERIP_WEB_KEY'] = Configuration::get('ERIP_WEB_KEY');
		$helper->fields_value['ERIP_EXPIRES'] = Configuration::get('ERIP_EXPIRES');

		return $helper->generateForm($fields_form);
	}
	
	// Хук оплаты
	public function hookPayment($params){

		global $smarty;

		if (!$this->active || !Configuration::get('ERIP_MER_NO') || !Configuration::get('ERIP_WEB_KEY'))
			return;

		$smarty->assign(array(
			'id' => (int)$params['cart']->id //отдаём id корзины
			));
		return $this->display(__FILE__, 'erip.tpl');
	}
}

?>