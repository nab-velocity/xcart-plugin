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


class ExpiredDate extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Velocity/checkout/expired_date.tpl';
    }

    /**
     * Get months array for expired month field
     *
     * @return array
     */
    protected function getExpiredMonths()
    {
        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = $i;
        }

        return $months;
    }

    /**
     * Get years array for expired year field
     *
     * @return array
     */
    protected function getExpiredYears()
    {
        $years = array();

        $currentYear = substr(date("Y"), -2);

        for ($i = 0; $i < 9; $i++) {
            $year = (int)$currentYear + $i;
            $years[$year] = $year;
        }

        return $years;
    }
} 