magento2-tpaycom
======================

tpaycom payment gateway Magento2 extension

**Module was made for educational purposes only, do not use in production! You can read the full article 

Install
=======

1. Go to Magento2 root folder

2. Copy plugin folder  'tpaycom' to app/code

3. Enter following commands to enable module:

    ```bash
    php bin/magento module:enable tpaycom_tpay  
    php bin/magento setup:upgrade
    ```
4. Enable and configure com in Magento Admin under Stores/Configuration/Payment Methods/tpay.com

Other Notes
===========

tpaycom works with PLN only!** If PLN is not your base currency, you will not see this module on checkout pages. 

