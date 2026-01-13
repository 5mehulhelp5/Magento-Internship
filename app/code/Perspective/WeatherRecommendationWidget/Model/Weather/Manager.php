<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Model\Weather\Validator;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as CookieManager;
use Perspective\WeatherRecommendationWidget\Model\Weather\WeatherDataCollector;
use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\Manager as Recommender;
use Psr\Log\LoggerInterface;
class Manager
{
    public const WEATHER_COOKIE_NAME = 'widget_weather_data';
    public const RECOMMENDATIONS_COOKIE_NAME = 'widget_recommended_products';

    protected $validator;
    protected $cookieManager;
    protected $weatherDataCollector;
    protected $recommender;
    protected $logger;
    public function __construct(
        Validator $validator,
        CookieManager $cookieManager,
        WeatherDataCollector $weatherDataCollector,
        Recommender $recommender,
        LoggerInterface $logger
    ) {
        $this->validator = $validator;
        $this->cookieManager = $cookieManager;
        $this->weatherDataCollector = $weatherDataCollector;
        $this->recommender = $recommender;
        $this->logger = $logger;
    }

    public function test(): array //rename
    {
        $recommendationData = [
            'weather_data' => [],
            'recommended_skus' => []
        ];

        if ($this->validator->validate()) {
            try {
                //get weather data[city, temperature]
                if (!$this->validator->isCookieSet(self::WEATHER_COOKIE_NAME)) {
                    $weatherData = $this->weatherDataCollector->collectWeatherData();
                    $this->cookieManager->set($weatherData, self::WEATHER_COOKIE_NAME);
                } else {
                    $weatherData = $this->cookieManager->get(self::WEATHER_COOKIE_NAME);
                }

                //get recommendations[sku]
                if (!$this->validator->isCookieSet(self::RECOMMENDATIONS_COOKIE_NAME)) {
                    $recommendedSkus = $this->recommender->getRecommendedSkus($weatherData['temperature']);
                    $this->cookieManager->set($recommendedSkus, self::RECOMMENDATIONS_COOKIE_NAME);
                } else {
                    $recommendedSkus = $this->cookieManager->get(self::RECOMMENDATIONS_COOKIE_NAME);
                }

                $recommendationData['weather_data'] = $weatherData;
                $recommendationData['recommended_skus'] = $recommendedSkus;
            } catch (\Throwable $e) {
                $this->logger->error(sprintf(
                    'Weather widget error: %s in %s on line %d',
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                ));
            }
        }
        return $recommendationData;
    }
}
