<?php

namespace Perspective\WeatherRecommendationWidget\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Customer\Model\SessionFactory;
use Perspective\WeatherRecommendationWidget\Model\Weather\Manager;

class Test implements ObserverInterface
{
    protected $curl;
    protected $customerSession;
    protected $remoteAddress;
    protected $sessionFactory;
    protected $weatherManager;

    public function __construct(
        Curl $curl,
        CustomerSession $customerSession,
        RemoteAddress $remoteAddress,
        SessionFactory $sessionFactory,
        Manager $weatherManager
    ) {
        $this->curl = $curl;
        $this->customerSession = $customerSession;
        $this->remoteAddress = $remoteAddress;
        $this->sessionFactory = $sessionFactory;
        $this->weatherManager = $weatherManager;
    }

    public function execute(Observer $observer)
    {

        $action = $observer->getEvent()->getControllerAction();
        $fullActionName = $action->getRequest()->getFullActionName();

        if ($fullActionName == 'cms_index_index') {

            $this->weatherManager->test();
            $aa = 2;

            return;
        }
    }
}
