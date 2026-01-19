<?php
namespace Perspective\WeatherInsurance\Controller\Quote;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;

class Save implements HttpPostActionInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        JsonFactory $resultJsonFactory,
        RequestInterface $request
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
    }

    /**
     * Get checkbox value and set to quote
     *
     * @return ResponseInterface|Json|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $insuranceCheckboxValue = (int)$this->request->getParam('insurance_checkbox_state');

        $quote = $this->checkoutSession->getQuote();

        $quote->setData('delivery_insurance', $insuranceCheckboxValue);
        $this->quoteRepository->save($quote);

        return $this->resultJsonFactory->create()->setData(['success' => true]);
    }
}
