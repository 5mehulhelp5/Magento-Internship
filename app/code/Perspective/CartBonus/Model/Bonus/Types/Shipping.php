<?php
namespace Perspective\CartBonus\Model\Bonus\Types;
class Shipping extends \Perspective\CartBonus\Model\Bonus\AbstractBonus
{
    public const BONUS_CODE = "shipping";
    public const MESSAGE_TEMPLATE = 'Bonus: %d%% discount for shipping';

    /**
     * {@inheritdoc}
     */
    public function isApplicable($quote, $total): bool
    {
        //if cart rules applied
        if ($this->validationHelper->isCartRulesApplied($quote)) {
            return false;
        }

        //if bonus enabled
        if (!$this->isEnabled()){
            return false;
        }

        //if bonus configured
        $config = $this->getConfig();
        $firstThreshold = $config['first_threshold_min_total'];
        $firstDiscount  = $config['first_threshold_discount_value'];
        $secondThreshold = $config['second_threshold_min_total'];
        $secondDiscount  = $config['second_threshold_discount_value'];
        if ($firstThreshold == 0 && $firstDiscount == 0 && $secondThreshold == 0 && $secondDiscount == 0) {
            return false;
        }

        //if totals > configured threshold
        $baseTotal = $total->getBaseTotalAmount('subtotal');
        if ($baseTotal < min($firstThreshold, $secondThreshold)) {
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
        $firstThreshold = $config['first_threshold_min_total'];
        $firstDiscount  = $config['first_threshold_discount_value'];
        $secondThreshold = $config['second_threshold_min_total'];
        $secondDiscount  = $config['second_threshold_discount_value'];
        $baseTotal = $total->getBaseTotalAmount('subtotal');

        //select discount
        if ($baseTotal < max($firstThreshold, $secondThreshold)) {
            $discount = min($firstDiscount, $secondDiscount);
        } else {
            $discount = max($firstDiscount, $secondDiscount);
        }

        //get shipping price
        $baseShippingAmount = $total->getBaseShippingAmount();
        //calculate shipping discount
        $discountAmount = $baseShippingAmount * ($discount / 100);

        //apply discount to bonus total
        $total->addTotalAmount($this::BONUS_TOTAL_CODE, -$discountAmount);
        $total->addBaseTotalAmount($this::BONUS_TOTAL_CODE, -$discountAmount);

        ////message for frontend totals
        $frontendMessages[] = sprintf(self::MESSAGE_TEMPLATE, $discount);

        return [
            'bonus_discount' => $discountAmount,
            'bonus_messages' => $frontendMessages
        ];
    }
}
