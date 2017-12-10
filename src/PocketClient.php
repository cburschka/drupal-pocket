<?php

namespace Drupal\pocket;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;
use Drupal\pocket\Exception\AccessDeniedException;
use Drupal\pocket\Exception\UnauthorizedException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

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
   *
   * @throws \Drupal\pocket\Exception\AccessDeniedException
   * @throws \Drupal\pocket\Exception\UnauthorizedException
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
          throw new UnauthorizedException('Bad access tokens.');
        case 403:
          throw new AccessDeniedException('Action is not permitted.');
      }
    } catch (GuzzleException $e) {
      watchdog_exception('pocket', $e);
    }

    return [];
  }

}
