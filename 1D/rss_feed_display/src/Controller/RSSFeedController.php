<?php

namespace Drupal\rss_feed_display\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * RSSFeed Controller
 *
 * This class provides a controller to display the links from an RSS feed.
 */
class RSSFeedController extends ControllerBase {

  /**
   * RouteMatchInterface to get the current URL.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * RequestStack to get the current request.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected RequestStack $requestStack;

  /**
   * Constructs a new instance of RSSFeedController.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(
    RequestStack $requestStack
  ) {
    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('request_stack'),
    );
  }

  /**
   * Generates a list of links from an RSS feed.
   *
   * @return array
   *   A render array containing the list of links.
   */
  public function linkList(): array {
    // Get the feed URL from the URL argument, if it exists.
    $feed_url = $this->requestStack->getCurrentRequest()->get('rss-url');
    if (!$feed_url) {
      // If the URL argument does not exist, get the URL from the configuration.
      $config = $this->config('rss_feed_display.settings');
      $feed_url = $config->get('rss_url');
    }

    // Attempt to retrieve the RSS feed from the URL.
    $rss = @file_get_contents($feed_url);
    $rss = @simplexml_load_string($rss);

    if ($rss === FALSE) {
      // If the RSS feed could not be retrieved, add a warning message.
      $this->messenger()
        ->addWarning(t("Can't fetch RSS Feed from provided URL."));
      return [];
    }

    // Create a list of links from the items in the RSS feed.
    $parents = []; // Declare an array to store the links.
    foreach ($rss->channel->item as $item) {
      // Format the publication date of the item.
      $date = Date('H:i D d/m/Y ', strtotime($item->pubDate));
      // Combine the title of the item and its publication date.
      $title = sprintf('%s (%s)', $item->title, $date);

      // Create a link object from the title and link of the item.
      $link = Link::fromTextAndUrl($title, Url::fromUri($item->link));
      // Add the renderable link object to the array of links.
      $parents[] = $link->toRenderable();
    }

    // Return the links in a list theme.
    return [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $parents,
    ];
  }

}
