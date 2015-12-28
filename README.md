Velocity X-Cart Module Installation Documentation 

1.	Configuration Requirement:  X-Cart site Version 5.x or above version must be required for our velocity payment module installation.
2. Download velocity X-Cart Module by Right clicking on ".tar" file select save as link option and save the .tar file.

3.	Installation & Configuration of Module from Admin Panel:
	  Login admin panel and click on left-side 'Modules' Menu option then click on 'Upload add-on' button, display one popup with browse option to choose the file from 'Choose File' and click on 'Install add-on'.

Show the list of all payment module listed after succesfull upload your velocity payment module is also listed.

By default our module is enabled and one more option Settings for configure the velocity credential, so first click on 'settings' link then this is redirected to left side 'Store setup'->'Payment methods' menu, shows all type of payment options for addition of our velocity payment module click on 'Add payment method' button display popup for search our velocity module and click on settings for configure our module using velocity credential.

VELOCITY CREDENTIAL DETAILS
1.	Identity Token: - This is security token provided by velocity to merchant.
2.	WorkFlowId/ServiceId: - This is servuce id provided by velocity to merchant.
3.	ApplicationProfileId: - This is Application id provided by velocity to merchant.
4.	MerchantProfileId: - This is Merchant id provided by velocity to merchant.
5.	Test Mode :- This is for test the module, if select dropdwon for test mode enable using test credential make payment and for live payment save module as live.

For Refund option at admin side first open left side menu 'orders->orders list' and click on any order for view order detail then show a Refund button with the refund amount then click on that for refund the total amount.

For edit the velocity credential goto 'Store setup'->'Payment methods' menu then click on configure button of our module and also active/inactive button for active/inactive the module and also remove button for remove the payment option from store but not uninstall. For uninstall the module goto modules menu and uncheck the enabled checkbox and click on remove icon then click on save changes button at bottom. 

4.  We have saved the raw request and response objects in core table of X-Cart as key - value format.