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


namespace XLite\Module\XC\Velocity\View\FormField\Input\Text;


class CardNumber extends \XLite\View\FormField\Input\Text
{
    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'card-number-field';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 24;
    }
} 