<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/easypay.php');
include(dirname(__FILE__).'/../../header.php');

$order_id = $_REQUEST['EP_OrderNo'];

echo "<div style='color:#00a12d; font-size:20px; font-weight:bold;'>Операция оплаты прошла успешно</div>";

include(dirname(__FILE__).'/../../footer.php');

?>