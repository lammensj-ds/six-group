<?php

declare(strict_types=1);

namespace Drupal\Tests\pirate_weather\Kernel;

use Drupal\KernelTests\KernelTestBase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\StreamInterface;
use Drupal\pirate_weather\ForecastService;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;

/**
 * Tests the ForecastService in a kernel environment.
 */
final class ForecastServiceTest extends KernelTestBase {

  protected Client $httpClient;

  protected LoggerChannelFactoryInterface $loggerFactory;

  protected LoggerChannelInterface $loggerChannel;

  protected ForecastService $forecastService;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['pirate_weather'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['pirate_weather']);

    $this->httpClient = $this->createMock(Client::class);
    $this->loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $this->loggerChannel = $this->createMock(LoggerChannelInterface::class);

    $this->loggerFactory->method('get')
      ->with('pirate_weather')
      ->willReturn($this->loggerChannel);

    $this->forecastService = new ForecastService(
      $this->httpClient,
      $this->loggerFactory,
    );
  }

  /**
   * Tests getForecast with a successful API response.
   */
  public function testGetForecastSuccess(): void {
    $latitude = '52.52';
    $longitude = '13.41';
    $expectedData = [
      'current' => [
        'temperature_2m' => 15.0,
        'weather_code' => 3,
        'is_day' => 1,
      ],
      'daily' => [
        'temperature_2m_max' => [20.0],
        'temperature_2m_min' => [10.0],
        'sunrise' => ['2025-06-03T05:00'],
        'sunset' => ['2025-06-03T21:00'],
        'weather_code' => [2],
      ],
    ];

    $mockBody = $this->createMock(StreamInterface::class);
    $mockBody->method('__toString')->willReturn(json_encode($expectedData));

    $this->httpClient->method('get')
      ->willReturn(new Response(200, [], $mockBody));

    $result = $this->forecastService->getForecast($latitude, $longitude);

    $this->assertEquals($expectedData, $result);
  }

  /**
   * Tests getForecast when a ClientException occurs.
   */
  public function testGetForecastClientException(): void {
    $latitude = '52.52';
    $longitude = '13.41';
    $exceptionMessage = 'API request failed.';

    $this->httpClient->method('get')
      ->willThrowException(new ClientException(
        $exceptionMessage,
        new Request('GET', 'http://example.com'),
        new Response(500)
      ));

    $this->loggerChannel->expects($this->once())
      ->method('error')
      ->with($exceptionMessage);

    $result = $this->forecastService->getForecast($latitude, $longitude);

    $this->assertEquals([], $result);
  }

  /**
   * Tests getForecast with an empty API response.
   */
  public function testGetForecastEmptyResponse(): void {
    $latitude = '52.52';
    $longitude = '13.41';

    $mockBody = $this->createMock(StreamInterface::class);
    $mockBody->method('__toString')->willReturn('{}');

    $this->httpClient->method('get')
      ->willReturn(new Response(200, [], $mockBody));

    $result = $this->forecastService->getForecast($latitude, $longitude);

    $this->assertEquals([], $result);
  }

  /**
   * Tests getForecast with an invalid JSON API response.
   */
  public function testGetForecastInvalidJsonResponse(): void {
    $latitude = '52.52';
    $longitude = '13.41';
    $invalidJson = '{"current": "invalid json';

    $mockBody = $this->createMock(StreamInterface::class);
    $mockBody->method('__toString')->willReturn($invalidJson);

    $this->httpClient->method('get')
      ->willReturn(new Response(400, [], $mockBody));

    $this->loggerChannel->expects($this->once())
      ->method('error')
      ->with($this->stringContains('Syntax error'));

    $result = $this->forecastService->getForecast($latitude, $longitude);

    $this->assertEquals([], $result);
  }

}
