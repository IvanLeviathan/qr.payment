<?
	use Bitrix\Main\Localization\Loc;
	\Bitrix\Main\Page\Asset::getInstance()->addCss("/bitrix/themes/.default/sale.css");
	Loc::loadMessages(__FILE__);

	$sum = roundEx($params['SUM'], 2);
	// var_dump($params);
	$orderId = $params['QR_CHECKOUT_ORDER_ID'];
	$error = false;

	$logo = false;
	$stamp = false;
	$directorSign = false;
	$buhSign = false;

	if($params['QR_CHECKOUT_COMPANY_LOGO']){
		$arFileTmp = CFile::ResizeImageGet(
            $params['QR_CHECKOUT_COMPANY_LOGO'],
            array("width" => 180, "height" => 80),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );
		if($arFileTmp)
			$logo = $arFileTmp['src'];
	}

	if($params['QR_CHECKOUT_STAMP']){
		$arFileTmp = CFile::ResizeImageGet(
            $params['QR_CHECKOUT_STAMP'],
            array("width" => 150, "height" => 150),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );
		if($arFileTmp)
			$stamp = $arFileTmp['src'];
	}

	if($params['QR_CHECKOUT_DIRECTOR_SIGN']){
		$arFileTmp = CFile::ResizeImageGet(
            $params['QR_CHECKOUT_DIRECTOR_SIGN'],
            array("width" => 200, "height" => 50),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );
		if($arFileTmp)
			$directorSign = $arFileTmp['src'];
	}

	if($params['QR_CHECKOUT_BUH_SIGN']){
		$arFileTmp = CFile::ResizeImageGet(
            $params['QR_CHECKOUT_BUH_SIGN'],
            array("width" => 200, "height" => 50),
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );
		if($arFileTmp)
			$buhSign = $arFileTmp['src'];
	}

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

	// order items
	$dbBasketItems = CSaleBasket::GetList(
		array(
			"NAME" => "ASC",
			"ID" => "ASC"
		),
		array(
			"ORDER_ID" => $orderId,
		)
	);

	//delivery
	$delivery = CSaleDelivery::GetByID($order['DELIVERY_ID']);


?>

