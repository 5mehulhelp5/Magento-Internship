<?php

namespace Perspective\CustomPriceAttribute\Model\Attribute\Backend;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Perspective\CustomPriceAttribute\Service\DefaultCustomPrice as DefaultCustomPriceService;


class CustomPrice extends AbstractBackend
{
    protected $defaultCustomPriceService;
    public function __construct(
        DefaultCustomPriceService $defaultCustomPriceService,
    ) {
        $this->defaultCustomPriceService = $defaultCustomPriceService;
    }

    public function beforeSave($object)
    {
        $allowModify = $object->getData('use_config_custom_price');
        if ($allowModify !== null && (int)$allowModify === 0) {
            $defaultCustomPrice = $this->defaultCustomPriceService->getDefaultCustomPrice($object);
            $object->setData('custom_price', $defaultCustomPrice);
        }
    }
}
//unset if 0
