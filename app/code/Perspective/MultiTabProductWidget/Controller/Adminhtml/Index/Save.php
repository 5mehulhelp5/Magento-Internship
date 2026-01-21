<?php
namespace Perspective\MultiTabProductWidget\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Perspective\MultiTabProductWidget\Model\Condition as ConditionModel;
use Perspective\MultiTabProductWidget\Model\ResourceModel\Condition as ConditionResourceModel;

class Save extends \Magento\Backend\App\Action
{

    protected $conditionModel;

    protected $conditionResourceModel;

    /**
     * @var Session
     */
    protected $adminsession;


    public function __construct(
        Action\Context $context,
        ConditionModel $conditionModel,
        ConditionResourceModel $conditionResourceModel,
        Session $adminsession
    ) {
        parent::__construct($context);
        $this->conditionModel = $conditionModel;
        $this->conditionResourceModel = $conditionResourceModel;
        $this->adminsession = $adminsession;
    }


    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $condition_id = $this->getRequest()->getParam('condition_id');
            if ($condition_id) {
                $this->conditionResourceModel->load($this->conditionModel, $condition_id);
            }




            $data['conditions'] = json_encode($data['rule']['conditions']);
            unset($data['rule']);

            $model = $this->conditionModel->setData($data);

            try {
                $this->conditionResourceModel->save($model);
                $this->messageManager->addSuccessMessage(__('The data has been saved.'));
                $this->adminsession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    if ($this->getRequest()->getParam('back') == 'add') {
                        return $resultRedirect->setPath('*/*/add');
                    }
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the data.'));
            }

            $this->_getSession()->setFormData($data);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
