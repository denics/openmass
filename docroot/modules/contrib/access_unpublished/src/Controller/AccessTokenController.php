<?php

namespace Drupal\access_unpublished\Controller;

use Drupal\access_unpublished\Entity\AccessToken;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AccessTokenController.
 *
 * @package Drupal\access_unpublished\Controller
 */
class AccessTokenController extends ControllerBase {

  protected $requestStack;

  /**
   * AccessTokenController constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(RequestStack $requestStack) {

    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  /**
   * Renews a AccessToken.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Returns to previous page.
   */
  public function renew($id) {

    $previousUrl = $this->requestStack->getCurrentRequest()->server->get('HTTP_REFERER');

    /** @var \Drupal\access_unpublished\Entity\AccessToken $token */
    $token = $this->entityTypeManager()->getStorage('access_token')->load($id);

    if ($token) {
      $token->set('expire', AccessToken::defaultExpiration());
      $token->save();

    }

    return new RedirectResponse($previousUrl);
  }

  /**
   * Deletes a AccessToken.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Returns to previous page.
   */
  public function delete($id) {

    $previousUrl = $this->requestStack->getCurrentRequest()->server->get('HTTP_REFERER');

    /** @var \Drupal\access_unpublished\Entity\AccessToken $token */
    $token = $this->entityTypeManager()->getStorage('access_token')->load($id);

    if ($token) {
      $token->delete();
    }

    return new RedirectResponse($previousUrl);
  }

}
