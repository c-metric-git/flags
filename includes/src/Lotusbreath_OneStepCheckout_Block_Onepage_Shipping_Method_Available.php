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
class Lotusbreath_OneStepCheckout_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $countryCode = Mage::getStoreConfig('general/country/default');
            if ($countryCode && !$this->getAddress()->getCountryId())
                $this->getAddress()->setCountryId($countryCode);

            $isUpdateShippingRates = true;
            $theAddress = $this->getAddress();

            if (Mage::getStoreConfig('lotusbreath_onestepcheckout/general/loadshippingrateswhenfillall')){
                $relatedLocationFields = Mage::getStoreConfig("lotusbreath_onestepcheckout/general/location_fields");

                if ($relatedLocationFields){
                    $relatedLocationFields = explode(',',$relatedLocationFields);
                }
                $isUpdateShippingRates = true;
                /*
                foreach ($relatedLocationFields as $localeField){
                     if ($localeField){
                         if ($localeField == 'region_id' && !Mage::getStoreConfig('general/region/display_all')){
                            continue;
                         }
                         $value = $theAddress->getData($localeField);
                         //echo $localeField . '-' . $value . '|';
                         if (empty($value)){
                             $isUpdateShippingRates = false;
                         }
                     }

                }*/
            }

            $groups = false;
            if ($isUpdateShippingRates){
                //$this->getAddress()->save()->setCollectShippingRates(true);
                $this->getAddress()->collectShippingRates()

                    //->save() ->setCollectShippingRates(false)
                ;
                $groups = $this->getAddress()->getGroupedAllShippingRates();


                if(count($groups) == 1){
                    $_sole = count($groups) == 1;
                    $_rates = $groups[key($groups)];
                    $_sole = $_sole && count($_rates) == 1;
                    if ($_sole) {

                        $result = Mage::getSingleton('lotusbreath_onestepcheckout/type_onepage')->saveShippingMethod(reset($_rates)->getCode());

                        if (!$result) {
                            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                                array('request' => Mage::app()->getRequest(),
                                    'quote' => $this->getQuote()));
                        }
                        $this->getQuote()->collectTotals()->save();
                    }

                }

                //echo count($groups);exit;
            }


            /*
            if (!empty($groups)) {
                $ratesFilter = new Varien_Filter_Object_Grid();
                $ratesFilter->addFilter(Mage::app()->getStore()->getPriceFilter(), 'price');

                foreach ($groups as $code => $groupItems) {
                    $groups[$code] = $ratesFilter->filter($groupItems);
                }
            }
            */


            /*if(array_key_exists('freeshipping', $groups)){
                $groups = array('freeshipping' => $groups['freeshipping']);
            }*/
            return $this->_rates = $groups;
        }else{
            //echo 1;
        }

        return $this->_rates;
    }

}