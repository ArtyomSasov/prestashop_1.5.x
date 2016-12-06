<?php
@include_once(dirname(__FILE__).'../../../erip.php');//Подключение файла модуля

class eripredirectModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();

		global $smarty;
		$products = array();

		$EP_OrderNo = Tools::GetValue('id_cart');// получаем id_cart
		$cart = New Cart($EP_OrderNo);// Объект корзины
		$erip = new erip();//Объект erip
		$EP_Sum = $cart->getOrderTotal(true, Cart::BOTH);//Сумма заказа
		$erip->validateOrder($cart->id, Configuration::get('PS_OS_CHEQUE'),$EP_Sum, $erip->displayName);//Создание заказа с статусом ожидаем оплату
		$EP_MerNo = Tools::safeOutput(Configuration::get('ERIP_MER_NO')); //номер поставщика
		$web_key = Tools::safeOutput(Configuration::get('ERIP_WEB_KEY')); //ключ
		$hash = md5($EP_MerNo . $web_key . $EP_OrderNo . $EP_Sum);

		$products = $cart->getProducts();//Описание продукта

		$this->context->smarty->assign(array(
			'EP_MerNo' => $EP_MerNo,
			'EP_Expires' => Tools::safeOutput(Configuration::get('ERIP_EXPIRES')),
			'EP_Sum' => $EP_Sum,
			'EP_Hash' => $hash,
			'EP_OrderNo' =>$EP_OrderNo,
			'EP_OrderInfo' =>$products[0]['name'],
			'EP_Cancel_URL' => $smarty->tpl_vars['base_dir_ssl']->value.'modules/erip/fail.php',
			'EP_Success_URL' => $smarty->tpl_vars['base_dir_ssl']->value.'modules/erip/success.php',
		));

		$this->setTemplate('redirect.tpl');// Подключение шаблона смарти
	}
}

?>