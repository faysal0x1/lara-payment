<?php

namespace Faysal0x1\LaraPayment\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static makePayment(array $requestData, $type = 'checkout', $pattern = 'json')
 * @method static orderValidate($post_data, $trx_id = '', $amount = 0, $currency = "BDT")
 * @method static returnSuccess($transId,$message,$url='/')
 * @method static returnFail($transId,$message,$url='/')
 * @method static setCustomerInfo(array $info)
 * @method static setShipmentInfo(array $info)
 * @method static setProductInfo(array $info)
 * @method static setAdditionalInfo(array $info)
 */
class SSLCommerzPayment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sslcommerznotification';
    }
} 