<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Artista;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class DiscoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artista', EntityType::class, array('class' => Artista::class,
            'choice_label' => 'getNombreCOMPLETO'))
            ->add('nombre', TextType::class)
            //->add('precio', TextType::class)
            ->add('precio', NumberType::class, [
                'scale'=>2,
                
            ])
            //->add('imagen', TextType::class, array('required' => false))
            ->add('imagen', FileType::class, [ //{{ form_row(form.imagen) }}
                'label' => 'Seleccionar imagen...',

                // Sin asignar significa que este campo no está asociado a ninguna propiedad de entidad
                'mapped' => false,

                // Hágalo opcional para que no tenga que volver a cargar el
                // archivo cada vez que edite los detalles del producto
                'required' => false,

                // los campos no mapeados no pueden definir su validación usando anotaciones
                // en la entidad asociada, por lo que puede usar las clases de restricción de PHP
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',// 'application/pdf',
                            'image/png'//,'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Por favor, selecciona una imagen ( JPEG / PNG ).',
                    ])
                ],
            ])
            //->add('save', SubmitType::class, array('label' => 'Crear Tasca'))
            ->add('save', SubmitType::class, array('label' => $options['submit']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'submit' => 'Enviar',
        ]);
    }

}
