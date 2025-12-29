<?php
namespace Perspective\CartBonus\Model\Bonus;
use Magento\Quote\Api\Data\CartInterface;
use Perspective\CartBonus\Helper\Validation;
use Magento\Quote\Model\Quote\Address\Total;
class Manager
{
    protected $validationHelper;

    /** @var \Perspective\CartBonus\Model\Bonus\AbstractBonus[] */
    private array $bonuses;

    public function __construct(
        Validation $validationHelper,
        array $bonuses = []
    ) {
        $this->validationHelper = $validationHelper;
        $this->bonuses = $bonuses;
    }

    public function test (CartInterface $quote, Total $total)
    {
        $result = [
            'bonus_discount' => 0,
            'bonus_messages' => []
        ];

        // if module !enabled
        if (!$this->validationHelper->isModuleEnabled()) {
            return $result;
        }

        //if totals summoned without quote
        $items = $quote->getItems();
        if (!$items) {
            return false;
        }

        foreach ($this->bonuses as $bonus) {
            if ($bonus->isApplicable($quote, $total)) {
                $bonusResult = $bonus->apply($quote, $total);
                if (isset($bonusResult['bonus_discount'])) {
                    $result['bonus_discount'] += $bonusResult['bonus_discount'];
                    $result['bonus_messages'] = array_merge(
                        $result['bonus_messages'],
                        $bonusResult['bonus_messages']
                    );
                }
            } else {
                $bonus->rollback($quote);
            }
        }
        return $result;
    }
}
