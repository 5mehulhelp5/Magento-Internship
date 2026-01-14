<?php

namespace Perspective\CustomPriceAttribute\Model\Attribute\Backend;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;


class CustomPrice extends AbstractBackend
{
    public function beforeSave($object)
    {
        //$object->setData($this->getAttribute()->getName(), 1);

        return $this;
    }
}
