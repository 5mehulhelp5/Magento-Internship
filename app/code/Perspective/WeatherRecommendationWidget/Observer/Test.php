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

            $test = $this->getData();
            $a = 1;


            //для теста погодного сервиса надо ждать активацию ключа
            $apiKey = '1dfbb5a96939f3281f5b9f5194e501c9';
            $url = "https://api.openweathermap.org/data/2.5/weather?lat={$test['latitude']}&lon={$test['longitude']}&appid={$apiKey}";
            $this->curl->get($url);
            $testik = json_decode($this->curl->getBody(), true);

            $temp = $testik['main']['temp'] - 273.15;


            $userIp = $this->remoteAddress->getRemoteAddress();

            $this->weatherManager->test();
            $cooTest = $_COOKIE;

            $aa = 2;



            return;
        }
    }

    public function getData($ip = '176.105.211.6')
    {
        $url = 'https://ipwho.is/' . $ip . '?fields=city,latitude,longitude';
        $this->curl->get($url);
        return json_decode($this->curl->getBody(), true);
    }
}
