**How To Install the Extension With Composer**

Before install of any extension  make sure you:

Back up your server. Set pre-install file permissions. Enable developer mode. Put Magento in maintenance mode.

After that launch from the main magento folder

composer require fortvision/platform

php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento cache:clean

php bin/magento cache:flush



**How To Install an Extension via .zip file**

1. Upload the Magento 2 extension files to your server and extract the .zip extension file.

2. Copy the Magento extension files into the app/code folder. 
The folder with README.md file should be located like that %magentofolder%/app/code/Fortvision/Platform/

3. RUN commands

php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy -f

4. Clear Magento cache and disable maintenance mode.

php bin/magento cache:flush



If you need enable/disable module you should use

Enable the module by running `php bin/magento module:disable Fortvision_Platform` 
or  `php bin/magento module:enable Fortvision_Platform`


If you use magento in docker, don't forget to restart container



**How to use
After installation you should sync orders and products with Fortvision

./bin/magento fortvision:export 


To update script version please use command

composer update fortvision/platform

