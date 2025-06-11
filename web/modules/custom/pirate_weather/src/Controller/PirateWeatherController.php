<?php

declare(strict_types=1);

namespace Drupal\pirate_weather\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\pirate_weather\ForecastServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Pirate Weather routes.
 */
final class PirateWeatherController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    protected readonly ForecastServiceInterface $pirateWeatherForecast,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('pirate_weather.services.forecast'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $forecast = $this->pirateWeatherForecast->getForecast('51.196', '4.408');
    if (empty($forecast)) {
      throw new NotFoundHttpException('Could not find weather forecast.');
    }

    $build['content'] = [
      '#theme' => 'forecast',
      '#today_temp' => $forecast['current']['temperature_2m'],
      '#today_wmo' => $forecast['current']['weather_code'],
      '#daily_time' => $forecast['daily']['time'],
      '#daily_wmo' => $forecast['daily']['weather_code'],
      '#daily_temp_max' => $forecast['daily']['temperature_2m_max'],
      '#temp_unit' => $forecast['current_units']['temperature_2m'],
    ];

    return $build;
  }

}
