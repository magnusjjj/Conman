<?php

class OrderItem {

    protected $description;
    protected $unitPrice;
    protected $quantity;
    protected $taxPercentage;
    protected $sku;

    const FORMAT_STRING = "orderItemList.orderItem(%d).%s";

    /**
     * Runs the test case and collects the results in a TestResult object.
     * If no TestResult object is passed a new one will be created.
     *
     * @description  Item description
     * @unitPrice Item price
     * @quantity Item quantity
     */
    public function __construct($description, $unitPrice = null, $quantity = null, $taxPercentage = null, $sku = null){
        $this->description = $description;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
        $this->taxPercentage = $taxPercentage;
        $this->sku = $sku;
    }


    public function getDescription(){
        return $this->description;
    }

    public function getUnitPrice(){
        return $this->unitPrice;
    }
    
    public function getQuantity(){
        return $this->quantity;
    }

    public function getTaxPercentage(){
        return $this->taxPercentage;
    }

    public function getSku(){
        return $this->sku;
    }

    public static function parseOrderItems($data){
        $items = array();
        $i = 0;

        if(!is_array($data)){
            return $items;
        }

        while(isset($data[sprintf(self::FORMAT_STRING, $i, "description")]))
        {
            $items[$i] = new OrderItem(
                $data[sprintf(self::FORMAT_STRING, $i, "description")],
                $data[sprintf(self::FORMAT_STRING, $i, "unitPrice")],
                $data[sprintf(self::FORMAT_STRING, $i, "quantity")],
                $data[sprintf(self::FORMAT_STRING, $i, "taxPercentage")],
                $data[sprintf(self::FORMAT_STRING, $i, "sku")]
            );

            $i++;
        }

        return $items;
    }

    public static function addOrderItemsToOutput($items, &$output){
        $i = 0;

        if(is_array($items))
            foreach ($items as $item) {
                $output[sprintf(self::FORMAT_STRING, $i, "description")] = $item->getDescription();
                if($item->getUnitPrice() != null){
                    $output[sprintf(self::FORMAT_STRING, $i, "unitPrice")] = number_format($item->getUnitPrice(), 4, ".", ",");
                    $output[sprintf(self::FORMAT_STRING, $i, "quantity")] =  number_format($item->getQuantity(), 2, ".", "");
                    $output[sprintf(self::FORMAT_STRING, $i, "taxPercentage")] = number_format($item->getTaxPercentage(), 6, ".", "");
                    $output[sprintf(self::FORMAT_STRING, $i, "sku")] = $item->getSku();
                }
                $i++;
            }
    }

    public function __toString(){
        return "description: " . $this->description .
               " unitPrice: " . $this->unitPrice .
               " quantity: " . $this->quantity .
               " taxPercentage: " . $this->taxPercentage .
               " sku: " . $this->sku;
    }
}

?>