<?php
namespace MilanDev\GiftProduct\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class CouponManagement extends \Magento\Quote\Model\CouponManagement
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($quoteRepository);
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode)
    {
        $couponCode = trim($couponCode);
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            
            $quote->setCouponCode($couponCode);
            $this->quoteRepository->save($quote->collectTotals());

            // start auto add gift
            if($quote->getCouponCode()){
                $this->objectManager->get(\MilanDev\GiftProduct\Helper\Gifts::class)->addGiftSku($couponCode);    
            }
            // end auto add gift

        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply coupon code'));
        }
        if ($quote->getCouponCode() != $couponCode) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $oldCouponCode = $quote->getCouponCode();
            $quote->setCouponCode('');
            $this->quoteRepository->save($quote->collectTotals());

            // start auto remove gift
            $this->objectManager->get(\MilanDev\GiftProduct\Helper\Gifts::class)->removeGiftSku($oldCouponCode);
            // end auto remove gift
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        return true;
    }
}
