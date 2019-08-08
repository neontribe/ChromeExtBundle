<?php
namespace KimaiPlugin\ChromeExtBundle\EventSubscriber;

use App\Entity\EntityWithMetaFields;
use App\Entity\MetaTableTypeInterface;
use App\Entity\ProjectMeta;
use App\Event\ProjectMetaDefinitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class ProjectFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProjectMetaDefinitionEvent::class => ['loadProjectMeta', 200],
        ];
    }

    public function loadProjectMeta(ProjectMetaDefinitionEvent $event)
    {
        $this->prepareEntity($event->getEntity(), new ProjectMeta());
    }

    private function prepareEntity(EntityWithMetaFields $entity, MetaTableTypeInterface $definition)
    {
        $definition
            ->setName('External ID')
            ->setType(TextType::class)
            ->addConstraint(new Length(['max' => 255]))
            ->setIsVisible(true);

        $entity->setMetaField($definition);
    }
}