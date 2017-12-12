<?php

namespace Drupal\pocket\Client;

use Drupal\Core\Url;
use GuzzleHttp\ClientInterface;

/**
 * Pocket user-linked client.
 */
class PocketUserClient extends PocketClient implements PocketUserClientInterface {

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
    parent::__construct($http, $key);
    $this->accessToken = $accessToken;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\pocket\Exception\UnauthorizedException
   * @throws \Drupal\pocket\Exception\AccessDeniedException
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
   * {@inheritdoc}
   */
  protected function sendRequest(string $endpoint, array $request): array {
    $request['access_token'] = $this->accessToken;
    return parent::sendRequest($endpoint, $request);
  }

}
