<?
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/easypay.php');
include(dirname(__FILE__).'/../../header.php');

$order_id = $_REQUEST['EP_OrderNo'];

echo "<div style='color:#D1001D; font-size:20px; font-weight:bold;'>Ошибка оплаты счета.</div>";

$history = new OrderHistory();// Объект История заказов
$history->id_order = $order_id;//Получение данных о заказе через id заказа
$history->changeIdOrderState(_PS_OS_CANCELED_, $history->id_order);//Изменим статус заказа на "Отменен"

include(dirname(__FILE__).'/../../footer.php');