<?php

namespace App\Form;

use App\Entity\Departemant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Driver\Mysqli\Initializer\Options;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class searchType extends AbstractType{

    public  function __construct(private EntityManagerInterface $em){

    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class');
        $resolver->setDefaults(['compound' => false,'multiple' => true, 'search'=> '/search']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($value) : array{
                return $value->map(fn($d)=> (string)$d->getId())->toArray();
            },
            function (array $ids): Collection{
                /*if(empty($ids)){
                    return new ArrayCollection([]);
                }*/
                return new ArrayCollection(
                   // $this->em->getRepository(Departemant::class)->findBy(['id' => $ids]));
                    $this->em->getRepository(Departemant::class)->findAll());
            }
        ));
        //parent::buildForm($builder,$options);
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
       
        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = null;
        $view->vars['preferred_choices'] = [];
        $view->vars['choices'] =$this->choices($form->getData());  /*[
            new ChoiceView([], 'aeaez', 'azeaze')
        ];*/
       // $view->vars['class'] = Departemant::class;
        $view->vars['query_builder'] = function(EntityRepository $er){
            return $er->createQueryBuilder('s')->orderBy('s.id','DESC');
        };
        $view->vars['choice_translation_domain'] = [];
        $view->vars['full_name'] .= '[]';
        $view->vars['multiple'] = true;
        $view->vars['attr']['data-remote'] = $options['search'];
    }

    public function getBlockPrefix()
    {
        return 'choice';
    }

    private function choices (Collection $value){
        return $value
            ->map(fn($d) => new ChoiceView($d, (string)$d->getId(), (string) $d))
            ->toArray();
    }
}