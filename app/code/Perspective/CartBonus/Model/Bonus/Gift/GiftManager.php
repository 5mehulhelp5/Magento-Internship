<?php
namespace Perspective\CartBonus\Model\Bonus\Gift;

use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\ItemFactory;
class GiftManager
{
    protected $itemFactory;

    public function __construct(
        ItemFactory $itemFactory
    ) {
        $this->itemFactory = $itemFactory;
    }

    private function getGiftItemId(Quote $quote) //кеширование добавить?
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

    private function isGiftAlreadyAdded(Quote $quote): bool
    {
        $giftId = $this->getGiftItemId($quote);
        if ($giftId !== null) {
            return true;
        }
        return false;
    }

    public function addGiftToQuote(Quote $quote, Product $product): void
    {
        if(!$this->isGiftAlreadyAdded($quote, $product)) {
            $this->createGiftItem($quote, $product);
        } else {
            $giftId = $this->getGiftItemId($quote);
            $quote->getItemById($giftId)->setQty(1);
        }
    }

    public function removeGiftFromQuote(Quote $quote): void
    {
        $giftId = $this->getGiftItemId($quote);
        if ($giftId !== null) {
            $quote->removeItem($giftId);
        }
    }

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
