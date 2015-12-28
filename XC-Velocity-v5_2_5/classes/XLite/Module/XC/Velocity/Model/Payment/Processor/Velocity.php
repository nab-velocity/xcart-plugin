<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Velocity Payment Module
 *
 * @category  X-Cart 5
 * @author    Velcity Team
 * @copyright Copyright (c) 2015-2016 Velocity. All rights reserved
 * @license   
 * @link      http://nabvelocity.com/
 */


namespace XLite\Module\XC\Velocity\Model\Payment\Processor;

/**
 * Velocity payment processor
 */
class Velocity extends \XLite\Model\Payment\Base\Online
{
    const ERROR_LOG = 'Velocity_error';
    const SERVICE_NAME = 'Velocity';   
    
    /**
     * Velocity lib included flag
     *
     * @var boolean
     */
    protected $velocityLibInculded = false;
    
    public static $identitytoken;
    public static $workflowid;
    public static $applicationprofileid;
    public static $merchantprofileid;
    public static $userAgent;

  
    
    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XLite\Module\XC\Velocity\View\Config';
    }

    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return 'modules/XC/Velocity/input_template.tpl';
    }

    /**
     * Logging the data under Velocity
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data
     *
     * @return void
     */
    protected static function log($data)
    {
        if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('Velocity', $data);
        }
    }
    
    /**
     * Get webhook URL
     *
     * @return string
     */
    public function getWebhookURL()
    {
        $url = explode(
            '&xid=',
            \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL('callback', null, array(), \XLite::getCustomerScript()),
                \XLite\Core\Config::getInstance()->Security->customer_security
            )
        );

        return $url[0];
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return (
            'live' == $method->getSetting('mode')
            && $method->getSetting('identitytoken')
            && $method->getSetting('workflowid')
            && $method->getSetting('applicationprofileid')
            && $method->getSetting('merchantprofileid')
        )
        || (
            'test' == $method->getSetting('mode')
            && $method->getSetting('identitytoken')
            && $method->getSetting('workflowid')
            && $method->getSetting('applicationprofileid')
            && $method->getSetting('merchantprofileid')
        );
    }

    /**
     * Get allowed backend transactions
     *
     * @return string Status code
     */
    public function getAllowedTransactions()
    {
        return array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
        );
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Format currency
     *
     * @param float $value Currency value
     *
     * @return integer
     */
    protected function formatCurrency($value)
    {
        return $this->getOrder()->getCurrency()->roundValueAsInteger($value);
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $request = \XLite\Core\Request::getInstance();

        static::log(array('request_data' => $request->getData()));

        $this->includeVelocityLibrary();

        if($this->getSetting('mode') == 'test') {
            $isTestAccount = true;
        } else {
            $isTestAccount = false;
        }
       
        $result = static::FAILED;
        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;
        $note = '';
        $backendTransaction = $this->transaction->createBackendTransaction(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
        
        try {            
            $velocityProcessor = new \VelocityProcessor( self::$applicationprofileid, self::$merchantprofileid, self::$workflowid, $isTestAccount, self::$identitytoken );    
        } catch (Exception $e) {
            $this->setDetail('error_message', $e->getMessage(), 'Velocity error message');
            \XLite\Core\TopMessage::addError(static::t($e->getMessage()));
        }
        
        static::log(array('session_token' => $velocityProcessor));
        
        $token = $request->getData();
        $tokendata = json_decode(base64_decode($token['token']));
        $avsData = array (
            'Street'        => $tokendata->addressLine1,
            'City'          => $tokendata->addressCity,
            'StateProvince' => $tokendata->addressState,
            'PostalCode'    => $tokendata->addressZip,
            'Country'       => 'USA'
         );

        if ($tokendata->cardtype == 'AMEX')
            $tokendata->cardtype = 'AmericanExpress';
        else if ($tokendata->cardtype == 'VISA')
            $tokendata->cardtype = 'Visa';
        else if ($tokendata->cardtype == 'MC')
            $tokendata->cardtype = 'MasterCard';
        else if ($tokendata->cardtype == 'DC')
            $tokendata->cardtype = 'Discover';
        
        $cardData = array(
            'cardtype'      => $tokendata->cardtype, 
            'pan'           => $tokendata->number, 
            'expire'        => $tokendata->expMonth.substr($tokendata->expYear, -2), 
            'cvv'           => $tokendata->cvc,
            'track1data'    => '', 
            'track2data'    => ''
        );
        
        /* Request for the verify avsdata and card data*/
        try {        
            $response = $velocityProcessor->verify(array(  
                'amount'       => $this->transaction->getValue(),
                'avsdata'      => $avsData, 
                'carddata'     => $cardData,
                'entry_mode'   => 'Keyed',
                'IndustryType' => 'Ecommerce',
                'Reference'    => 'xyz',
                'EmployeeId'   => '11'
            ));

            $xml = \VelocityXmlCreator::authorizeandcaptureXML(array(  
                'amount'       => $this->transaction->getValue(),
                'avsdata'      => $avsData, 
                'carddata'     => $cardData,
                'entry_mode'   => 'Keyed',
                'IndustryType' => 'Ecommerce',
                'Reference'    => 'xyz',
                'EmployeeId'   => '11'
            ));  // got authorizeandcapture xml object. 

            $req = $xml->saveXML();
            $obj_req = serialize($req);

        } catch (Exception $e) {
            $this->setDetail('error_message', $e->getMessage(), 'Velocity error message');
            \XLite\Core\TopMessage::addError(static::t($e->getMessage()));
        }
        
        if (is_array($response) && isset($response['Status']) && $response['Status'] == 'Successful') {

            /* Request for the authrizeandcapture transaction */
            try {
                $cap_response = $velocityProcessor->authorizeAndCapture( array(
                    'amount'       => $this->transaction->getValue(), 
                    'avsdata'      => $avsData,
                    'token'        => $response['PaymentAccountDataToken'], 
                    'order_id'     => '1526363',//$this->getOrder()->getOrderNumber(),
                    'entry_mode'   => 'Keyed',
                    'IndustryType' => 'Ecommerce',
                    'Reference'    => 'xyz',
                    'EmployeeId'   => '11'
                ));

                if ( is_array($cap_response) && !empty($cap_response) && isset($cap_response['Status']) && $cap_response['Status'] == 'Successful') {

                    $result = static::COMPLETED;
                    $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;
                    $this->setDetail('velocity_payment_id', $cap_response['TransactionId'], 'Velocity Payment ID');
                    $this->setDetail('approval_code', $cap_response['ApprovalCode'], 'Velocity Approval Code');
                    $this->setDetail('request_object', $obj_req, 'Velocity Request Object');
                    $this->setDetail('response_object', serialize($cap_response), 'Velocity Response Object');
                    $this->setDetail('refund_status', $cap_response['TransactionState'], 'Velcoity Transaction Status');

                } else if ( is_array($cap_response) && !empty($cap_response) ) {
                    $this->setDetail('error_message', $cap_response['StatusMessage'], 'Velocity error message');
                    \XLite\Core\TopMessage::addError(static::t($cap_response['StatusMessage']));
                } else if ( is_string($cap_response) ) {
                    $this->setDetail('error_message', $cap_response, 'Velocity error message');
                    \XLite\Core\TopMessage::addError(static::t($cap_response['StatusMessage']));
                } else {
                    $this->setDetail('error_message', 'Some unkown error ouccrs in authandcap', 'Velocity error message');
                    \XLite\Core\TopMessage::addError(static::t('Some unkown error ouccrs in authandcap'));
                }
            } catch(Exception $e) {
                $this->setDetail('error_message', $e->getMessage(), 'Velocity error message');
                \XLite\Core\TopMessage::addError(static::t($e->getMessage()));
            }

        } else if (is_array($response) &&(isset($response['Status']) && $response['Status'] != 'Successful')) {
            $this->setDetail('error_message', $response['StatusMessage'], 'Velocity error message');
            \XLite\Core\TopMessage::addError(static::t($response['StatusMessage']));
        } else if (is_string($response)) {
            $this->setDetail('error_message', $response, 'Velocity error message');
            \XLite\Core\TopMessage::addError(static::t($response));
        } else {
            $this->setDetail('error_message', 'Some unkown error ouccrs', 'Velocity error message');
            \XLite\Core\TopMessage::addError(static::t('Some unkown error ouccrs in verify'));
        }
        
        $backendTransaction->setStatus($backendTransactionStatus);
        $backendTransaction->registerTransactionInOrderHistory('initial request');
        $this->transaction->setNote($note);

        return $result;
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeVelocityLibrary();
        
        $backendTransactionStatus = $transaction::STATUS_FAILED;
        
        $errorData = '';

        if($this->getSetting('mode') == 'test') {
            $isTestAccount = true;
        } else {
            $isTestAccount = false;
        }
        
        try {            
            $velocityProcessor = new \VelocityProcessor( self::$applicationprofileid, self::$merchantprofileid, self::$workflowid, $isTestAccount, self::$identitytoken );    
        } catch (Exception $e) {
            $transaction->setDataCell('error_message', $e->getMessage(), 'Velocity error message');
            $errorData .= $e->getMessage();
        }
        
        $refund_amount = $transaction->getValue();
        $txnid = $transaction->getPaymentTransaction()->getDataCell('velocity_payment_id')->getValue();
        
        try {
            // request for refund
            $response = $velocityProcessor->returnById(array(  
                'amount'        => $refund_amount,
                'TransactionId' => $txnid
            ));

            $xml = \VelocityXmlCreator::returnByIdXML($refund_amount, $txnid);  // got ReturnById xml object.  

            $req = $xml->saveXML();
            $obj_req = serialize($req);

            if ( is_array($response) && !empty($response) && isset($response['Status']) && $response['Status'] == 'Successful') {

                $backendTransactionStatus = $transaction::STATUS_SUCCESS;
                $transaction->setDataCell('velocity_refund_id', $response['TransactionId'], 'Velocity Refund ID');
                $transaction->setDataCell('approval_code', $response['ApprovalCode'], 'Velocity Approval Code');
                $transaction->setDataCell('request_refund_object', $obj_req, 'Velocity Request Refund Object');
                $transaction->setDataCell('response_refund_object', serialize($response), 'Velocity Response Refund Object');
                $transaction->setDataCell('refund_status', $response['TransactionState'], 'Refund Transaction Status');
                $transaction->setStatus($backendTransactionStatus);
                \XLite\Core\Database::getEM()->flush();

            } else if ( is_array($response) && !empty($response) ) {
                $transaction->setDataCell('error_message', $response['StatusMessage'], 'Velocity error message');
                $errorData .= $response['StatusMessage'];
            } else if (is_string($response)) {
                $transaction->setDataCell('error_message', $response, 'Velocity error message');
                $errorData .= $response;
            } else {
                $transaction->setDataCell('error_message', 'Unknown Error please contact the site admin', 'Velocity error message');
                $errorData .= 'Unknown Error please contact the site admin';
            }

        } catch(Exception $e) {
            $transaction->setDataCell('error_message', $e->getMessage(), 'Velocity error message');
            $errorData .= $e->getMessage();
        }  
                
        if (\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus) {

            $order = $transaction->getPaymentTransaction()->getOrder();

            $paymentTransactionSums = $order->getRawPaymentTransactionSums();
            $refunded = $paymentTransactionSums['refunded'];
            $status = $refunded < $transaction->getPaymentTransaction()->getValue()
                ? \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
                : \XLite\Model\Order\Status\Payment::STATUS_REFUNDED;

            $order->setPaymentStatus($status);
            \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been refunded successfully');

        } else {
            $msg = 'Transaction failure';
            if (!empty($errorData)) {
                $msg .= '-' . $errorData;
            }
            \XLite\Core\TopMessage::getInstance()->addError($msg);
        }

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Include and configure velocity sdk
     *
     * @return void
     */
    protected function includeVelocityLibrary()
    {
        if (!$this->velocityLibIncluded) {

            require_once LC_DIR_MODULES . 'XC' . LC_DS . 'Velocity' . LC_DS . 'sdk' . LC_DS . 'Velocity.php';
            if (isset($this->transaction)) {
                
                $identitytoken = $this->getSetting('identitytoken');
                $workflowid = $this->getSetting('workflowid');
                $applicationprofileid = $this->getSetting('applicationprofileid');
                $merchantprofileid = $this->getSetting('merchantprofileid');

            } else {
                $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                    ->findOneBy(array('service_name' => static::SERVICE_NAME));
                $identitytoken = $this->getSetting('identitytoken');
                $workflowid = $this->getSetting('workflowid');
                $applicationprofileid = $this->getSetting('applicationprofileid');
                $merchantprofileid = $this->getSetting('merchantprofileid');
            }

            self::$identitytoken = $identitytoken;
            self::$workflowid = $workflowid;
            self::$applicationprofileid = $applicationprofileid;
            self::$merchantprofileid = $merchantprofileid;
            self::$userAgent = 'xcart';
   
            $this->velocityLibIncluded = true;
        }
    }

    /**
     * Include and configure Velocity SDK
     *
     * @return void
     */
    protected function includeVelocityLibraryWebhook()
    {
        $this-> includeVelocityLibrary();
    }

}
