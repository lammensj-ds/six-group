<?php

declare(strict_types=1);

namespace Drupal\pirate_weather;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Implements the Open Meteo API for a weather forecast.
 */
final class ForecastService implements ForecastServiceInterface {

  protected const string BASE_URL = 'https://api.open-meteo.com/v1/forecast';

  /**
   * Constructs a ForecastService object.
   */
  public function __construct(
    protected readonly Client $httpClient,
    protected readonly LoggerChannelFactoryInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getForecast(string $latitude, string $longitude): array {
    $url = Url::fromUri(self::BASE_URL);
    $url->setOption('query', [
      'latitude' => $latitude,
      'longitude' => $longitude,
      'current' => implode(',', ['weather_code', 'temperature_2m', 'is_day']),
      'daily' => implode(',', ['weather_code', 'sunrise', 'sunset', 'temperature_2m_max', 'temperature_2m_min']),
      'models' => 'best_match',
      'timezone' => 'Europe/Berlin',
    ]);

    try {
      $response = $this->httpClient->get($url->toString());
    }
    catch (ClientException $e) {
      $this->logger->get('pirate_weather')->error($e->getMessage());

      return [];
    }

    return Json::decode((string) $response->getBody());
  }

}
