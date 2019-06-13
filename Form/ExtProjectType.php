<?php

namespace KimaiPlugin\ChromeExtBundle\Form;

use App\Entity\Project;
use KimaiPlugin\ChromeExtBundle\Entity\ExtProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {

        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $repo = $options['project_repo'];
        $kimaiProject = $options['kimai_project'];
        $projects = $repo->findAll();

        $options = [
            'label' => 'Choose',
            'required' => true,
            'choices' => $projects,
            'choice_label' => function (Project $project, $key, $value) {
            return $project->getName();
            }
            ];

        if ($kimaiProject) {
            $options['data'] = $kimaiProject->getProject();
        }

        $label = $kimaiProject ? 'Update' : 'Create';
        $builder->add('project', ChoiceType::class, $options)->add('save', SubmitType::class, [
            'label' => $label,
            'attr' => [
                'class' => 'btn-primary btn'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('project_repo');
        $resolver->setRequired('kimai_project');
//         $resolver->setDefaults([
//             'data_class' => ExtProject::class,
//         ]);
    }
}
