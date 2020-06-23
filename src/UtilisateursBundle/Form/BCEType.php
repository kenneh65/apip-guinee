<?php

namespace UtilisateursBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;

class UtilisateursType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('nom', 'text', array('label' => 'utilisateur.nom', 'attr' => array('class' => 'form-control')))
                ->add('prenom', 'text', array('label' => 'utilisateur.prenom', 'attr' => array('class' => 'form-control')))
                ->add('genre', 'choice', array('choices' => array(
                        'M' => 'utilisateur.homme',
                        'F' => 'utilisateur.femme'
                     ), 'label' => 'utilisateur.genre', 'attr' => array('class' => 'form-control')))
                ->add('typeIdentification',new ChoiceType(),array('choices'=>array('cni'=>'utilisateur.type_cni','passport'=>'utilisateur.type_passport','carte consulaire'=>'utilisateur.type_carte','extrait'=>'utilisateur.type_extrait'),'label'=>'utilisateur.type_identiifcation','attr'=>array('class'=>'form-control')))
              ->add('cni', 'text', array('label' => 'utilisateur.cni', 'attr' => array('class' => 'form-control')))
                ->add('dateNaissance', 'date', array('label' => 'utilisateur.dateNaissance', 'widget' => 'single_text','required'=>true))
                ->add('lieuNaissance', 'text', array('required'=>true,'label' => 'utilisateur.lieu', 'attr' => array('class' => 'form-control')))
                ->add('adresse', 'textarea', array('required'=>true,'label' => 'utilisateur.adresse', 'attr' => array('class' => 'form-control')))
                ->add('telephone', 'text', array('label' => 'utilisateur.telephone', 'attr' => array('class' => 'form-control')))
                ->add('paysResidence', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ) use(&$options) {
					
                        return $formJ->getListPays($options['locale']);
                    }, 'property' => 'libelle', 'multiple' => false,'required'=>true,
                    'label' => 'utilisateur.paysResidence'
                ))
                ->add('regionResidence', 'entity', array('class' => 'BanquemondialeBundle:Region','required'=>true, 'property' => 'libelle', 'multiple' => false))
                ->add('departement', 'entity', array('class' => 'BanquemondialeBundle:Departement','required'=>true, 'property' => 'libelle', 'multiple' => false))
                ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
                ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
               ->add('enabled', 'choice', array('choices' => array('1' => 'utilisateur.actif', '0' => 'utilisateur.inactif'), 'label' => 'utilisateur.etat'))

        ;
        $builder->get('paysResidence')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($formeAsObject)use(&$options) {
            return $options['definedPaysTraduction'];
        },
                // string vers objet
                function ($formAsString) {
            return $formAsString->getPays();
        }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'UtilisateursBundle\Entity\Utilisateurs',
            'locale' => 'fr',
			'definedPaysTraduction' => 'BanquemondialeBundle\Entity\PaysTraduction'
        ));
    }

}
