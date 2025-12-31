<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather\Cookie;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Manager
{
    public const COOKIE_NAME = 'weather_data';

    private CookieManagerInterface $cookieManager;
    private CookieMetadataFactory $cookieMetadataFactory;
    private SessionManagerInterface $sessionManager;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
    }

    public function set(array $data, int $duration = 10800): void
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setDuration($duration);

        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, json_encode($data), $metadata);
    }

    public function get(): ?array
    {
        $value = $this->cookieManager->getCookie(self::COOKIE_NAME);
        return $value ? json_decode($value, true) : null;
    }

}
