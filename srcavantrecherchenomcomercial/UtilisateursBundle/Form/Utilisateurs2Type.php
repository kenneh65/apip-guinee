<?php

namespace UtilisateursBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use BanquemondialeBundle\Repository\RegionRepository;
use BanquemondialeBundle\Repository\PrefectureRepository;
use Symfony\Component\Form\CallbackTransformer;
use ParametrageBundle\Repository\PoleRepository;

class Utilisateurs2Type extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->pref = null;
        $builder->add('nom', 'text', array('label' => 'utilisateur.nom', 'attr' => array('class' => 'form-control')))
                ->add('prenom', 'text', array('label' => 'utilisateur.prenom', 'attr' => array('class' => 'form-control')))
                ->add('dateNaissance', 'date', array('label' => 'utilisateur.dateNaissance', 'widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false))
                ->add('lieuNaissance', 'text', array('required' => false, 'label' => 'utilisateur.lieu', 'attr' => array('class' => 'form-control')))
                ->add('telephone', 'text', array('required' => true, 'label' => 'utilisateur.telephone', 'attr' => array('class' => 'form-control', 'placeholder' => 'placeholder.telephone')))
//                ->add('regionResidence', 'entity', array(
//                    'class' => 'BanquemondialeBundle:Region', 
//                    'query_builder' => function (RegionRepository $formJ) use(&$options) {
//                        return $formJ->getRegionByPaysdeResidence();
//                    }, 'property' => 'libelle', 'multiple' => false,
//                    'placeholder' => 'message.selectionner'
//                ))
//                ->add('prefecture', 'entity', array(
//                    'class' => 'BanquemondialeBundle:Prefecture', 'query_builder' => function (PrefectureRepository $pref) use(&$options) {
//                        return $pref->getListPrefecture($this->pref);
//                    },
//                    'property' => 'libelle', 'multiple' => false, 'required' => true,
//                    'placeholder' => 'message.selectionner'
//                ))
//                ->add('sousPrefecture', 'entity', array('class' => 'BanquemondialeBundle:SousPrefectureCommune', 'required' => true, 'property' => 'libelle', 'multiple' => false, 'label' => "sousPrefecture" ,
//                    'placeholder'=>'message.selectionner'))
//                ->add('adresse', 'textarea', array('required' => true, 'label' => 'utilisateur.adresse', 'attr' => array('class' => 'form-control')))
                ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'required' => true))
                ->add('username', null, array('required' => true, 'label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
                ->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole', 'query_builder' => function (PoleRepository $pole)use(&$opts) {
                        return $pole->getListeDesPoleAPIP_Traitant();
                    },
                    'required' => true, 'property' => 'nom', 'multiple' => false, 'label' => "pole",
                    'placeholder' => 'message.selectionner', 'required' => 'required'))
                ->add('profile', 'entity', array('class' => 'UtilisateursBundle:Profile', 'required' => true, 'property' => 'nom', 'multiple' => false, 'label' => "Profile",
                    'placeholder' => 'message.selectionner'))
                ->add('entreprise', 'entity', array('class' => 'BanquemondialeBundle:Entreprise', 'required' => true, 'property' => 'denomination', 'multiple' => false, 'label' => "Entreprise",
                    'placeholder' => 'message.selectionner'))
                ->add('enabled', 'choice', array('choices' => array('1' => 'utilisateur.actif', '0' => 'utilisateur.inactif'), 'label' => 'utilisateur.etat'))

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'UtilisateursBundle\Entity\Utilisateurs',
            'locale' => 'fr'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'frmUtilisateur';
    }

}
