<?php

namespace App\Form;

use App\Entity\Registro;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistroForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('nombre', TextType::class, array('attr'=>array('placeholder'=> 'Juan Luis', 'label'=>'Nombre')));

    $builder->add('apellidos',TextType::class,array('attr'=>array('placeholder'=>'Rolo Salazar', 'label'=>'Apellidos')));

    $builder->add('email',EmailType::class,array('attr'=>array('placeholder'=>'juanrolo@upgradehub.com','label'=>'Email')));

    $builder->add('password', PasswordType::class, array('attr'=>array('placeholder'=>'**********','label'=>'Password')));

    $builder->add('ciudad',ChoiceType::class,['choices'=>['Madrid'=>'madrid','Sevilla'=>'sevilla','Bacerlona'=>'barcelona'],'placeholder'=>'Elige tu ciudad', 'label'=>'Ciudad']);

    $builder->add('politica', CheckboxType::class, [
      'label' => 'Acepto la ',
      'required' => true,
    ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(['data_class' => User::class]);
  }
}