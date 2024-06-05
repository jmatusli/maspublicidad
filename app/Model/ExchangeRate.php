<?php
App::uses('AppModel', 'Model');

class ExchangeRate extends AppModel {

	public function getApplicableExchangeRate($applicationDate){
		$applicableExchangeRate=$this->find('first',array(
			'conditions'=>array(
				'application_date <='=>$applicationDate,
			),
			'order'=>'application_date DESC',
		));
		return $applicableExchangeRate;
	}
  public function getApplicableExchangeRateValue($applicationDate,$currencyId=CURRENCY_USD){
		$applicableExchangeRate=$this->find('first',[
			'conditions'=>[
				'application_date <='=>$applicationDate,
        'base_currency_id'=>$currencyId,
			],
			'order'=>'application_date DESC',
		]);
		return $applicableExchangeRate['ExchangeRate']['rate'];
	}
	
  public function convertAmount($fromCurrencyId,$toCurrencyId,$conversionDate,$amount){
    switch ($toCurrencyId){
      case CURRENCY_CS:
        if ($fromCurrencyId == CURRENCY_USD){
          $exchangeRateUsd=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_USD);
          return round($amount*$exchangeRateUsd,2);  
        }
        else {
          $exchangeRateEuro=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_EURO);
          return round($amount*$exchangeRateEuro,2);
        }
        break;
      case CURRENCY_USD:
        if ($fromCurrencyId == CURRENCY_CS){
          $exchangeRateUsd=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_USD);
          return round($amount/$exchangeRateUsd,2);  
        }
        else {
          $exchangeRateUsd=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_USD);
          $exchangeRateEuro=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_EURO);
          return round($amount*$exchangeRateEuro/$exchangeRateUsd,2);
        }
        break;
      case CURRENCY_EURO:
        if ($fromCurrencyId == CURRENCY_CS){
          $exchangeRateEuro=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_EURO);
          return round($amount/$exchangeRateEuro,2);  
        }
        else {
          $exchangeRateUsd=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_USD);
          $exchangeRateEuro=$this->getApplicableExchangeRateValue($conversionDate,CURRENCY_EURO);
          return round($amount*$exchangeRateUsd/$exchangeRateEuro,2);
        }
        break;
    }
  }
  
  
	function getLatestExchangeRateDuration(){
		$latestExchangeRate=$this->find('first',array(
			'fields'=>array('ExchangeRate.application_date'),
			'order'=>'ExchangeRate.application_date DESC',
		));
		$duration=0;
		if (!empty($latestExchangeRate)){
			$applicationDate=new DateTime($latestExchangeRate['ExchangeRate']['application_date']);
			//pr($applicationDate);
			$currentDate= new DateTime(date('Y-m-d'));
			$daysPassed=$currentDate->diff($applicationDate);
			//pr($daysPassed);
			$duration=abs($daysPassed->format('%r%a'));
		}
		return $duration;
	}

	public $validate = array(
		'conversion_currency_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'base_currency_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'rate' => array(
			'decimal' => array(
				'rule' => array('decimal',4),
				'message' => 'La tasa de cambio tiene que tener 4 números decimales',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public $belongsTo = [
		'ConversionCurrency' => [
			'className' => 'Currency',
			'foreignKey' => 'conversion_currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'BaseCurrency' => [
			'className' => 'Currency',
			'foreignKey' => 'base_currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];
}
