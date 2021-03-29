<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use HeHePay\SyliusPaymentPlugin\Payum\Action\StatusAction;
use HeHePay\SyliusPaymentPlugin\Payum\Action\CaptureAction;
final class HeHePayGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'hehe_pay',
            'payum.factory_title' => 'HeHe Pay',
            'payum.action.status' => new StatusAction(),
            'payum.action.capture' => new CaptureAction(),
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new SyliusApi([
                'app_name' => $config['app_name'],
                'app_key' => $config['app_key'],
                'api_id' => $config['api_id'],
                'api_secret' => $config['api_secret'],
                'client_username' => $config['client_username'],
                'client_password' => $config['client_password'],
                'logo_url' => $config['logo_url'],
                'site_url' => $config['site_url'],
                'payment_result_callback' => $config['payment_result_callback'],
                'app_redirection_url' => $config['app_redirection_url'],
            ]);
        };
    }
}
