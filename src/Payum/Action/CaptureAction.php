<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Payum\Action;

use HeHePay\SyliusPaymentPlugin\Payum\SyliusApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;
use Payum\Core\Request\Capture;

final class CaptureAction implements ActionInterface, ApiAwareInterface
{
    /** @var Client */
    private $client;
    /** @var SyliusApi */
    private $api;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getModel();

        try {
            $response = $this->client->request('POST', 'https://gateway.hehepay.rw/api/v1/auth/get-token', [
                'body' => json_encode([
                    'app_name' => 'hehe-cart',
                    'app_key'=> 'C5485732FA6DEB48779E2E3DCAE8A',
                ]),
            ]);

            $response = $this->client->request('POST', 'https://gateway.hehepay.rw/api/v1/payments/request', [
                'body' => json_encode([
                    'order_id' => 'id',
                    'amount' => $payment->getAmount(),
                    'currency' => $payment->getCurrencyCode(),
                    'app_logo_url' => 'https://res.cloudinary.com/hehe/image/upload/q_auto,f_auto,fl_lossy/v1569944754/logistics-platform/images/hehe-logo.png',
                    'site_url' => 'https://storefront.commerce.hehe.rw/',
                    'transaction_description' => 'Online Payment.',
                    'payment_result_callback' => '#',
                    'app_redirection_url' => 'http://localhost:8000/en_US/order/0c3dm-4zjU',
                ]),
            ]);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } finally {
            $payment->setDetails(['status' => $response->getStatusCode()]);
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof SyliusPaymentInterface
        ;
    }

    public function setApi($api): void
    {
        if (!$api instanceof SyliusApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusApi::class);
        }

        $this->api = $api;
    }
}