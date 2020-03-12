<?
	use Bitrix\Main\Localization\Loc;
	\Bitrix\Main\Page\Asset::getInstance()->addCss("/bitrix/themes/.default/sale.css");
	Loc::loadMessages(__FILE__);

	$sum = roundEx($params['SUM'], 2);
	// var_dump($params);
	$orderId = $params['QR_CHECKOUT_ORDER_ID'];
	$error = false;

	if($orderId){
		$order = CSaleOrder::GetByID($orderId);
		$price = $order['PRICE'];
		$userEmail = $order['USER_EMAIL'];
		$orderNumber = $order['ACCOUNT_NUMBER'];
		$pathToTemplate = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);

		// make price to QR pay format
		if(intVal($price) == $price)
			$priceQr = $price.'00';
		else
			$priceQr = preg_replace('/[^0-9]/', '', $price);

		// make checkout descriptions
		$checkoutDescription = str_replace(
			array("#ORDER_ID#", "#ORDER_NUMBER#", "#USER_EMAIL#"),
			array($orderId, $orderNumber, $userEmail),
			$params['QR_CHECKOUT_DESCRIPTION']
		);

		// need to find user name, delivery address
		$lastName = $params['QR_CHECKOUT_PAYER_NAME'];
		$payerAddress = $params['QR_CHECKOUT_PAYER_ADDRESS'];
		$res = CSaleOrderPropsValue::GetList(
            array(),
            array("ORDER_ID" => $orderId, "ORDER_PROPS_ID" => array($params['QR_CHECKOUT_PAYER_NAME'], $params['QR_CHECKOUT_PAYER_ADDRESS'])),
            false,
            false,
            array()
        );
        while($arVals = $res->Fetch()){
			if($arVals['ORDER_PROPS_ID'] == $params['QR_CHECKOUT_PAYER_NAME'])
				$lastName = $arVals['VALUE'];
			if($arVals['ORDER_PROPS_ID'] == $params['QR_CHECKOUT_PAYER_ADDRESS'])
				$payerAddress = $arVals['VALUE'];
		}


		// make vals url
		$qrArray = array(
			"Qr_class" => "ST00012",
			"Name" => "Name=".$params['QR_CHECKOUT_NAME'],
			"PersonalAcc" => "PersonalAcc=".preg_replace('/[^0-9]/', '', $params['QR_CHECKOUT_RS']),
			"BankName" => "BankName=".$params['QR_CHECKOUT_BANK_NAME'],
			"BIC" => "BIC=".preg_replace('/[^0-9]/', '', $params['QR_CHECKOUT_BIK']),
			"CorrespAcc" => "CorrespAcc=".preg_replace('/[^0-9]/', '', $params['QR_CHECKOUT_KS']),
			"KPP" => "KPP=".preg_replace('/[^0-9]/', '', $params['QR_CHECKOUT_PAYEE_KPP']),
			"PayeeINN" => "PayeeINN=".preg_replace('/[^0-9]/', '', $params['QR_CHECKOUT_PAYEE_INN']),
			"lastName" => "lastName=".$lastName,
			"payerAddress" => "payerAddress=".$payerAddress,
			"Purpose" => "Purpose=".$checkoutDescription,
			"Sum" => "Sum=".$priceQr
		);
		$eventResult = $this->onBeforeMakeQr($qrArray, $order);
		$qrArray = $eventResult['QR_ARRAY'];

		$qrStr = implode('|', $qrArray);

		if(!$qrStr)
			$error = true;
	}else{
		$error = true;
	}




?>



<?if(!$error){?>
	<?

	?>
	<div class="sale-paysystem-wrapper">
		<span class="tablebodytext">
			<?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_CHECKOUT_DESCRIPTION')." ".SaleFormatCurrency($price, $order['CURRENCY']);?>
		</span>
		<div class="sale-paysystem-yandex-button-container">
			<span class="sale-paysystem-yandex-button">
				<img class="qr-img" src="//chart.googleapis.com/chart?chs=250x250&cht=qr&choe=UTF-8&chl=<?=$qrStr?>" />
			</span>
			<span class="sale-paysystem-yandex-button-descrition"><?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_CHECKOUT_REDIRECT_MESS');?></span>
		</div>

		<p>
			<span class="tablebodytext sale-paysystem-description">
				<?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_CHECKOUT_WARNING_RETURN');?>
			</span>
		</p>
	</div>
<?}else{?>
	<div class="sale-paysystem-wrapper">
		<span class="tablebodytext" style="color: red;">
			<?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_CHECKOUT_ERROR');?>
		</span>
	</div>
<?}?>