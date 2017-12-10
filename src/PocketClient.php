<?php

namespace Drupal\pocket;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Pocket client.
 */
class PocketClient implements PocketClientInterface {

  const URL = 'https://getpocket.com/';

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $http;

  /**
   * @var string
   */
  private $key;

  /**
   * @var string
   */
  private $accessToken;

  /**
   * PocketClient constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http
   * @param string                      $key
   * @param string                      $accessToken
   */
  public function __construct(ClientInterface $http, string $key, string $accessToken) {
    $this->http = $http;
    $this->key = $key;
    $this->accessToken = $accessToken;
  }

  /**
   * {@inheritdoc}
   */
  public function add(Url $url, array $tags = [], string $title = NULL): array {
    $request['url'] = $url->setAbsolute()->toString();
    if ($tags) {
      $request['tags'] = implode(',', $tags);
    }
    if ($title) {
      $request['title'] = $title;
    }
    $response = $this->sendRequest('v3/add', $request);
    return $response['item'];
  }

  /**
   * @param string $endpoint
   * @param array  $request
   *
   * @return array
   */
  protected function sendRequest(string $endpoint, array $request): array {
    $request['consumer_key'] = $this->key;
    $request['access_token'] = $this->accessToken;
    return $this->sendJson(static::URL . $endpoint, $request);
  }

  /**
   * @param string $url
   * @param mixed  $body
   *
   * @return array
   */
  protected function sendJson(string $url, $body): array {
    try {
      $response = $this->http->request('POST', $url, ['json' => $body]);
      return Json::decode($response->getBody());
    } catch (GuzzleException $e) {
      watchdog_exception('pocket', $e);
      return [];
    }
  }

}
