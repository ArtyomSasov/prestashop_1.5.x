<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/easypay.php');
include(dirname(__FILE__).'/../../header.php');

$order_id = $_REQUEST['EP_OrderNo'];

echo "<div style='color:#00a12d; font-size:20px; font-weight:bold;'>Счет добавлен в систему ЕРИП для оплаты.</div>
	<div style='color:#00a12d; font-size:20px; font-weight:bold;'>Номер заказа для оплаты: $order_id </div>";

include(dirname(__FILE__).'/../../footer.php');

?>