<?if(!$error){?>
	<style>
		.qr-table, .sign-table{
			border-collapse: collapse;
			/* border: 1px solid #000; */
			width: 100%;
			margin: 20px 0;
		}
		.qr-table td{
			border: 1px solid #000;
			padding: 5px;
		}
		.sale-paysystem-yandex-button-descrition{
			display: block;
			max-width: 200px;
			text-align: center;
			margin: 0 auto;
		}
		.no-border, .qr-table .no-border{
			border: none;
		}
		.bill-desc{
			margin: 30px 0 30px 0;
			text-align: left;
		}
		.sign-table{
			width: auto;
			margin-top: 40px;
			position: relative;
		}
		.sign-table td{
			font-weight: bold;
		}
		.sign-table td.sign-td{
			width: 200px;
			border-bottom: 1px solid #000;
			text-align: center;
		}
		.sign-table td{
			padding: 30px 15px 0;
			vertical-align: bottom;
		}
		.sign-table td:first-child{
			padding-left: 0;
			text-align: left;
		}
		.sign-table .stamp-img{
			position: absolute;
		}
		.compant-logo{
			max-width: 100%;
		}
	</style>
	<div class="sale-paysystem-wrapper">
		<table class="qr-table">
			<tr>
				<td class="no-border" colspan="3" style="text-align: left;">
					<?if($logo){?>
						<img class="company-logo" src="<?=$logo?>"/>
					<?}?>
				</td>
				<td class="no-border" colspan="2" style="text-align: right;">
					<?if($params['QR_CHECKOUT_NAME']){?>
						<div><b><?=$params['QR_CHECKOUT_NAME']?></b></div>
					<?}?>
					<?if($params['QR_CHECKOUT_COMPANY_ADDRESS']){?>
						<div><b><?=$params['QR_CHECKOUT_COMPANY_ADDRESS']?></b></div>
					<?}?>
					<?if($params['QR_CHECKOUT_COMPANY_PHONE']){?>
						<div><b><a href="tel:<?=$params['QR_CHECKOUT_COMPANY_PHONE']?>"><?=$params['QR_CHECKOUT_COMPANY_PHONE']?></a></b></div>
					<?}?>
				</td>
			</tr>
			<tr>
				<td style="width: 25%;">
					<?if($params['QR_CHECKOUT_PAYEE_INN']){
						echo Loc::getMessage('INN'); echo '&nbsp;'.$params['QR_CHECKOUT_PAYEE_INN'];
					}?>
				</td>
				<td style="width: 25%;">
					<?if($params['QR_CHECKOUT_PAYEE_KPP']){
						echo Loc::getMessage('KPP');
					}?>
				</td>
				<td rowspan="2">
					<?=Loc::getMessage('RS');?>
				</td>
				<td rowspan="2">
					<?=$params['QR_CHECKOUT_RS']?>
				</td>
				<td rowspan="4" style="text-align: center;">
					<img class="qr-img" src="//chart.googleapis.com/chart?chs=200x200&cht=qr&choe=UTF-8&chl=<?=$qrStr?>" />
					<span class="sale-paysystem-yandex-button-descrition"><?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_CHECKOUT_REDIRECT_MESS');?></span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<small><?=Loc::getMessage('CHECKOUT_NAME')?></small>
					<?if($params['QR_CHECKOUT_NAME']){?>
						<br/>
						<?=$params['QR_CHECKOUT_NAME']?>
					<?}?>
				</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="2">
					<small><?=Loc::getMessage('BANK')?></small>
					<?if($params['QR_CHECKOUT_BANK_NAME']){?>
						<br/>
						<?=$params['QR_CHECKOUT_BANK_NAME']?>
					<?}?>
				</td>
				<td>
					<?=Loc::getMessage('BIK')?>
				</td>
				<td>
					<?=$params['QR_CHECKOUT_BIK']?>
				</td>
			</tr>
			<tr>
				<td>
					<?=Loc::getMessage('KS')?>
				</td>
				<td>
					<?=$params['QR_CHECKOUT_KS']?>
				</td>
			</tr>

		</table>

		<h2 style="text-align: center;"><?=$checkoutDescription?></h2>

		<?//ITEM LIST?>
		<?if($dbBasketItems->SelectedRowsCount()){?>
			<table class="qr-table">
				<tr>
					<td>№</td>
					<td>Наименование товара</td>
					<td>Кол-во</td>
					<td>Ед.</td>
					<td>Цена, руб.</td>
					<td>Сумма, руб.</td>
				</tr>
				<?
				$count = 1;
				$allSumm = 0;
				while ($arItem = $dbBasketItems->Fetch()){?>
					<tr>
						<td><?=$count?></td>
						<td><?=$arItem['NAME']?></td>
						<td><?=$arItem['QUANTITY']?></td>
						<td><?=$arItem['MEASURE_NAME']?></td>
						<td><?=number_format($arItem['PRICE'], 2, '.', '')?></td>
						<td><?=number_format($arItem['PRICE'] * $arItem['QUANTITY'], 2, '.', '')?></td>
					</tr>
					<?
					$count++;
					$allSumm += $arItem['PRICE'] * $arItem['QUANTITY'];
				}?>
				<?if($order['PRICE_DELIVERY']){?>
					<tr>
						<td><?=$count?></td>
						<td><?=Loc::getMessage('DELIVERY')?>&nbsp;<?=$delivery['NAME']?></td>
						<td>1</td>
						<td>шт</td>
						<td><?=number_format($order['PRICE_DELIVERY'], 2, '.', '')?></td>
						<td><?=number_format($order['PRICE_DELIVERY'], 2, '.', '')?></td>
					</tr>
					<?$allSumm += $order['PRICE_DELIVERY'];?>
				<?}?>
				<tr>
					<td class="no-border"></td>
					<td class="no-border"></td>
					<td class="no-border"></td>
					<td class="no-border"></td>
					<td class="no-border" style="text-align: right;">Итого:</td>
					<td><?=number_format($allSumm, 2, '.', '')?></td>
				</tr>
			</table>
			<p style="text-align: left;"><?=Loc::getMessage('ALL_NAMES', array(
				"#COUNT#" => $dbBasketItems->SelectedRowsCount(),
				"#SUMM#" => SaleFormatCurrency($allSumm, $order['CURRENCY'])
			))?></p>
			<p style="text-align: left;"><b><?=Number2Word_Rus($allSumm)?></b></p>
		<?}?>

		<?if($params['QR_CHECKOUT_DESC']){?>
			<div class="bill-desc"><?=$params['QR_CHECKOUT_DESC']?></div>
		<?}?>

		<table class="sign-table">
			<?if($stamp){?>
				<tr>
					<td></td>
					<td>
						<img class="stamp-img" src="<?=$stamp?>">
					</td>
				</tr>
			<?}?>
			<?if($params['QR_CHECKOUT_DIRECTOR_STATUS'] && $params['QR_CHECKOUT_DIRECTOR_NAME']){?>
				<tr>
					<td><?=$params['QR_CHECKOUT_DIRECTOR_STATUS']?></td>
					<td class="sign-td">
						<?if($directorSign){?>
							<img src="<?=$directorSign?>">
						<?}?>
					</td>
					<td><?=$params['QR_CHECKOUT_DIRECTOR_NAME']?></td>
				</tr>
			<?}?>
			<?if($params['QR_CHECKOUT_BUH_STATUS'] && $params['QR_CHECKOUT_BUH_NAME']){?>
				<tr>
					<td><?=$params['QR_CHECKOUT_BUH_STATUS']?></td>
					<td class="sign-td">
						<?if($buhSign){?>
							<img src="<?=$buhSign?>">
						<?}?>
					</td>
					<td><?=$params['QR_CHECKOUT_BUH_NAME']?></td>
				</tr>
			<?}?>
		</table>
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