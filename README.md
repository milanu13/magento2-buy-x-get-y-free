# Magento 2 - Buy X Get Y Free
The module allows you to add a gift product automatically to cart when a specific coupon code is applied.

## Requirements
This plugin supports Magento2.2.x version or higher.

## Installation
To install this module, copy the module folder in the <strong>magento_root/app/code</strong> directory and run the following commands:
```
bin/magento module:enable MilanDev_GiftProduct
bin/magento setup:upgrade
bin/magento setup:di:compile
```
## Configurations
1 ) Please add a new rule with coupon code by following the path below and fill up the required fields.
```
Marketing-->Promotion-->Cart Price Rules
```
2 ) Create a new <strong>simple product with zero (0) price.</strong>

3 ) Set the SKU and coupon code in the following path:
```
Stores-->Configuration-->Milandev-->Gift Product
```
## License
Free as in Freedom.