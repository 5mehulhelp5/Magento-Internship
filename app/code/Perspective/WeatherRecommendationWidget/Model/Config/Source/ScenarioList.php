<?php

namespace Perspective\WeatherRecommendationWidget\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Perspective\WeatherRecommendationWidget\Service\Weather\GetConfigData as WeatherConfigService;
class ScenarioList implements OptionSourceInterface
{
    /**
     * @var WeatherConfigService
     */
    protected $weatherConfigService;

    /**
     * @param WeatherConfigService $weatherConfigService
     */
    public function __construct(
        WeatherConfigService $weatherConfigService
    ) {
        $this->weatherConfigService = $weatherConfigService;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return $this->weatherConfigService->getScenarioList();
    }
}
