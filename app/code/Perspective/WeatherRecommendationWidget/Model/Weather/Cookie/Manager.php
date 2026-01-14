<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Cookie;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Perspective\WeatherRecommendationWidget\Service\Weather\GetConfigData as WeatherConfigService;

class Manager
{
    public const COOKIE_NAME = 'weather_data';

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;
    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;
    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;
    /**
     * @var WeatherConfigService
     */
    protected $weatherConfigService;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param WeatherConfigService $weatherConfigService
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        WeatherConfigService $weatherConfigService
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->weatherConfigService = $weatherConfigService;
    }

    /**
     * Set public cookie with data
     *
     * @param array $data
     * @param string $cookieName
     * @return void
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function set(array $data, string $cookieName): void
    {
        $duration = $this->weatherConfigService->getCacheTime();

        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($duration*60);

        $this->cookieManager->setPublicCookie($cookieName, json_encode($data), $metadata);
    }

    /**
     * @param string $cookieName
     * @return array|null
     */
    public function get(string $cookieName): ?array
    {
        $value = $this->cookieManager->getCookie($cookieName);
        return $value ? json_decode($value, true) : null;
    }

}
