<?php

namespace Drupal\pirate_weather\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Defines Twig extensions.
 */
class PirateWeatherTwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFilters(): array {
    return [
      'wmo_trans' => new TwigFilter('wmo_trans', $this->translateWmoCode(...)),
    ];
  }

  /**
   * Translates a given code to a human-readable explanation.
   *
   * @param string $code
   *   The WMO code.
   *
   * @return string
   *   The translation.
   */
  public function translateWmoCode(string $code): string {
    return match ($code) {
      '0' => t('Clear sky'),
      '1', '2', '3' => t('Mainly clear, partly cloudy, and overcast'),
      '45', '48' => t('Fog and depositing rime fog'),
      '51', '53', '55' => t('Drizzle: Light, moderate, and dense intensity'),
      '56', '57' => t('Freezing Drizzle: Light and dense intensity'),
      '61', '63', '65' => t('Rain: Slight, moderate and heavy intensity'),
      '66', '67' => t('Freezing Rain: Light and heavy intensity'),
      '71', '73', '75' => t('Snow fall: Slight, moderate, and heavy intensity'),
      '77' => t('Snow grains'),
      '80', '81', '82' => t('Rain showers: Slight, moderate, and violent'),
      '85', '86' => t('Snow showers slight and heavy'),
      '95' => t('Thunderstorm: Slight or moderate'),
      '96', '99' => t('Thunderstorm with slight and heavy hail'),
      default => t('Could not translate code @code', ['@code' => $code]),
    };
  }

}
