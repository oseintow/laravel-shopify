<?php
/**
 * Created by oseintow.
 * User: oseintow
 * Date: 9/14/16
 * Time: 7:28 PM
 */

namespace Oseintow\Shopify\Exceptions;

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