<?php

namespace Drupal\oist_campus_events\Services;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Service responsible to fetch posts from 3rd party API.
 *
 * @package Drupal\oist_campus_events\Services
 */
class FetchEventsService {

  /**
   * The HTTP client to fetch the posts.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected ClientInterface $httpClient;

  /**
   * Serialization for JSON.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected Json $json;

  /**
   * Logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $loggerChannelFactory;

  /**
   * FetchPostsService constructor.
   *
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   The HTTP client to fetch the posts.
   * @param \Drupal\Component\Serialization\Json $json
   *   Serialization for JSON.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger channel.
   */
  public function __construct(
    ClientInterface $httpClient,
    Json $json,
    LoggerChannelFactoryInterface $loggerChannelFactory
  ) {

    $this->httpClient = $httpClient;
    $this->json = $json;
    $this->loggerChannelFactory = $loggerChannelFactory->get('dependency_injection_exercise');
  }

  /**
   * Returns the default http client.
   *
   * @return \GuzzleHttp\Client
   *   A guzzle http client instance.
   */
  protected function httpClient(): ClientInterface {
    return $this->httpClient;
  }

  /**
   * Fetch posts from 3rd party API.
   *
   * @param int $count
   *
   * @return array
   *   Posts data array.
   *
   */
  public function getFeeds(int $count = NULL): mixed {
    // Try to obtain the post data via the external API.
    try {
      $response = $this->httpClient()->request('GET', 'https://groups.oist.jp/api/infoscreen/all');
      $raw_data = $response->getBody()->getContents();
      $data = $this->json->decode($raw_data);
      if ($count) {
        $data['Items'] = array_slice($data['Items'], 0, $count);
      }
    }
    catch (GuzzleException $e) {
      $this->logger()->error($e->getMessage());
      $data = [];
    }

    return $data['Items'] ?? [];
  }

  /**
   * Returns a channel logger object.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   *   The logger for this channel.
   */
  protected function logger(): LoggerChannelInterface {
    return $this->loggerChannelFactory;
  }

}
