<?php

declare(strict_types=1);

namespace HeHePay\SyliusPaymentPlugin\Payum;

final class SyliusApi
{
    /** @var string */
    private $apiKey;

    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function getContent(): array
    {
        return $this->content;
    }
}
