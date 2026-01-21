<?php
declare(strict_types=1);

namespace Perspective\MultiTabProductWidget\Block\Adminhtml\Condition\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Rule\Block\Conditions as RuleConditions;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * A safe CatalogRule-like Conditions block which will create/load a rule
 * via factory if registry doesn't contain it (prevents get* on null).
 *
 * Minimal, intentionally close to core CatalogRule block but with fallback.
 */
class CatalogConditions extends Generic implements TabInterface
{
    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var RuleConditions
     */
    protected $_conditions;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * CatalogConditions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param RuleConditions $conditions
     * @param Fieldset $rendererFieldset
     * @param array $data
     * @param RuleFactory|null $ruleFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        RuleConditions $conditions,
        Fieldset $rendererFieldset,
        array $data = [],
        RuleFactory $ruleFactory = null
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_conditions = $conditions;
        $this->ruleFactory = $ruleFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(RuleFactory::class);
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * Show tab?
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Hidden?
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string|null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Tab url (not used)
     *
     * @return string|null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Ajax loaded?
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Prepare form before render
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        // Try to get existing catalog rule from registry (core behavior)
        $model = $this->_coreRegistry->registry('current_promo_catalog_rule');

        // If not present, try to create/load via factory (SalesRule-like fallback)
        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            $model = $this->ruleFactory->create();
            if ($id) {
                $model->load($id);
            }
        }

        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Adds 'Conditions' to the form.
     *
     * @param \Magento\CatalogRule\Api\Data\RuleInterface|\Magento\CatalogRule\Model\Rule $model
     * @param string $fieldsetId
     * @param string $formName
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'perspective_widget_conditions_form')
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        // model is guaranteed to be an object here (factory fallback)
        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl(
            'catalog_rule/promo_catalog/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => $formName]
        );

        $renderer = $this->getLayout()->createBlock(Fieldset::class);
        $renderer->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            ['legend' => __('Conditions (don\'t add conditions if rule is applied to all products)')]
        )->setRenderer($renderer);

        $fieldset->addField(
            'conditions_display',
            'text',
            [
                'name' => 'conditions_display',
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => $formName
            ]
        )
            ->setRule($model)
            ->setRenderer($this->_conditions);

        $fieldset->addField(
            'conditions',
            'hidden',
            [
                'name' => 'conditions',
                'data-form-part' => $formName,
                'value' => $model->getConditionsSerialized(),
            ]
        );




        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);
        return $form;
    }

    /**
     * Set form name and js form object on condition and its children.
     *
     * @param AbstractCondition $conditions
     * @param string $formName
     * @param string $jsFormName
     * @return void
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $jsFormName)
    {
        $conditions->setFormName($formName);
        $conditions->setJsFormObject($jsFormName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName, $jsFormName);
            }
        }
    }
}
