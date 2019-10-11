<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Currencyget {

        protected static $_iso_currency = '^(AED|AFN|ALL|AMD|ANG|AOA|ARS|AUD|AWG|AZN|BAM|BBD|BDT|BGN|BHD|BIF|BMD|BND|BOB|BOV|BRL|BSD|BTN|BWP|BYR|BZD|CAD|CDF|CHE|CHF|CHW|CLF|CLP|CNY|COP|COU|CRC|CUC|CUP|CVE|CZK|DJF|DKK|DOP|DZD|EGP|ERN|ETB|EUR|FJD|FKP|GBP|GEL|GHS|GIP|GMD|GNF|GTQ|GYD|HKD|HNL|HRK|HTG|HUF|IDR|ILS|INR|IQD|IRR|ISK|JMD|JOD|JPY|KES|KGS|KHR|KMF|KPW|KRW|KWD|KYD|KZT|LAK|LBP|LKR|LRD|LSL|LTL|LVL|LYD|MAD|MDL|MGA|MKD|MMK|MNT|MOP|MRO|MUR|MVR|MWK|MXN|MXV|MYR|MZN|NAD|NGN|NIO|NOK|NPR|NZD|OMR|PAB|PEN|PGK|PHP|PKR|PLN|PYG|QAR|RON|RSD|RUB|RWF|SAR|SBD|SCR|SDG|SEK|SGD|SHP|SLL|SOS|SRD|SSP|STD|SVC|SYP|SZL|THB|TJS|TMT|TND|TOP|TRY|TTD|TWD|TZS|UAH|UGX|USD|USN|USS|UYI|UYU|UZS|VEF|VND|VUV|WST|XAF|XAG|XAU|XBA|XBB|XBC|XBD|XCD|XDR|XFU|XOF|XPD|XPF|XPT|XSU|XTS|XUA|XXX|YER|ZAR|ZMW|ZWL)$';
		        
        public function currency_conversion($value=1, $from, $to){
            if (!preg_match('/'.self::$_iso_currency.'/i', strtoupper(trim($from)))) return 'Currency "FROM", is not a valid ISO Code.';
            if (!preg_match('/'.self::$_iso_currency.'/i', strtoupper(trim($to)))) return 'Currency "TO", is not a valid ISO Code.';
            $exchange = self::casperonapi($from, $to);
            if ( !$exchange ){
                return 'There has been a mistake, the servers do not respond.';
            }else{
                return ($exchange*$value);
            }
        }
        private static function casperonapi($from, $to) {
			$from_Currency = strtolower($from);
			$to_Currency = strtolower($to);
			$encode_amount = 1;
           
			$value=@file_get_contents("http://api.casperon.co/v1/currency/convertsion?amount=1&from=$from&to=$to");
			$value = floatval($value);
            if($value > 0){ 
				return $value;
            } else {
				return false;
			}
	    }

    }


/* End of file currency.php */
/* Location: ./application/libraries/currency.php */