<?php
class Mageme_PostSubmission_Model_Observer
{
    /** @var VladimirPopov_WebForms_Model_Results $result */
    protected $result;

    const URL_FIELD_CODE = 'post_url';

    public function postSubmission($observer){
        Mage::log('Post submission triggered',null,'mageme.log',1);
        $result = $observer->getResult();
        $this->result = $result;

        $url = $result->getValue(self::URL_FIELD_CODE);
        Mage::log($url,null,'mageme.log',1);

        if($url){
            $request_params = $this->getCurlParams();
            Mage::log($request_params,null,'mageme.log',1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16');
            $data = curl_exec($ch);
            Mage::log($data,null,'mageme.log',1);
            curl_close($ch);
        }
    }

    protected function getCurlParams(){
        $resultFields = $this->result->getField();
        $curl_params = array();
        foreach ($resultFields as $field_id => $value){
            $field = Mage::getModel('webforms/fields')->load($field_id);
            if($field->getCode() && $field->getCode() != self::URL_FIELD_CODE) {
                $curl_params[$field->getCode()] = urlencode($this->result->getValue($field->getCode()));
            }
        }

        return http_build_query($curl_params);
    }
}