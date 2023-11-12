Fortvision platform extension
# 

@- Enable the module by running `php bin/magento module:enable Fortvision_Platform`


php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy -f

php bin/magento cache:flush
