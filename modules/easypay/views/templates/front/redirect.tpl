{l s='Ожидание перенаправления' mod='easypay'}

<form action="https://ssl.easypay.by/weborder/?EP_Module=prestashop_1_6" method="post" id="easypay">
	<input type="hidden" name="EP_MerNo" value="{$EP_MerNo}" />
	<input type="hidden" name="EP_Expires" value="{$EP_Expires}" />
	<input type="hidden" name="EP_Debug" value="{$EP_Debug}" />
	<input type="hidden" name="EP_Sum" value="{$EP_Sum}" />
	<input type="hidden" name="EP_OrderNo" value="{$EP_OrderNo}" />
	<input type="hidden" name="EP_Hash" value="{$EP_Hash}" />
	<input type="hidden" name="EP_URL_Type" value="get" />
	<input type="hidden" name="EP_Success_URL" value="{$EP_Success_URL}" />
	<input type="hidden" name="EP_Cancel_URL" value="{$EP_Cancel_URL}" />
	<input type="hidden" name="EP_Comment" value="Заказ от магазина {$shop_name}" />
	<input type="hidden" name="EP_OrderInfo" value="{$EP_OrderInfo}" />
	<input type="hidden" name="EP_Encoding" value="utf-8" />
	<input type="submit" value="{l s='Оплатить' mod='easypay'}">
</form>

<script type="text/javascript">
	$('#easypay').submit();
</script>

