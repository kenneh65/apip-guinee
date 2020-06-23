<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ParametrageBundle\Repository\TypeArchiveNomCommerciaux;

class ArchiveNomCommerciauxType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('denominationSociale','text',array('required' => false))
				//->add('sigle')
                //->add('adresse', 'textarea') 
				//->add('nomFormulaire')      
				//->add('typePole', 'entity', array('class' => 'ParametrageBundle:TypePole', 'translation_domain' => 'messages','placeholder' => 'message.aucun.type', 'property'=>'description','required'=>false)   )
				//->add('region', 'entity', array('class' => 'BanquemondialeBundle:Region', 'translation_domain' => 'messages','placeholder' => 'message.aucun.type', 'property'=>'libelle','required'=>false)   )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ParametrageBundle\Entity\ArchiveNomCommerciaux'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'frmArchive';
    }

}
