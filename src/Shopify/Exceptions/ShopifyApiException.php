<?php
/**
 * Created by kudobuzz inc.
 * User: kudobuzz
 * Date: 9/14/16
 * Time: 7:28 PM
 */

namespace Kudobuzz\Shopify\Exceptions;

use Exception;

class ShopifyApiException extends Exception
{

    /**
     * ShopifyApiException constructor.
     * @param $message
     * @param int code
     */
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}