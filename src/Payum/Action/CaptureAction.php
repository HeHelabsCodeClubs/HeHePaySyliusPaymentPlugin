<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Payum\Action;

use HeHePay\SyliusPaymentPlugin\Payum\SyliusApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Payum;
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
    /** @var Payum */
    protected Payum $payum;

    public function __constructor(Payum $payum, Client $client) {
        $this->payum = $payum;
        $this->client = $client;
    }

    public function get_token() {
        $url = "https://gateway.hehepay.rw/api/v1/auth/get-token";
        
        $postData = array(
            'app_name' => 'hehe-cart',
            'app_key'=> 'C5485732FA6DEB48779E2E3DCAE8A',
        );

        // for sending data as json type
        $fields = json_encode($postData);

        $ch = curl_init($url);
        curl_setopt(
            $ch, 
            CURLOPT_HTTPHEADER, 
            array(
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function execute($request) {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getModel();

        try {
            $token = $this->get_token();

            $response = $this->client->request('POST', 'https://gateway.hehepay.rw/api/v1/payments/request', [
                'body' => json_encode([
                    'order_id' => $payment->getOrder(),
                    'amount' => $payment->getAmount(),
                    'currency' => $payment->getCurrencyCode(),
                    'app_logo_url' => 'https://res.cloudinary.com/hehe/image/upload/q_auto,f_auto,fl_lossy/v1569944754/logistics-platform/images/hehe-logo.png',
                    'site_url' => 'https://storefront.commerce.hehe.rw/',
                    'transaction_description' => 'Online Payment.'.$payment->getOrder(),
                    'payment_result_callback' => '#',
                    'app_redirection_url' => 'http://localhost:8000/en_US/order/0c3dm-4zjU',
                ]),
                'headers' => json_encode([
                    'Authorization' => 'Bearer ',
                    'content-type' => 'application/json',
                ])
            ]);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        } finally {
            header('Location: https://google.com');
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
    //     if (!$api instanceof SyliusApi) {
    //         throw new UnsupportedApiException('Not supported. Expected an instance of ' . SyliusApi::class);
    //     }

        $this->api = $api;
    }
}