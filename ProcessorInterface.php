<?php
namespace Born\Terminal;

interface ProcessorInterface {
    /**
     * @param array $prices
     */
    public function setPricing($prices = []);
    /**
     * @param string $cart
     */
    public function scan($cart = '');
    /**
     * @param bool $pint
     * @return float
     */
    public function getTotal($pint = false);
}