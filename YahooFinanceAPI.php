<?php

namespace dmitriyLuch\Yii2YahooFinanceAPI;

class YahooFinanceAPI
{
    public $api_url = 'http://query.yahooapis.com/v1/public/yql';

    const SYMBOL = 'Symbol';
    const NAME = 'Name';
    const CHANGE = 'Change';
    const CHANGE_REALTIME = 'ChangeRealtime';
    const PER_RATIO = 'PERatio';
    const PER_RATIO_REALTIME = 'PERatioRealtime';
    const VOLUME = 'Volume';
    const PERCENT_CHANGE = 'PercentChange';
    const DIVIDEND_YIELD = 'DividendYield';
    const LAST_TRADE_REALTIME_WITH_TIME = 'LastTradeRealtimeWithTime';
    const LAST_TRADE_WITH_TIME = 'LastTradeWithTime';
    const LAST_TRADE_PRICE_ONLY = 'LastTradePriceOnly';
    const LAST_TRADE_TIME = 'LastTradeTime';
    const LAST_TRADE_DATE = 'LastTradeDate';
    const OPEN = 'Open';
    const PREV_CLOSE = 'PrevClose';

    public function getDefaultFields(){
        return [
            self::SYMBOL,
            self::NAME,
            self::CHANGE,
            self::CHANGE_REALTIME,
            self::PER_RATIO,
            self::PER_RATIO_REALTIME,
            self::VOLUME,
            self::PERCENT_CHANGE,
            self::DIVIDEND_YIELD,
            self::LAST_TRADE_REALTIME_WITH_TIME,
            self::LAST_TRADE_WITH_TIME,
            self::LAST_TRADE_PRICE_ONLY,
            self::LAST_TRADE_TIME,
            self::LAST_TRADE_DATE,
            self::OPEN,
            self::PREV_CLOSE,
        ];
    }

    /**
     * @param array $tickers The array of ticker symbols
     * @param array|bool $fields Array of fields to get from the returned XML
     * document, or if true use default fields, or if false return XML
     *
     * @return array|string The array of data or the XML document
     */
    public function api ($tickers,$fields=true) {
        // set url
        $url = $this->api_url;
        $url .= '?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20%28%22'.implode(',',$tickers).'%22%29&env=store://datatables.org/alltableswithkeys';

        // set fields
        if ($fields===true || empty($fields)) {
            $fields = $this->getDefaultFields();
        }

        for($i=0; $i<10; $i++){
            // make request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $resp = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            print_r($resp);
            curl_close($ch);

            // parse response
            if (!empty($fields)) {
                $xml = new \SimpleXMLElement($resp);
                $data = array();
                $row = array();
                $time = time();
                if(is_object($xml)){
                    foreach($xml->results->quote as $quote){
                        $row = array();
                        foreach ($fields as $field) {
                            $row[$field] = (string) $quote->$field;
                        }
                        $data[] = $row;
                    }
                }
            } else {
                $data = $resp;
            }
            if(is_array($data) && count($data) > 0){
                break;
            }
        }

        return $data;
    }
}
