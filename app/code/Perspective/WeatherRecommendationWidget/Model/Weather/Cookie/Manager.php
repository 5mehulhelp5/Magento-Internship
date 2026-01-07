<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Cookie;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Perspective\WeatherRecommendationWidget\Helper\Config;

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
     * @var Config
     */
    protected $configHelper;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param Config $configHelper
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Config $configHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->configHelper = $configHelper;
    }

    /**
     * Set public cookie with data
     *
     * @param array $data
     * @param string $cookieName
     * @return void
     */
    public function set(array $data, string $cookieName): void
    {
        $duration = $this->configHelper->getCacheTime();
        
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($duration*60);

        $this->cookieManager->setPublicCookie($cookieName, json_encode($data), $metadata);
    }

    /**
     * @param string $cookieName
     *
     * @return array|null
     */
    public function get($cookieName): ?array
    {
        $value = $this->cookieManager->getCookie($cookieName);
        return $value ? json_decode($value, true) : null;
    }

}
