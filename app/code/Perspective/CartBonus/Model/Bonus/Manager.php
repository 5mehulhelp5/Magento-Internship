<?php
namespace Perspective\CartBonus\Model\Bonus;
use Magento\Quote\Api\Data\CartInterface;
use Perspective\CartBonus\Helper\Validation;
use Magento\Quote\Model\Quote\Address\Total;
class Manager
{
    /**
     * @var Validation
     */
    protected $validationHelper;
    /** @var \Perspective\CartBonus\Model\Bonus\AbstractBonus[] */
    private array $bonuses;

    /**
     * @param Validation $validationHelper
     * @param array $bonuses
     */
    public function __construct(
        Validation $validationHelper,
        array $bonuses = []
    ) {
        $this->validationHelper = $validationHelper;
        $this->bonuses = $bonuses;
    }

    /**
     *  Applies all applicable bonuses to the given cart quote and totals.
     *
     *  For each bonus:
     *  - Checks if it is applicable.
     *  - Applies it and collects bonus discount and messages.
     *  - Rolls back the bonus if not applicable.
     *
     * @param CartInterface $quote
     * @param Total $total
     * @return array
     */
    public function applyBonuses (CartInterface $quote, Total $total): array
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
            return $result;
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
