<?php
namespace Perspective\WeatherRecommendationWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Perspective\WeatherRecommendationWidget\Model\Weather\Manager;

class RecommendationWidget extends Template
{
   
    protected $productCollectionFactory;

    protected $recommendationManager;

    protected $recommendationData = null;

    protected $_template = 'Perspective_WeatherRecommendationWidget::widget/recommendation.phtml';

    public function __construct(
        Context $context,
        Manager $recommendationManager,
        ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->recommendationManager = $recommendationManager;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

 
    public function getRecommendationData()
    {
        if ($this->recommendationData !== null) {
            return $this->recommendationData;
        }

        $this->recommendationData = $this->recommendationManager->test();

        return $this->recommendationData;
    }

    
    public function isVisible(): bool
    {
        $data = $this->getRecommendationData();
        return !empty($data['recommended_skus']);
    }


    public function getProductCollection()
    {
        $data = $this->getRecommendationData();
        $skus = $data['recommended_skus'];
        
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                   ->addFieldToFilter('sku', ['in' => $skus])
                   ->setPageSize(count($skus)); //

        return $collection;
    }


    public function getProductListHtml()
    {
        $collection = $this->getProductCollection();

        $toolbar = $this->getLayout()->createBlock(\Magento\Catalog\Block\Product\ProductList\Toolbar::class)
            ->setCollection($collection);

        // переделать под вариант и
        $listBlock = $this->getLayout()->createBlock(\Magento\Catalog\Block\Product\ListProduct::class)
            ->setCollection($collection)
            ->setChild('toolbar', $toolbar)
            ->setTemplate('Magento_Catalog::product/list.phtml');

        $listBlock->setData('viewModel', new \Magento\Catalog\ViewModel\Product\OptionsData());

        return $listBlock->toHtml();
    }

    protected function _toHtml()//перейти на isVisible а не через это
    {
        if (!$this->isVisible()) {
            return '';
        }
        return parent::_toHtml();
    }
}
