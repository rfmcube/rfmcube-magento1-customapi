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
            $res[] = $this->detailedInfo($customer['customer_id']);
        }
        return $res;
    }

    /**
     * Retrieve customer data
     *
     * @param int $customerId
     * @param array $attributes
     * @return array
     */
    public function detailedInfo($customerId, $attributes = null) {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }
        if (!is_null($attributes) && !is_array($attributes)) {
            $attributes = array($attributes);
        }
        $result = array();
        foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
            $result[$attributeAlias] = $customer->getData($attributeCode);
        }
        foreach ($this->getAllowedAttributes($customer, $attributes) as $attributeCode => $attribute) {
            $result[$attributeCode] = $customer->getData($attributeCode);
        }

        //RFMCUBE
        //add the subscription status for the current customer
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        $result['is_subscribed'] = $subscriber->isSubscribed();

        //find a way to track the last subscription changed date
        //$result['last_updated_subscription']=$subscriber->isSubscribed();
        //RFMCUBE

        return $result;
    }

}
