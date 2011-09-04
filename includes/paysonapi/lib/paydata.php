<?php

class PayData {
    // Required
    protected $returnUrl;
    protected $cancelUrl;
    protected $ipnUrl;
    protected $memo;
    protected $sender;
    protected $receivers;

    // Optional
    protected $localeCode;
    protected $currencyCode;
    protected $orderItems;

    protected $fundingConstraints;
    protected $invoiceFee;

    protected $custom;
    protected $trackingId;
    protected $guaranteeOffered;
    protected $feesPayer;

    public function __construct($returnUrl, $cancelUrl, $ipnUrl, $memo, $sender, $receivers) {
        $this->setReturnUrl($returnUrl);
        $this->setCancelUrl($cancelUrl);
        $this->setIpnUrl($ipnUrl);
        $this->setMemo($memo);
        $this->setSender($sender);
        $this->setReceivers($receivers);
    }

    public function setReturnUrl($url) {
        $this->returnUrl = $url;
    }

    public function setCancelUrl($url) {
        $this->cancelUrl = $url;
    }

    public function setIpnUrl($url) {
        $this->ipnUrl = $url;
    }

    public function setMemo($memo) {
        $this->memo = $memo;
    }
	
	public function setguaranteeOffered($value)
	{
		$this->guaranteeOffered = $value;
	}

    public function setSender($sender) {
        if(get_class($sender) != "Sender"){
            throw new PaysonApiException("Object not of type Sender");
        }
        
        $this->sender = $sender;
    }

    public function setReceivers($receivers) {
        if(!is_array($receivers))
            throw new PaysonApiException("Parameter must be an array of Receivers");

        foreach ($receivers as $receiver){
            if(get_class($receiver) != "Receiver")
                throw new PaysonApiException("Parameter must be an array of Receivers");
        }

        $this->receivers = $receivers;
    }

    public function setLocaleCode($localeCode) {
        $this->localeCode = $localeCode;
    }

    public function setCurrencyCode($currencyCode) {
        $this->currencyCode = $currencyCode;
    }

    public function setFeesPayer($feesPayer) {
        $this->feesPayer = $feesPayer;
    }

    public function setOrderItems($items) {
        if(!is_array($items))
            throw new PaysonApiException("Parameter must be an array of OrderItems");

        foreach ($items as $item){
            if(get_class($item) != "OrderItem")
                throw new PaysonApiException("Parameter must be an array of OrderItems");
        }

        $this->orderItems = $items;
    }

    public function setFundingConstraints($constraints) {
        if(!is_array($constraints))
            throw new PaysonApiException("Parameter must be an array of funding constraints");

        $this->fundingConstraints = $constraints;
    }

    public function setInvoiceFee($invoiceFee) {
        $this->invoiceFee = $invoiceFee;
    }

    public function setCustom($custom) {
        $this->custom = $custom;
    }

    public function setTrackingId($trackingId) {
        $this->trackingId = $trackingId;
    }

    public function getOutput(){
        $output = array();

        $output["returnUrl"] = $this->returnUrl;
        $output["cancelUrl"] = $this->cancelUrl;
        $output["ipnNotificationUrl"] = $this->ipnUrl;
        $output["memo"] = $this->memo;

        if(isset($this->localeCode)){
            $output["localeCode"] = LocaleCode::ConstantToString($this->localeCode);
        }

        if(isset($this->currencyCode)){
            $output["currencyCode"] = CurrencyCode::ConstantToString($this->currencyCode);
        }

        $this->sender->addSenderToOutput($output);
        Receiver::addReceiversToOutput($this->receivers, $output);

        OrderItem::addOrderItemsToOutput($this->orderItems, $output);

        if(isset($this->fundingConstraints)) {
            FundingConstraint::addConstraintsToOutput($this->fundingConstraints, $output);

            if(in_array(FundingConstraint::INVOICE, $this->fundingConstraints) and
                isset($this->invoiceFee))
            {
                $output["invoiceFee"] = $this->invoiceFee;
            }
        }

        if(isset($this->custom)){
            $output["custom"] = $this->custom;
        }

        if(isset($this->trackingId)){
            $output["trackingId"] = $this->trackingId;
        }

        if(isset($this->feesPayer)) {
            $output["feesPayer"] = FeesPayer::ConstantToString($this->feesPayer);
        }

        if(isset($this->guaranteeOffered)){
            $output["guaranteeOffered"] = $this->guaranteeOffered;
        }

        return $output;
    }

}

?>