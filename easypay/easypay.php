<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_'))
	exit;

class EasyPay extends PaymentModule {

	public function __construct(){
		$this->name = 'easypay';
		$this->tab = 'payments_gateways';
		$this->author = 'EasyPay';
		$this->version = '1.0';
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_); 

		parent::__construct();

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('EasyPay');
		$this->description = $this->l('Приём платежей через систему EasyPay');
		$this->confirmUninstall = $this->l('Вы уверены, что хотите удалить модуль EasyPay?');
	}
	
	// Установка модуля
	public function install(){
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		return parent::install() &&
			$this->registerHook('payment') &&
			Configuration::updateValue('EASYPAY_MODULE_NAME', 'EasyPay') &&
			Configuration::updateValue('EASYPAY_MER_NO', '') &&
			Configuration::updateValue('EASYPAY_DEBUG', '0') &&
			Configuration::updateValue('EASYPAY_WEB_KEY', '') &&
			Configuration::updateValue('EASYPAY_EXPIRES', '2') && 
			Configuration::updateValue('EASYPAY_VALIDATION_PAGE', 'http://'.Tools::safeOutput($_SERVER['HTTP_HOST']).
				__PS_BASE_URI__.'modules/easypay/validation.php');

	}

	// Удаление модуля
	public function uninstall(){
		return parent::uninstall() && 
			Configuration::deleteByName('EASYPAY_MER_NO') && 
			Configuration::deleteByName('EASYPAY_MODULE_NAME') && 
			Configuration::deleteByName('EASYPAY_DEBUG') && 
			Configuration::deleteByName('EASYPAY_EXPIRES') && 
			Configuration::deleteByName('EASYPAY_VALIDATION_PAGE') && 
			Configuration::deleteByName('EASYPAY_WEB_KEY');
	}

	// Сохранение значений из конфигурации
	public function getContent(){
		$output = null;
		$check = true;
		$array = array('EASYPAY_MER_NO','EASYPAY_WEB_KEY','EASYPAY_EXPIRES');

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
				$conf_value = strval(Tools::getValue('EASYPAY_DEBUG'));
				Configuration::updateValue('EASYPAY_DEBUG', $conf_value); 
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
				'image' => 'https://ssl.easypay.by/imgs/icons/easypay_icon.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Номер Поставщика:'),
					'name' => 'EASYPAY_MER_NO',
					'size' => 30,
					'required' => true,
					'desc' => 'Выдается Администратором при подключении к EasyPay.'),
				array(
					'type' => 'text',
					'label' => $this->l('Ключ для подписи счетов:'),
					'name' => 'EASYPAY_WEB_KEY',
					'size' => 30,
					'required' => true,
					'desc' => 'Web_key (выдается Администратором при подключении к EasyPay).'), 
				array(
					'type' => 'radio',
					'is_bool' => true,
					'label' => $this->l('Режим отладки:'),
					'name' => 'EASYPAY_DEBUG',
					'required' => true,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'debug_on',
							'value' => 1,
							'label' => $this->l('Enabled')),
						array(
							'id' => 'debug_off',
							'value' => 0,
							'label' => $this->l('Disabled'))),
					'desc' => 'В этом режиме выводится подробная информация о передаваемых данных. Выписка счетов и оплата при включенном режиме осуществляется реальная.'),
				array(
					'type' => 'text',
					'label' => $this->l('Срок действия счета:'),
					'name' => 'EASYPAY_EXPIRES',
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

		$helper->fields_value['EASYPAY_MER_NO'] = Configuration::get('EASYPAY_MER_NO');
		$helper->fields_value['EASYPAY_WEB_KEY'] = Configuration::get('EASYPAY_WEB_KEY');
		$helper->fields_value['EASYPAY_DEBUG'] = Configuration::get('EASYPAY_DEBUG');
		$helper->fields_value['EASYPAY_EXPIRES'] = Configuration::get('EASYPAY_EXPIRES');

		return $helper->generateForm($fields_form);
	}
	
	// Хук оплаты
	public function hookPayment($params){

		global $smarty;

		if (!$this->active || !Configuration::get('EASYPAY_MER_NO') || !Configuration::get('EASYPAY_WEB_KEY'))
			return;

		$smarty->assign(array(
			'id' => (int)$params['cart']->id //отдаём id корзины
			));
		return $this->context->smarty->fetch(__FILE__, 'easypay.tpl');
	}
}

?>