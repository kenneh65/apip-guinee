<?php

namespace DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceJuriqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denominationCommercial', TextType::class,[
                'attr'=>['class'=>'form-control ']
            ])
//            ->add('isPiecesIdentite',CheckboxType::class,[
//                'attr'=>['class'=>'pretty p-default']
//            ])
//            ->add('isDenomination')
//            ->add('isConformiteJuridique')
//            ->add('isCapital')
//            ->add('isDuree')
//            ->add('isActivites')
            ->add('remarqueVerificateur',TextareaType::class,[
                'required'=>false,
                'attr'=>['class'=>'form-control ','rows'=>'10']
            ])
//            ->add('dateVerification', DateType::class,[
//                'widget' => 'single_text',
//                 'format' => 'yyyy-MM-dd',
//                 'data' => new \DateTime(),
//                 'required' => true,
//                  'mapped' => true,
//                    'attr'=>[
//                        'class'=>'form-control '
//                    ]
//
//                ]
//                )
            // ->add('isCreated')
            // ->add('utilisateur')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DefaultBundle\Entity\ServiceJurique',
        ));
    }
}
