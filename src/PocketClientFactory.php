<?php

namespace Drupal\pocket;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;

/**
 * Client factory class.
 */
class PocketClientFactory implements PocketClientFactoryInterface {

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var string
   */
  private $key;

  /**
   * PocketClientFactory constructor.
   *
   * @param \GuzzleHttp\ClientInterface                $client
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(ClientInterface $client, ConfigFactoryInterface $configFactory) {
    $this->client = $client;
    $this->configFactory = $configFactory;
  }

  /**
   * @param string $accessToken
   *
   * @return \Drupal\pocket\PocketClient
   */
  public function getClient(string $accessToken): PocketClientInterface {
    return new PocketClient($this->client, $this->getKey(), $accessToken);
  }

  /**
   * @return string
   */
  private function getKey(): string {
    if (!$this->key) {
      $this->key = $this->configFactory->get('pocket.config')->get('key');
    }
    return $this->key;
  }

}
