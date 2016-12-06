<?php
@include_once(dirname(__FILE__).'../../../easypay.php');//Подключение файла модуля

class easypayredirectModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();

		global $smarty;
		$products = array();

		$EP_OrderNo = Tools::GetValue('id_cart');// получаем id_cart
		$cart = New Cart($EP_OrderNo);// Объект корзины
		$easypay = new easypay();//Объект easypay
		$EP_Sum = $cart->getOrderTotal(true, Cart::BOTH);//Сумма заказа
		$easypay->validateOrder($cart->id, Configuration::get('PS_OS_CHEQUE'),$EP_Sum, $easypay->displayName);//Создание заказа с статусом ожидаем оплату
		$EP_MerNo = Tools::safeOutput(Configuration::get('EASYPAY_MER_NO')); //номер поставщика
		$web_key = Tools::safeOutput(Configuration::get('EASYPAY_WEB_KEY')); //ключ
		$hash = md5($EP_MerNo . $web_key . $EP_OrderNo . $EP_Sum);
		
		$products = $cart->getProducts();//Описание продукта

		$this->context->smarty->assign(array(
			'EP_MerNo' => $EP_MerNo,
			'EP_Debug' => Tools::safeOutput(Configuration::get('EASYPAY_DEBUG')),
			'EP_Expires' => Tools::safeOutput(Configuration::get('EASYPAY_EXPIRES')),
			'EP_Sum' => $EP_Sum,
			'EP_Hash' => $hash,
			'EP_OrderNo' =>$EP_OrderNo,
			'EP_OrderInfo' =>$products[0]['name'],
			'EP_Cancel_URL' => $smarty->tpl_vars['base_dir_ssl']->value.'modules/easypay/fail.php',
			'EP_Success_URL' => $smarty->tpl_vars['base_dir_ssl']->value.'modules/easypay/success.php',
		));

		$this->setTemplate('redirect.tpl');// Подключение шаблона смарти
	}
}

?>