<?php
namespace Perspective\WeatherRecommendationWidget\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param $value
     * @return array
     */
    public function stringToArray($value)
    {
        return array_map('intval', explode(',', $value));
    }
}
