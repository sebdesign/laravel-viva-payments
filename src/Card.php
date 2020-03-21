<?php

namespace Sebdesign\VivaPayments;

use Illuminate\Support\Carbon;

class Card
{
    const ENDPOINT = '/api/cards/';

    /**
     * @var \Sebdesign\VivaPayments\Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param \Sebdesign\VivaPayments\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a token for the credit card.
     *
     * @param  string $name   The cardholder's name
     * @param  string $number The credit card number
     * @param  int    $cvc    The CVC number
     * @param  int    $month  The expiration month
     * @param  int    $year   The expiration year
     * @return string
     */
    public function token(
        string $name,
        string $number,
        int $cvc,
        int $month,
        int $year
    ): string {
        $token = $this->client->post(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                'CardHolderName'    => $name,
                'Number'            => $this->normalizeNumber($number),
                'CVC'               => $cvc,
                'ExpirationDate'    => $this->getExpirationDate($month, $year),
            ],
            \GuzzleHttp\RequestOptions::QUERY => [
                'key'               => $this->getKey(),
            ],
        ]);

        return $token->Token;
    }

    /**
     * Strip non-numeric characters.
     *
     * @param  string $number  The credit card number
     * @return string
     */
    protected function normalizeNumber(string $number): string
    {
        return preg_replace('/\D/', '', $number);
    }

    /**
     * Get the public key as query string.
     *
     * @return string
     */
    protected function getKey(): string
    {
        return config('services.viva.public_key');
    }

    /**
     * Get the expiration date.
     *
     * @param  int $month
     * @param  int $year
     * @return string
     */
    protected function getExpirationDate(int $month, int $year): string
    {
        return Carbon::createFromDate($year, $month, 15)->toDateString();
    }

    /**
     * Check for installments support.
     *
     * @param  string $number  The credit card number
     * @return int
     */
    public function installments(string $number)
    {
        $response = $this->client->get(self::ENDPOINT.'/installments', [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'CardNumber' => $this->normalizeNumber($number),
            ],
            \GuzzleHttp\RequestOptions::QUERY => [
                'key' => $this->getKey(),
            ],
        ]);

        return $response->MaxInstallments;
    }
}
