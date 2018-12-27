<?php
namespace MilanDev\GiftProduct\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Gifts extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Cart $cart
        
    ) {
        
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
        $this->product = $product;
        $this->cart = $cart;

    }

    /**
     * Check if the module is enabled or not
     * 
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('giftproduct/giftproductgroup/enabled') ? 1 : 0;
    }

    /**
     * Get product id by sku
     * 
     * @return mix
     */
    public function getGiftSkuId() {
        $sku = $this->scopeConfig->getValue('giftproduct/giftproductgroup/sku');
        $_product = $this->product->loadByAttribute('sku', $sku);
        return ($sku && $_product->getId()) ? $_product->getId() : 0;
    }

    /**
     * Get coupon code from system config
     * 
     * @return mix
     */
    public function getGiftCouponCode()
    {
        $couponCode = $this->scopeConfig->getValue('giftproduct/giftproductgroup/coupon_code');
        return $couponCode ? strtolower($couponCode) : 0;
    }
    
    /**
     * Add gift product to cart
     * 
     * @return void
     */
    public function addGiftSku($couponCode) {
        
        if($this->isEnabled() && ($this->getGiftCouponCode()==strtolower($couponCode))){
            $productId = $this->getGiftSkuId();
            $_product = $this->product->load($productId);

            $params = array(
                'product' => $productId, 
                'qty'   => 1                
            );            

            if(!$this->cart->getQuote()->hasProductId($productId)){
                $this->cart->addProduct($_product, $params);
                $this->cart->save();
            }
        }
    }

    /**
     * Remove gift product from cart
     *
     * @return void
     */
    public function removeGiftSku($oldCouponCode) {
        $productId = $this->getGiftSkuId();
        
        if($this->isEnabled() && ($this->getGiftCouponCode()==strtolower($oldCouponCode))){
            $allItems = $this->cart->getQuote()->getAllVisibleItems();
            foreach ($allItems as $item) {
                $itemId = $item->getItemId();
                $itemProdId = $item->getProduct()->getId();
                if($itemProdId == $productId){
                    $this->cart->removeItem($itemId)->save();
                }
            }
        }
    }
}
