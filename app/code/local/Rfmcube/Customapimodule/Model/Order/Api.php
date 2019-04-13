<?php

class Rfmcube_Customapimodule_Model_Order_Api extends Mage_Sales_Model_Order_Api {

    /**
     * Retrieve list of detailed orders. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function detailedItems($filters) {

        Mage::log('detailedItems for order');
        $res = array();

        $orderCollection = $this->items($filters);

        foreach ($orderCollection as $order) {
            Mage::log('load order id ' . $order['increment_id']);
            $res[] = $this->info($order['increment_id']);
        }
        return $res;
    }

}
