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


class CVC extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_MOUSE_WHEEL_CTRL] = false;
        $this->widgetParams[static::PARAM_MOUSE_WHEEL_ICON] = false;
    }

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

        $classes[] = 'cvc-number-field';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 4;
    }
}