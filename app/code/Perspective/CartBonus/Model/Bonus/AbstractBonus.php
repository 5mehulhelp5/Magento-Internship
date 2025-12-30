<?php
namespace Perspective\CartBonus\Model\Bonus;
use Perspective\CartBonus\Helper\Validation;
use Perspective\CartBonus\Helper\Data;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Perspective\CartBonus\Model\Bonus\Gift\GiftManager;
abstract class AbstractBonus
{

    /**
     * code from system config
     */
    public const BONUS_CODE = '';
    /**
     * message template for frontend totals
     */
    public const MESSAGE_TEMPLATE = '';

    public const BONUS_TOTAL_CODE = 'bonus_total';
    /**
     * @var Validation
     */
    protected $validationHelper;
    /**
     * @var Data
     */
    protected $dataHelper;
    /**
     * @var GiftManager
     */
    protected $giftManager;

    /**
     * @param Validation $validationHelper
     * @param Data $dataHelper
     * @param GiftManager $giftManager
     */
    public function __construct(
        Validation $validationHelper,
        Data $dataHelper,
        GiftManager $giftManager
    ) {
        $this->validationHelper = $validationHelper;
        $this->dataHelper = $dataHelper;
        $this->giftManager = $giftManager;
    }

    /**
     * Check if bonus can be applied to the cart
     *
     * @param CartInterface $quote
     * @param Total $total
     * @return bool
     */
    abstract public function isApplicable(CartInterface $quote, Total $total): bool;

    /**
     * Applies the bonus to the cart
     *
     * @param CartInterface $quote
     * @param Total $total
     * @return array  Result data for frontend (bonus amount, messages)
     */
    abstract public function apply(CartInterface $quote, Total $total): array;

    /**
     * Rolls back the bonus effects in the cart.
     *
     * @param CartInterface $quote
     * @return void
     */
    public function rollback(CartInterface $quote): void
    {
        //if need custom rollback logic(example: gift bonus)
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return static::BONUS_CODE;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->validationHelper->getBonusConfig($this->getCode());
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->validationHelper->isBonusEnabled($this->getCode());
    }

}
