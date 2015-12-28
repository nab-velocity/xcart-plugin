{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Velocity configuration page
 *
 * @author    Velocity Team
 * @copyright Copyright (c) 2015-2016 Velocity. All rights reserved
 * @license   
 * @link      http://nabvelocity.com/
 *}

<table cellspacing="1" cellpadding="5" class="settings-table velocity-style">
    <tr>
        <td colspan="2">
            <div class="webhook">
                <div class="url">
                    {paymentMethod.processor.getWebhookURL()}
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="setting-name"><label for="settings_identity_token">{t(#Identity Token#)}</label></td>
        <td>
            <widget
                    class="\XLite\View\FormField\Textarea\Simple"
                    fieldName="settings[identitytoken]"
                    fieldId="settings_identity_token"
                    value="{paymentMethod.getSetting(#identitytoken#)}"
                    fieldOnly="true"
                    />
        </td>
    </tr>
    <tr>
        <td class="setting-name"><label for="settings_workflow_id">{t(#WorkflowId/Service#)}</label></td>
        <td>
            <widget
                    class="\XLite\View\FormField\Input\Text"
                    fieldName="settings[workflowid]"
                    fieldId="settings_workflow_id"
                    value="{paymentMethod.getSetting(#workflowid#)}"
                    fieldOnly="true"
                    />
        </td>
    </tr>
    <tr>
        <td class="setting-name"><label for="settings_application_profile_id">{t(#Application Profile Id#)}</label></td>
        <td>
            <widget
                    class="\XLite\View\FormField\Input\Text"
                    fieldName="settings[applicationprofileid]"
                    fieldId="settings_application_profile_id"
                    value="{paymentMethod.getSetting(#applicationprofileid#)}"
                    fieldOnly="true"
                    />
        </td>
    </tr>
    <tr>
        <td class="setting-name"><label for="settings_merchant_profile_id">{t(#Merchant Profile Id#)}</label></td>
        <td>
            <widget
                    class="\XLite\View\FormField\Input\Text"
                    fieldName="settings[merchantprofileid]"
                    fieldId="settings_merchant_profile_id"
                    value="{paymentMethod.getSetting(#merchantprofileid#)}"
                    fieldOnly="true"
                    />
        </td>
    </tr>
    

    <td class="setting-name"><label for="settings_mode">{t(#Test/Live mode#)}</label></td>
      <td>
        <widget class="\XLite\View\FormField\Select\TestLiveMode" fieldId="settings_mode" fieldName="settings[mode]" fieldOnly=true value="{paymentMethod.getSetting(#mode#)}" />
      </td>
    </tr>

</table>
