<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$description = array(
	'RETURN' => Loc::getMessage('SALE_HPS_QR_CHECKOUT_RETURN'),
	'RESTRICTION' => Loc::getMessage('SALE_HPS_QR_CHECKOUT_RESTRICTION'),
	'COMMISSION' => Loc::getMessage('SALE_HPS_QR_CHECKOUT_COMMISSION'),
	'MAIN' => Loc::getMessage('SALE_HPS_QR_CHECKOUT_DESCRIPTION'),
);


$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$host = $request->isHttps() ? 'https' : 'http';

$isAvailable = \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_TRUE;


/*
ST00012|Name=ИП Григорьева Татьяна Михайловна|PersonalAcc=40802810855160002723|BankName=СЕВЕРО-ЗАПАДНЫЙ БАНК ПАО СБЕРБАНК|BIC=044030653|CorrespAcc=30101810500000000653|KPP=312321321|PayeeINN=524926002383|lastName=Иванов|payerAddress=Цветочная|Purpose=оплата заказа № 123|Sum=354045
*/

$data = array(
	'NAME' => Loc::getMessage('SALE_HPS_QR_CHECKOUT'),
	'SORT' => 500,
	'IS_AVAILABLE' => $isAvailable,
	'CODES' => array(
		"QR_CHECKOUT_NAME" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_NAME"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_NAME_DESC"),
			'SORT' => 100,
			'GROUP' => 'Информация в QR-коде',
		),
		"QR_CHECKOUT_PAYEE_INN" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYEE_INN"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYEE_INN_DESC"),
			'SORT' => 200,
			'GROUP' => 'Информация в QR-коде'
		),
		"QR_CHECKOUT_PAYEE_KPP" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYEE_KPP"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYEE_KPP_DESC"),
			'SORT' => 210,
			'GROUP' => 'Информация в QR-коде'
		),
		"QR_CHECKOUT_BANK_NAME" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_BANK_NAME"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_BANK_NAME_DESC"),
			'SORT' => 250,
			'GROUP' => 'Информация в QR-коде'
		),
		"QR_CHECKOUT_BIK" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_BIK"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_BIK_DESC"),
			'SORT' => 260,
			'GROUP' => 'Информация в QR-коде'
		),
		"QR_CHECKOUT_KS" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_KS"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_KS_DESC"),
			'SORT' => 270,
			'GROUP' => 'Информация в QR-коде'
		),
		"QR_CHECKOUT_RS" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_RS"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_RS_DESC"),
			'SORT' => 280,
			'GROUP' => 'Информация в QR-коде'
		),
		"QR_CHECKOUT_DESCRIPTION" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYMENT_DESCRIPTION"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYMENT_DESCRIPTION_DESC"),
			'SORT' => 290,
			'GROUP' => 'Информация в QR-коде',
			'DEFAULT' => array(
				'PROVIDER_KEY' => 'VALUE',
				'PROVIDER_VALUE' => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYMENT_DESCRIPTION_TEMPLATE"),
			)
		),
		"QR_CHECKOUT_PAYER_NAME" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYER_NAME"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYER_NAME_DESC"),
			'SORT' => 320,
			'GROUP' => 'Информация в QR-коде',
		),
		"QR_CHECKOUT_PAYER_ADDRESS" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYER_ADDRESS"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_PAYER_ADDRESS_DESC"),
			'SORT' => 330,
			'GROUP' => 'Информация в QR-коде',
		),
		"QR_CHECKOUT_ORDER_ID" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_ORDER_ID"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_ORDER_ID_DESC"),
			'SORT' => 340,
			'GROUP' => 'Информация в QR-коде',
			'DEFAULT' => array(
				'PROVIDER_KEY' => 'ORDER',
				'PROVIDER_VALUE' => 'ID'
			)
		),
		"QR_CHECKOUT_RETURN_URL" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_RETURN_URL"),
			"DESCRIPTION" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_RETURN_URL_DESC"),
			'SORT' => 350,
			'GROUP' => 'Информация в QR-коде',
			'DEFAULT' => array(
				'PROVIDER_KEY' => 'VALUE',
				'PROVIDER_VALUE' => $host.'://'.$request->getHttpHost().'/'
			)
		),

		"PS_CHANGE_STATUS_PAY" => array(
			"NAME" => Loc::getMessage("SALE_HPS_QR_CHECKOUT_CHANGE_STATUS_PAY"),
			'SORT' => 400,
			'GROUP' => 'GENERAL_SETTINGS',
			"INPUT" => array(
				'TYPE' => 'Y/N'
			),
		),
	)
);