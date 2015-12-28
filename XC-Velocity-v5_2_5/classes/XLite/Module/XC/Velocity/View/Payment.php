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


namespace XLite\Module\XC\Velocity\View;

/**
 * Payment
 */
class Payment extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Velocity/payment.tpl';
    }

    /**
     * Get codes of every state
     *
     * @return string
     */
    protected function getStateCodes()
    {
        $states = \XLite\Core\Database::getRepo('\XLite\Model\State')->findAll();
        $result = array();

        foreach ($states as $state) {
            $result[] = array(
                'id'   => $state->getStateId(),
                'code' => $state->getCode(),
            );
        }

        return json_encode($result);
    }
} 
