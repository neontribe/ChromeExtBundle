<?php
namespace KimaiPlugin\ChromeExtBundle\EventListener;

use App\Entity\Timesheet;
use App\Timesheet\CalculatorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use KimaiPlugin\ChromeExtBundle\Entity\ExtIssue;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// http://localhost/workspace/kimai/kimai2-app/public/en/timesheet/create?begin=2019-06-11&project=1&extProject=1&extIssue=1
class TimesheetSaveListener implements EventSubscriberInterface, CalculatorInterface
{
    /**
     *
     * @var Registry
     */
    protected $doctrine;

    /**
     *
     * @var RequestStack
     */
    protected $requestStack;

    /**
     *
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /*
     * @var array
     */
    protected $issueStack = [];

    /**
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry, RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->doctrine = $registry;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                [
                    'processResponse',
                    10
                ]
            ]
        ];
    }

    public function calculate(Timesheet $record)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            // If a timesheet is loaded from a fixture, this is null;
            return;
        }
        $parameters = $this->getParameters($request);
        $issueId = $parameters['extIssue'];

        if ($issueId) {
            $issueRepo = $this->doctrine->getManager()->getRepository(ExtIssue::class);
            /*
             * var ExtIssue
             */
            $extIssue = $issueRepo->find($issueId);
            if ($extIssue) {
                // This won't work. The record is not persisted and doesn't have an id yet.
                // $extIssue->addTimesheet($record);
                // $entityManager->persist($extIssue);
                // So we'll stash it for later
                $this->issueStack[] = [
                    'issueId' => $issueId,
                    'record' => $record
                ];
            }
        }
    }

    /**
     * When we get here the record has been persisted so we can add it to the ext issue.
     *
     * @param FilterResponseEvent $event
     * @return \Symfony\Component\HttpKernel\Event\FilterResponseEvent
     */
    public function processResponse(FilterResponseEvent $event)
    {
        $entityManager = $this->doctrine->getManager();
        $issueRepo = $entityManager->getRepository(ExtIssue::class);

        $extIssue = False;

        // This block persists the time sheets now they have ids set after creation.
        foreach ($this->issueStack as $timesheet) {
            $issueId = $timesheet['issueId'];
            $record = $timesheet['record'];

            $extIssue = $issueRepo->find($issueId);
            $extIssue->addTimesheet($record);

            $entityManager->persist($extIssue);
        }

        if ($extIssue) {
            $entityManager->flush();

            $projectUuid = $extIssue->getProject()->getUuid();
            $issueUuid = $extIssue->getUuid();

            $url = $this->urlGenerator->generate('chrome_ext_list', [
                "projectUuid" => $projectUuid,
                "issueUuid" => $issueUuid
            ]);
            $response = new RedirectResponse($url);
            $event->setResponse($response);
        }
    }

    protected function getParameters(Request $request): array {
        $referrer = $request->server->get('HTTP_REFERER');
        $query = parse_url($referrer, PHP_URL_QUERY);

        $parameters = [];
        parse_str($query, $parameters);

        return $parameters;
    }

}