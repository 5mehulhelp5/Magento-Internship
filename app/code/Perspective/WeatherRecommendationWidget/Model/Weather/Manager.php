<?php
namespace Perspective\WeatherRecommendationWidget\Model\Weather;
use Perspective\WeatherRecommendationWidget\Model\Weather\Validator;
use Perspective\WeatherRecommendationWidget\Model\Weather\Cookie\Manager as CookieManager;
use Perspective\WeatherRecommendationWidget\Model\Weather\WeatherDataCollector;
class Manager
{
    protected $validator;
    protected $cookieManager;
    protected $weatherDataCollector;
    public function __construct(
        Validator $validator,
        CookieManager $cookieManager,
        WeatherDataCollector $weatherDataCollector
    ) {
        $this->validator = $validator;
        $this->cookieManager = $cookieManager;
        $this->weatherDataCollector = $weatherDataCollector;
    }


    public function test()
    {
        if (!$this->validator->validate()) {

        }
        if (!$this->validator->hasWeatherData()) {
            $weatherData = $this->weatherDataCollector->collectWeatherData();
            $this->cookieManager->set($weatherData);
        }
    }
}
