<?php
namespace Perspective\CustomPriceAttribute\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class CustomPrice extends AbstractModifier
{
    private ArrayManager $arrayManager;

    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    public function modifyData(array $data): array
    {
        return $data;
    }

    public function modifyMeta(array $meta): array
    {


        return $meta;
    }
}
