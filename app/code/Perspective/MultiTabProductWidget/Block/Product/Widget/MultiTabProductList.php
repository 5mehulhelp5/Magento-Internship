<?php
namespace Perspective\MultiTabProductWidget\Block\Product\Widget;

use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Rule\Model\Condition\CombineFactory;
use Magento\CatalogRule\Model\Rule as CatalogRule;
use Magento\Catalog\Model\ResourceModel\Product\Collection;



class MultiTabProductList extends ProductsList
{
    protected $combineFactory;
    protected $catalogRule;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        CombineFactory $combineFactory,
        CatalogRule $catalogRule,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data
        );
        $this->combineFactory = $combineFactory;
        $this->catalogRule = $catalogRule;
    }


    protected function getConditions(): \Magento\Rule\Model\Condition\Combine
    {
        $tabId = $this->getData('current_tab_id');

        $conditions = json_decode($this->getData('tab_conditions_' . $tabId), true);

        $postData = ['conditions' => $conditions];
        $this->catalogRule->loadPost($postData);
        return $this->catalogRule->getConditions();
    }


    public function getProductListHtml(string $tabId): string
    {
        //сет таб айди
        $this->setData('current_tab_id', $tabId);

        //формирование коллекции
        $this->setProductCollection($this->createCollection());

        $this->setTemplate('Magento_CatalogWidget::product/widget/content/grid.phtml');
        $this->setData('show_pager', 0);

        return $this->toHtml();
    }


    protected function _beforeToHtml()
    {
        //отложил создание коллекции чтоб сначала мог получить айди таба
        return $this;
    }
}

