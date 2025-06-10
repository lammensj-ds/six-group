<?php

declare(strict_types=1);

namespace Drupal\pirate_weather\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\pirate_weather\ForecastServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a weather forecast block.
 */
#[Block(
  id: 'pirate_weather_weather_forecast',
  admin_label: new TranslatableMarkup('Weather Forecast'),
  category: new TranslatableMarkup('Pirate Weather'),
)]
final class WeatherForecastBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected readonly ForecastServiceInterface $pirateWeatherForecast,
    protected readonly LoggerChannelFactoryInterface $logger,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('pirate_weather.services.forecast'),
      $container->get('logger.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'latitude' => '37.859444',
      'longitude' => '20.624444',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['latitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->configuration['latitude'],
      '#required' => TRUE,
    ];
    $form['longitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->configuration['longitude'],
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['latitude'] = $form_state->getValue('latitude');
    $this->configuration['longitude'] = $form_state->getValue('longitude');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $forecast = $this->pirateWeatherForecast->getForecast($this->configuration['latitude'], $this->configuration['longitude']);

    $build['content'] = [
      '#markup' => $this->t('Currently, the temperature is @temp @units', [
        '@temp' => $forecast['current']['temperature_2m'],
        '@units' => $forecast['current_units']['temperature_2m'],
      ]),
    ];

    return $build;
  }

}
