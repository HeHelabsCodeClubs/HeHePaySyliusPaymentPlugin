<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;

final class HeHePayGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('app_name', TextType::class, [
            'label' => 'App Name',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'App Name Not Blank',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('app_key', TextType::class, [
            'label' => 'App Key',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'App Key Not Blank',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ]);
    }
}