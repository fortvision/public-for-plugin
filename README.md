Fortvision platform extension
# 

@- Enable the module by running `php bin/magento module:enable Fortvision_Platform`


**How To Install a Magento 2 Extension via .zip file**

1. Upload the Magento 2 extension files to your server.

2. Extract the .zip extension file.

3. Copy the Magento extension files into the app/code folder.

4. Install the Magento 2 extension and check its status.

php bin/magento setup:upgrade

php bin/magento module:enable Fortvision_Platform`

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy -f

5. Clear Magento cache and disable maintenance mode.

php bin/magento cache:flush


**How To Install a Magento 2 Extension With Composer**

Before we show you how to install a Magento 2 extension via Composer, make sure you:

Back up your server. Set pre-install file permissions. Enable developer mode. Put Magento in maintenance mode.
