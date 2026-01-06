<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Model\Weather\Validator;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as CookieManager;
use Perspective\WeatherRecommendationWidget\Model\Weather\WeatherDataCollector;
use Perspective\WeatherRecommendationWidget\Model\Weather\Recommendation\Manager as Recommender;
class Manager
{
    public const WEATHER_COOKIE_NAME = 'widget_weather_data';
    public const RECOMMENDATIONS_COOKIE_NAME = 'widget_recommended_products';

    protected $validator;
    protected $cookieManager;
    protected $weatherDataCollector;
    protected $recommender;
    public function __construct(
        Validator $validator,
        CookieManager $cookieManager,
        WeatherDataCollector $weatherDataCollector,
        Recommender $recommender
    ) {
        $this->validator = $validator;
        $this->cookieManager = $cookieManager;
        $this->weatherDataCollector = $weatherDataCollector;
        $this->recommender = $recommender;
    }


    public function test() //rename
    {
        // какой-то глобальный трай?
        $weatherData = [];
        $recommendedSkus = [];
        


        if (!$this->validator->validate()) {
            $test = 1;//exc
        }

        
        if (!$this->validator->isCookieSet(self::WEATHER_COOKIE_NAME)) {
            $weatherData = $this->weatherDataCollector->collectWeatherData();
            $this->cookieManager->set($weatherData, self::WEATHER_COOKIE_NAME);
        } else {
            $weatherData = $this->cookieManager->get(self::WEATHER_COOKIE_NAME);
        }

        //
        
        if (!$this->validator->isCookieSet(self::RECOMMENDATIONS_COOKIE_NAME)) {
            $recommendedSkus = $this->recommender->getRecommendedSkus($weatherData['temperature']);
            $this->cookieManager->set($recommendedSkus, self::RECOMMENDATIONS_COOKIE_NAME);
        } else {
            $recommendedSkus = $this->cookieManager->get(self::RECOMMENDATIONS_COOKIE_NAME);
        }
        
        
        $recommendationData = [
            'weather_data' => $weatherData,
            'recommended_skus' => $recommendedSkus
        ];
        return $recommendationData;



        // глобальный кетч который при любой проблеме обрубит дальнейшее действие?



            



        

        //система эксепшенов на разных уровнях для  возврата пустых ску в виджет на этом уровне
        // получение продукта уже будет в вьюмоделе? виджета
    }
}
