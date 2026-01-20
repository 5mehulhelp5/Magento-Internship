<?php
namespace Perspective\MultiTabProductWidget\Block\Product\Widget;

use Magento\CatalogWidget\Block\Product\ProductsList;


class MultiTabProductList extends ProductsList
{


    public function getProductListHtml():string
    {

        $conditionsData = $this->getConditions();

        /** @var ProductsList $block */
        $block = $this->getLayout()->createBlock(
            ProductsList::class
        );

        $block->setTemplate('Magento_CatalogWidget::product/widget/content/grid.phtml');
        $block->setData('conditions', []);
        $block->setData('products_count', 20);
        $block->setData('show_pager', 0);

        return $block->toHtml();
    }

}
