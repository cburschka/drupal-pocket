<?php

namespace Drupal\pocket\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Url;
use Drupal\pocket\Exception\PocketHttpException;
use Drupal\pocket\PocketAuthClient;
use Drupal\pocket\PocketClientFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PocketAuthorize extends ControllerBase {

  use DependencySerializationTrait;

  /**
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $storage;

  /**
   * @var \Drupal\pocket\PocketClientFactoryInterface
   */
  protected $clientFactory;

  /**
   * @var \Drupal\pocket\PocketAuthClient
   */
  private $client;

  /**
   * PocketAuthorize constructor.
   *
   * @param \Drupal\Core\KeyValueStore\KeyValueStoreInterface $storage
   * @param \Drupal\pocket\PocketClientFactoryInterface       $clientFactory
   */
  public function __construct(
    KeyValueStoreInterface $storage,
    PocketClientFactoryInterface $clientFactory
  ) {
    $this->storage = $storage;
    $this->clientFactory = $clientFactory;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
   * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('keyvalue.expirable')->get('pocket'),
      $container->get('pocket.client')
    );
  }

  /**
   * Accept confirmation of a request token, and run the callback.
   *
   * @param string $id
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   * @throws \InvalidArgumentException
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function authorize(string $id): RedirectResponse {
    $request = $this->storage->get("request:$id");
    if (!$request) {
      throw new NotFoundHttpException('This request token does not exist.');
    }

    $callback = $request['callback'];
    try {
      $access = $this->getClient()
        ->getAccessToken($request['token'])
        ->setState($request['state']);
    } catch (PocketHttpException $exception) {
      throw new NotFoundHttpException('Pocket did not return an access token.');
    }

    $url = $callback($access);
    \assert($url instanceof Url);

    return new RedirectResponse($url->toString());
  }

  /**
   * Initialize the client.
   *
   * @return \Drupal\pocket\PocketAuthClient
   */
  protected function getClient(): PocketAuthClient {
    if ($this->client === NULL) {
      $this->client = $this->clientFactory->getAuthClient();
    }
    return $this->client;
  }

}
