<?php
namespace Born\Terminal;

class Processor implements ProcessorInterface {
    /**
     * @var mixed[]
     */
    private $prices;
    /**
     * @var float
     */
    public  $total;
    /**
     * @param array $prices
     */
    public function setPricing($prices = [])
    {
        $this->prices = $prices;
    }
    /**
     * @param string $cart
     */
    public function scan($cart = '')
    {
        $orderedProducts = $this->scanProducts($cart);
        $subtotsl = 0;
        foreach ($orderedProducts as $product => $qty) {
            $subtotsl += $this->getSubtotal($product, $qty);
        }
        $this->total = $subtotsl;
    }
    /**
     * @param bool $pint
     * @return float
     */
    public function getTotal($pint = false)
    {
        if ($pint) {
            echo $this->formatPrice($this->total) . "\n";
        } else {
            return $this->total;
        }

    }
    /**
     * @param string $cart
     * @return array
     */
    private function scanProducts($cart)
    {
        $products = [];
        foreach(str_split($cart) as $char){
            $char = strtoupper($char);
            if (empty($products[$char])) {
                $products[$char] = 1;
            } else {
                $products[$char] ++;
            }
        }
        return $products;
    }
    /**
     * @param string $product
     * @param int $qty
     * @return float|int
     */
    private function getSubtotal(string $product, int $qty) {
        if (!$this->prices[$product]) {
            return 0;
        }
        $maxMatchedQty = 0;
        $maxMatchedPrice = 0;
        foreach ($this->prices[$product] as $price) {
            if ($qty >= $price['qty'] && $maxMatchedQty < $price['qty']) {
                $maxMatchedQty = $price['qty'];
                $maxMatchedPrice = $price['price'];
            }
        }
        $multiply = (int) ($qty / $maxMatchedQty);
        $price = $maxMatchedPrice * $multiply;
        if (($qty % $maxMatchedQty) == 0) {
            return $price;
        } else {
            $price += $this->getSubtotal($product, $qty % $maxMatchedQty);
        }
        return $price;
    }
    /**
     * @param bool $print
     * @return string
     */
    public function printPricing($print = false)
    {
        $string = '';
        foreach ($this->prices as $product => $prices) {
            $line = $product . ': ';
            foreach ($prices as $price) {
                if ($price['qty'] == 1) {
                    $line .= $this->formatPrice($price['price']);
                } else {
                    $line .= ', '. sprintf ('%d for %s', $price['qty'], $this->formatPrice($price['price']));
                }
            }
            $string .= $line . "\n";
        }
        if ($print) {
            echo $string;
        } else{
            return $string;
        }
    }
    /**
     * @param $price
     * @return string
     */
    private function formatPrice($price)
    {
        return sprintf ('$%01.2f', $price);
    }

}