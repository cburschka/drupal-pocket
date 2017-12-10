<?php

namespace Drupal\pocket;

use Drupal\Component\Serialization\Json;
use Drupal\pocket\Exception\AccessDeniedException;
use Drupal\pocket\Exception\UnauthorizedException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

class PocketClient {

  /**
   * The main service URL.
   */
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
   * PocketClient constructor.
   *
   * @param \GuzzleHttp\ClientInterface $http
   * @param string                      $key
   */
  public function __construct(ClientInterface $http, string $key) {
    $this->http = $http;
    $this->key = $key;
  }

  /**
   * @param string $endpoint
   * @param array  $request
   *
   * @return array
   *
   * @throws \Drupal\pocket\Exception\AccessDeniedException
   * @throws \Drupal\pocket\Exception\UnauthorizedException
   */
  protected function sendRequest(string $endpoint, array $request): array {
    $request['consumer_key'] = $this->key;
    return $this->sendJson(static::URL . $endpoint, $request);
  }

  /**
   * @param string $url
   * @param mixed  $body
   *
   * @return array
   *
   * @throws \Drupal\pocket\Exception\UnauthorizedException
   * @throws \Drupal\pocket\Exception\AccessDeniedException
   */
  protected function sendJson(string $url, $body): array {
    try {
      $response = $this->http->request('POST', $url, ['json' => $body]);
      return Json::decode($response->getBody());
    } catch (ServerException $e) {
      $response = $e->getResponse();
      switch ($response->getStatusCode()) {
        // Swallow 400 and 503, as neither can be fixed by the caller.
        case 400:
        case 503:
          watchdog_exception('pocket', $e);
          break;
        case 401:
          throw new UnauthorizedException($e);
        case 403:
          throw new AccessDeniedException($e);
      }
    } catch (GuzzleException $e) {
      watchdog_exception('pocket', $e);
    }

    return [];
  }

}
