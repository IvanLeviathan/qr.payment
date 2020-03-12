<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Request;
use Bitrix\Sale;
use Bitrix\Sale\PaySystem;
use Bitrix\Main\Event;

class QrHandler extends PaySystem\BaseServiceHandler
{
	/**
	 * @param Sale\Payment $payment
	 * @param Request|null $request
	 * @return PaySystem\ServiceResult
	 */
	public function initiatePay(Sale\Payment $payment, Request $request = null)
	{
		return $this->showTemplate($payment, "template");
	}

	/**
	 * @return array
	 */
	public function getCurrencyList()
	{
		return array('RUB');
	}
	public function onBeforeMakeQr($qrArray, $order){
		$returnArr = array("QR_ARRAY" => $qrArray, "ORDER" => $order);
		$event = new Event("qr_payment", "onBeforeMakeQr", $returnArr);
		$event->send();

		// Обработка результатов вызова
		if ($event->getResults()){
		    foreach ($event->getResults() as $eventResult)
		    {
				$returnArr = $eventResult->getParameters();
		    }
		}

		return $returnArr;
	}
}