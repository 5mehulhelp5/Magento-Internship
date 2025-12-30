<?php
namespace Perspective\CartBonus\Model\Bonus\Gift;

use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\ItemFactory;
class GiftManager
{
    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @param ItemFactory $itemFactory
     */
    public function __construct(
        ItemFactory $itemFactory
    ) {
        $this->itemFactory = $itemFactory;
    }

    /**
     * @param Quote $quote
     * @return mixed|null
     */
    private function getGiftItemId(Quote $quote)
    {
        $giftId = null;
        foreach ($quote->getAllItems() as $item) {
            /* @var \Magento\Quote\Model\Quote\Item $item*/
            $additionalData = $item->getAdditionalData();
            if ($additionalData === 'is_gift') {
                $giftId = $item->getId();
                break;
            }
        }
        return $giftId;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    private function isGiftAlreadyAdded(Quote $quote): bool
    {
        $giftId = $this->getGiftItemId($quote);
        if ($giftId !== null) {
            return true;
        }
        return false;
    }

    /**
     * @param Quote $quote
     * @param Product $product
     * @return void
     */
    public function addGiftToQuote(Quote $quote, Product $product): void
    {
        if(!$this->isGiftAlreadyAdded($quote)) {
            $this->createGiftItem($quote, $product);
        } else {
            $giftId = $this->getGiftItemId($quote);
            $quote->getItemById($giftId)->setQty(1);
        }
    }

    /**
     * @param Quote $quote
     * @return void
     */
    public function removeGiftFromQuote(Quote $quote): void
    {
        $giftId = $this->getGiftItemId($quote);
        if ($giftId !== null) {
            $quote->removeItem($giftId);
        }
    }

    /**
     * @param Quote $quote
     * @param Product $product
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createGiftItem(Quote $quote, Product $product): void
    {
        $quoteItem = $this->itemFactory->create();
        $quoteItem->setQuote($quote);
        $quoteItem->setProduct($product);
        $quoteItem->setQty(1);
        $quoteItem->setCustomPrice(0);
        $quoteItem->setOriginalCustomPrice(0);
        $quoteItem->setAdditionalData('is_gift');
        $quoteItem->getProduct()->setIsSuperMode(true);

        $quote->addItem($quoteItem);
    }
}
