{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Checkout payment template
 *
 * @author    Velocity Team
 * @copyright Copyright (c) 2015-2016 Velocity. All rights reserved
 * @license   
 * @link      http://nabvelocity.com/
 *}

<div class="velocity-widget">
    <input type="hidden" name="token" id="token" value="" class="token"/>
    <div style="display: none" id="state-codes-data" data-state-codes="{getStateCodes()}"></div>
    <widget class="\XLite\View\CreditCard" />
</div>
