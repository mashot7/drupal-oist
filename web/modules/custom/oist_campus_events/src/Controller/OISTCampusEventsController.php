<?php

namespace Drupal\oist_campus_events\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\oist_campus_events\Services\FetchEventsService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the rest output.
 */
class OISTCampusEventsController extends ControllerBase {

  /**
   * @var \Drupal\oist_campus_events\Services\FetchEventsService
   */
  private FetchEventsService $fetchEventsService;

  /**
   * Controller
   *
   * @param \Drupal\oist_campus_events\Services\FetchEventsService $fetchEventsService
   */
  public function __construct(
    FetchEventsService $fetchEventsService
  ) {
    $this->fetchEventsService = $fetchEventsService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      $container->get('oist_campus_events.fetch_events')
    );
  }

  /**
   * Displays the photos.
   *
   * @return array[]
   *   A renderable array representing the photos.
   * @throws \Exception
   */
  public function infoscreen(): array {
    $events = $this->fetchEventsService->getFeeds(7);

    return [
      '#theme' => 'events_display',
      '#events' => $events,
      '#attached' => [
        'library' => ['oist_campus_events/bootstrap'],
      ],
    ];
  }

}
