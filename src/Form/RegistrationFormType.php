<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            /* agreeTerms это так называемый тип формы без сопоставления, что означает, что он не сопоставляется с каким-либо конкретным свойством объекта,
            и под объектом я подразумеваю объект,
            подключенный к этой форме, который в нашем случае является пользователем, который мы собираемся создать*/
            /*таким образом, этот флажок отображается как часть этой формы, но его значение не сопоставляется ни с каким свойством объекта,
            хотя, как вы видите, используя ограничения, для него должно быть установлено значение true */
            // Что по сути означает что кто-то соглашается с нашими правилами.
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            // Для того чтобы создать поле подтверждение пароля меняем PasswordType на RepeatedType
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                // Тип за которым будет повторять строка RepeatedType
                'type' => PasswordType::class,
                // mapped => false означает непривязанный к свойству какой либо сущности
                'mapped' => false,
                // Сообщение которое будет отображенно если пароли не совпадают
                'invalid_message' => 'The password field must match',
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => [
                    'label' => 'Passwords',
                    'mapped' => false
                ],
                'second_options' =>
                [
                    'label' => 'Repeated password',
                    'mapped' => false
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
