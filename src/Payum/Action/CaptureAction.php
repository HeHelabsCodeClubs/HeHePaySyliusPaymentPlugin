<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Payum\Action;

use HeHePay\SyliusPaymentPlugin\Payum\SyliusApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Payum;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Reply\HttpRedirect;
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

    public function __constructor(Payum $payum) {
        $this->payum = $payum;
    }

    public function get_token() {
        $url = "https://gateway.hehepay.rw/api/v1/auth/get-token";

        $postData = array(
            'app_name' => $this->getContent['app_name'],
            'app_key'=> $this->getContent['app_key'],
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
            $payment_id = $payment->getMethod();
            $token = $this->get_token();
            $decode = json_decode($token);
            $amount = $payment->getAmount() / 100;

            if($decode->status_code != 200) {
                $payment->setDetails(['status' => 400]);
            } else {
                $url = "https://gateway.hehepay.rw/api/v1/payments/request";
                $postData = array(
                    "order_id" => $payment->getId(),
                    "amount" => $amount,
                    "currency" => "RWF", // $payment->getCurrencyCode()
                    "app_logo_url" => $this->getContent['logo_url'],
                    "site_url" => $this->getContent['site_url'],
                    "transaction_description" => "Order Payment. ID: ".$payment->getId(),
                    "payment_result_callback" => $this->getContent["payment_result_callback"],
                    "app_redirection_url" => $this->getContent["app_redirection_url"],
                    "custom" => [
                        "platform" => "web",
                        "site_url" => $this->getContent['site_url'],
                        "payment_id" => $payment_id->getId(),
                        "api_id" => $this->getContent["api_id"],
                        "api_secret" => $this->getContent["api_secret"],
                        "client_username" => $this->getContent["client_username"],
                        "client_password" => $this->getContent["client_password"]
                    ]
                );

                // for sending data as json type
                $fields = json_encode($postData);

                $ch = curl_init($url);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json',
                        'Authorization: Bearer '.$decode->data->access_token,
                    )
                );
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

                $result = curl_exec($ch);
                curl_close($ch);

                $URL = json_decode($result);

                $payment->setDetails(['redirection_URL' => $URL->data->payment_redirection_url, 'status' => 200]);
            }
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
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

        $this->getContent = $api->getContent();
    }
}
