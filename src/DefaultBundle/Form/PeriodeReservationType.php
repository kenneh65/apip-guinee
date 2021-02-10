<?php

namespace DefaultBundle\Form;

use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use DefaultBundle\services\monServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodeReservationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('amount')
            ->add('nombre')
            ->add('formeJuridiqueTraduction', 'entity', array(
                'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                'query_builder' => function (FormeJuridiqueTraductionRepository $formJ) {
                    $service=new monServices();
                    return $formJ->getListFormeJuridiqueByLanque($service->getlocal());
                }, 'property' => 'libelle',
                'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
            ))
//            ->add('formeJuridiqueTraduction')
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DefaultBundle\Entity\PeriodeReservation'
        ));
    }
}
