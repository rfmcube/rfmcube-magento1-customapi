<?php

class Rfmcube_Customapimodule_Model_Customer_Api extends Mage_Customer_Model_Customer_Api {

    /**
     * Retrieve list of detailed customers. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function detailedItems($filters) {

        Mage::log('detailedItems for customer');
        $res = array();

        $customerCollection = $this->items($filters);

        foreach ($customerCollection as $customer) {
            Mage::log('load customer id ' . $customer['customer_id']);
//            Mage::log($customerCollection['id']);
            // $orders[] = $this->_getAttributes($order, 'order');
            $res[] = $this->info($customer['customer_id']);
        }
        return $res;
    }

}
