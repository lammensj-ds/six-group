<?php

namespace Drupal\pirate_weather\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for pirate_weather.
 */
class PirateWeatherHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'forecast' => [
        'variables' => [
          'today_temp' => '',
          'today_wmo' => '',
          'daily_time' => [],
          'daily_wmo' => [],
          'daily_temp_max' => [],
          'temp_unit' => '',
        ],
      ],
    ];
  }

}
