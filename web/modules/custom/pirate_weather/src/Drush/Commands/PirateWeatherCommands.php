<?php

namespace Drupal\pirate_weather\Drush\Commands;

use Drupal\pirate_weather\ForecastServiceInterface;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\VarDumper\VarDumper;

/**
 * A Drush commandfile.
 */
class PirateWeatherCommands extends DrushCommands {

  use AutowireTrait;

  /**
   * Constructs a PirateWeatherCommands object.
   */
  public function __construct(
    #[Autowire('pirate_weather.services.forecast')]
    protected readonly ForecastServiceInterface $pirateWeatherServicesForecast,
  ) {
    parent::__construct();
  }

  /**
   * Get weather forecast.
   */
  #[CLI\Command(name: 'pirate-weather:forecast')]
  #[CLI\Argument(name: 'latitude', description: 'The latitude')]
  #[CLI\Argument(name: 'longitude', description: 'The longitude')]
  public function forecast(string $latitude, string $longitude): void {
    $forecast = $this->pirateWeatherServicesForecast->getForecast($latitude, $longitude);

    $this->logger()->success(dt('Currently, the temperature is @temp @units', [
      '@temp' => $forecast['current']['temperature_2m'],
      '@units' => $forecast['current_units']['temperature_2m'],
    ]));
  }

}
