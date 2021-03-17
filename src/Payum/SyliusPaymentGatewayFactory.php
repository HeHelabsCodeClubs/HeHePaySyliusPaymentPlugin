<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use HeHePay\SyliusPaymentPlugin\Payum\Action\StatusAction;

final class SyliusPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'hehe_pay',
            'payum.factory_title' => 'HeHe Pay',
            'payum.action.status' => new StatusAction(),
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return [
                'app_name' => $config['app_name'],
                'app_key' => $config['app_key'],
            ];
        };
    }
}