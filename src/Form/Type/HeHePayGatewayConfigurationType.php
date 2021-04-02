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
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('app_key', TextType::class, [
            'label' => 'App Key',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('api_id', TextType::class, [
            'label' => 'API ID',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('api_secret', TextType::class, [
            'label' => 'API Secret',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('client_username', TextType::class, [
            'label' => 'Sylius Username',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('client_password', TextType::class, [
            'label' => 'Sylius Password',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('logo_url', TextType::class, [
            'label' => 'Store Logo URL',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('site_url', TextType::class, [
            'label' => 'Store Site URL',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('payment_result_callback', TextType::class, [
            'label' => 'Payment Result Callback URL',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ])->add('app_redirection_url', TextType::class, [
            'label' => 'Store Redirection URL',
            'constraints' => [
                new NotBlank(
                    [
                        'message' => 'Field Required',
                        'groups' => ['sylius'],
                    ]
                ),
            ],
        ]);
    }
}