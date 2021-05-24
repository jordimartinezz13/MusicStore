<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class FiltroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('campoDeBusqueda', TextType::class)
            ->add('tipoDeFiltraje', ChoiceType::class, [
                'choices' => [
                    'Filtro por texto' => [
                        'Si' => 'stock_yes'
                    ],
                    'Filtro por autores' => [
                        
                        'Backordered' => 'stock_backordered',
                        'Discontinued' => 'stock_discontinued',
                        ]
                    ]
                ]
            )
            ->add('save', SubmitType::class, array('label' => $options['submit']))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'submit' => 'Filtrar',
        ]);
    }
}
