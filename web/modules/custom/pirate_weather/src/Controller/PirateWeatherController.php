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
    $forecast = $this->pirateWeatherForecast->getForecast('-20.859444', '-0.624444');
    if (empty($forecast)) {
      throw new NotFoundHttpException('Could not find weather forecast.');
    }

    $build['content'] = [
      '#markup' => $this->t('Currently, the temperature is @temp @units', [
        '@temp' => $forecast['current']['temperature_2m'],
        '@units' => $forecast['current_units']['temperature_2m'],
      ]),
    ];

    return $build;
  }

}
