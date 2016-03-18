<?php
/*
Lotus Breath - One Step Checkout
Copyright (C) 2014  Lotus Breath
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Lotusbreath_OneStepCheckout_Model_Session extends Mage_Checkout_Model_Session{

    public function compareOldData($key, $data){

        $oldData = $this->getData($key);
        switch ($key){
            case 'data_shipping_method':
                if($this->getQuote()->getShippingAddress()->getShippingMethod() == $data)
                    return true;
                break;
        }
        if (!$oldData)
            return false;
        //Mage::log($key . "|" . print_r($oldData, true) . "|" . print_r($data, true) , null, 'onestepcheckout.log', true);
        if (is_array($data) && count($data) > 0){
            foreach($data as $keyV => $value){
                if (isset($oldData[$keyV]) && $oldData[$keyV] == $value ){
                    continue;
                }else{
                    return false;
                }
            }
            //Mage::log("$key not require saved again", null, 'onestepcheckout.log', true);
            return true;
        }else{
            if($oldData == $data){
                //Mage::log("$key not require saved again", null, 'onestepcheckout.log', true);
                return true;
            }
        }

        return false;

    }
}
