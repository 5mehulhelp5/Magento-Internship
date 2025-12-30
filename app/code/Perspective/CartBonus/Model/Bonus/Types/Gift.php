<?php
namespace Perspective\CartBonus\Model\Bonus\Types;
class Gift extends \Perspective\CartBonus\Model\Bonus\AbstractBonus
{
    public const BONUS_CODE = 'gift';
    public const MESSAGE_TEMPLATE = 'Bonus: gift - %s';

    /**
     * {@inheritdoc}
     */
    public function isApplicable($quote, $total): bool
    {
        //if cart rules appled
        if ($this->validationHelper->isCartRulesApplied($quote)) { //якщо винести до менеджера то не спрацює логіка rollback
            return false;
        }

        //if bonus enabled
        if (!$this->isEnabled()){
            return false;
        }

        //if bonus configured
        $config = $this->getConfig();
        $threshold = $config['threshold_min_total'];
        $giftId = $config['select_product'];
        if ($threshold == 0 && $giftId == 0) {
            return false;
        }

        //if totals > configured threshold
        $baseTotal = $total->getBaseTotalAmount('subtotal');
        if ($baseTotal < $threshold) {
            return false;
        }

        //if gift have qty in stock
        if (!$this->validationHelper->isProductSalable($this->dataHelper->getProductSkuById($giftId))) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($quote, $total): array
    {
        $config = $this->getConfig();
        $gift = $this->dataHelper->getProductById($config['select_product']);

        $this->giftManager->addGiftToQuote($quote, $gift);

        $giftName = $gift->getName();
        $frontendMessages[] = sprintf(self::MESSAGE_TEMPLATE, $giftName);

        return [
            'bonus_discount' => 0,
            'bonus_messages' => $frontendMessages
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rollback($quote): void
    {
        $this->giftManager->removeGiftFromQuote($quote);
    }
}
