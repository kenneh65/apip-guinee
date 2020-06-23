<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;

class QuittanceSearchType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected $options;

    public function __construct($options = null) {
        $this->options = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $opts = $this->options;
        $this->langue = $options['langue'];

        $builder
                ->add('numeroDossier', 'text', array('required' => false))
                ->add('denominationSociale', 'text', array('required' => false))
                ->add('numeroQuittance', 'text', array('required' => false))
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction', 'required' => false,
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    }, 'property' => 'libelle', 'choice_value' => 'formeJuridique.id',
                    'multiple' => false, 'placeholder' => ''
                ))
                ->add('datePaiementDebut', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false, 'mapped' => false))
                ->add('datePaiementFin', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false, 'mapped' => false))
                ->add('typeDossier', 'entity', array('class' => 'BanquemondialeBundle:TypeDossier', 'required' => false, 'property' => 'libelle', 'choice_value' => 'id', 'multiple' => false, 'placeholder' => 'select'))
                ->add('entreprise', 'text', array('required' => false, 'mapped' => false))
                ->add('nomAgentDepot', 'text', array('required' => false, 'mapped' => false))

        ;



        $builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
                // formeJuridique vers formeJuridiqueTraduction							
                function ($object) {
            return null;
        },
                // formeJuridiqueTraduction vers formeJuridique
                function ($objectTraduction) {
            if ($objectTraduction) {
                //die(dump($objectTraduction->getFormeJuridique()));
                return $objectTraduction->getFormeJuridique();
            } else
                return null;
        }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'langue' => 'BanquemondialeBundle\Entity\Langue',
            'csrf_protection' => false,
            'data_class' => 'BanquemondialeBundle\Entity\Quittance'
        ));
    }

    public function getName() {
        return 'quittances';
    }

}
