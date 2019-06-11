<?php
namespace KimaiPlugin\ChromeExtBundle\EventListener;

use App\Entity\Timesheet;
use App\Timesheet\CalculatorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TimesheetSaveListener  implements CalculatorInterface {

  /**
   *
   * @var LoggerInterface
   */
  protected $logger;

  /**
   *
   * @var Registry
   */
  protected $doctrine;

  /**
   *
   * @var SessionInterface
   */
  protected $session;

  /**
   *
   * @param RegistryInterface $registry
   */
  public function __construct(LoggerInterface $logger, RegistryInterface $registry, SessionInterface $session) {
    $this->logger = $logger;
    $this->doctrine = $registry;
    $this->session = $session;
  }

  public function calculate(Timesheet $record)
  {
      // Move logic here
  }

  /*
  public function processResponse(FilterResponseEvent $event) {
    $kernel = $event->getKernel();
    $request = $event->getRequest();
    $response = $event->getResponse();

    $route = $request->get('_route');
    $method = $request->getMethod();

    if ($route === 'timesheet_create') {
      if ($method == "GET") {
        // Is this a GET then check if it came fromt he extension
        $projectUuid = $request->get('projectUuid');
        $issueUuid = $request->get('issueUuid');

        if ($projectUuid && $issueUuid) {
          // Attach these to the form
          $this->session->set('kimai-neon-chrome-ext', [
            'projectUuid' => $projectUuid,
            'issueUuid' => $issueUuid
          ]);
        }
      } elseif ($method == "POST") {
        // Else if this is a POST then see if we are a extension create
        $data = $this->session->get('kimai-neon-chrome-ext');
        $projectUuid = $data['projectUuid'];
        $issueUuid = $data['issueUuid'];
        if ($projectUuid && $issueUuid) {
          //
        }
        $this->session->remove('kimai-neon-chrome-ext');
      }
    }
  }
  */
}