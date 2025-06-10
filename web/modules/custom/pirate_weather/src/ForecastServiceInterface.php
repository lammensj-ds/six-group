<?php

declare(strict_types=1);

namespace Drupal\pirate_weather;

/**
 * Interface to connect with an external system to fetch weather data.
 */
interface ForecastServiceInterface {

  /**
   * Get the forecast for the next days.
   *
   * @param string $latitude
   *    The latitude of the location.
   * @param string $longitude
   *    The longitude of the location.
   *
   * @return array
   *   Returns the forecast in a structured format.
   */
  public function getForecast(string $latitude, string $longitude): array;

}
