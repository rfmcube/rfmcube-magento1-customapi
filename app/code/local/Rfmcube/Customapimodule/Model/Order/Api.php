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
            $res[] = $this->detailedInfo($order['increment_id']);
        }
        return $res;
    }

    /**
     * Retrieve full order information
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function detailedInfo($orderIncrementId) {
        $order = $this->_initOrder($orderIncrementId);
        if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                    Mage::getSingleton('giftmessage/message')->load($order->getGiftMessageId())->getMessage()
            );
        }
        $result = $this->_getAttributes($order, 'order');
        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address'] = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = array();
        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                        Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                );
            }
            //RFMCUBE
            $arrayItem = $this->_getAttributes($item, 'order_item');

//            //add categories to orderItem
            $storeid = $order->getStoreId();
            $prodid = $item->getProductId();
            $product = Mage::helper('catalog/product')->getProduct($prodid, $storeid, null);
//
//            //as array
            $arrayItem['categories'] = array();
            foreach ($product->getCategoryIds() as $categoryId) {

                $category = Mage::getModel('catalog/category')->setStoreId($storeid)->load($categoryId);

                $arrayItem['categories'][] = array(
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'description' => $category->getDescription(),
                    'parent_id' => $category->getParentId()
                );
            }

            //as comma separated string
            $arrayItem['category_ids_as_string'] = implode(",", $product->getCategoryIds());
            //RFMCUBE


            $result['items'][] = $arrayItem;
        }
        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');
        $result['status_history'] = array();
        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }
        return $result;
    }

}
