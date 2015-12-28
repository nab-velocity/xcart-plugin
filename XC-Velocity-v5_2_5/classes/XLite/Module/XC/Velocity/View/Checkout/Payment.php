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

namespace XLite\Module\XC\Velocity\View\Checkout;

/**
 * Payment template
 */
abstract class Payment extends \XLite\View\Checkout\Payment implements \XLite\Base\IDecorator
{
    /**
     * Get JS files 
     * 
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(array('service_name' => 'Velocity'));

        if ($method && $method->isEnabled()) {
            $list[] = 'modules/XC/Velocity/payment.js';

            // Add JS file for dynamic credit card widget
            $list = array_merge($list, $this->getWidget(array(), '\XLite\View\CreditCard')->getJSFiles());
        }

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(array('service_name' => 'Velocity'));

        if ($method && $method->isEnabled()) {

            //Add CSS file for dynamic credit card widget
            $list = array_merge($list, $this->getWidget(array(), '\XLite\View\CreditCard')->getCSSFiles());
        }

        return $list;
    }
}
