<?php

namespace DefaultBundle\Form;

use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use DefaultBundle\services\monServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class reservationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomCommercial')
            ->add('nom')
            ->add('prenom')
            ->add('adresse')
            ->add('email',EmailType::class,[])
            ->add('telephone')
            ->add('modePaiement', ChoiceType::class, array(
                'choices' => array(0=>'Orange Money'
                ),
                'required' => false, 'placeholder' => '===Veillez choisir un mode de paiement==='
            ))
            ->add('formeJuridique', 'entity', array(
        'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
        'query_builder' => function (FormeJuridiqueTraductionRepository $formJ) {
            $service=new monServices();
            return $formJ->getListFormeJuridiqueByLanque($service->getlocal());
        }, 'property' => 'libelle',
        'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
    ))
            ->add('PeriodeReservation', 'entity', array(
        'class' => 'DefaultBundle\Entity\PeriodeReservation',
        'property' => 'libelle','mapped'=>false
    ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DefaultBundle\Entity\reservation'
        ));
    }
}
