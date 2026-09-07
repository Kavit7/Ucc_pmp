<?php

namespace app\components;

use Yii;
use RuntimeException;

/**
 * Thin wrapper around the two Flutterwave v3 API calls the payment flow
 * needs: starting a Standard Checkout session, and independently verifying
 * a transaction server-side afterward. Verification is what actually
 * matters for security - PaymentGatewayController never trusts the
 * status query param a browser redirect can carry, only what this class
 * gets back directly from Flutterwave's API using the secret key.
 *
 * Docs: https://developer.flutterwave.com/docs/standard
 */
class FlutterwaveClient
{
    private const BASE_URL = 'https://api.flutterwave.com/v3';

    private $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public static function isConfigured()
    {
        $config = Yii::$app->params['flutterwave'] ?? [];
        return !empty($config['public_key']) && !empty($config['secret_key']);
    }

    public static function fromParams()
    {
        $config = Yii::$app->params['flutterwave'] ?? [];
        if (empty($config['secret_key'])) {
            throw new RuntimeException('Flutterwave is not configured - secret_key is missing.');
        }
        return new self($config['secret_key']);
    }

    /**
     * Starts a Standard Checkout session. Returns the hosted payment page
     * URL the tenant's browser should be redirected to.
     */
    public function initiatePayment($txRef, $amount, $currency, $redirectUrl, array $customer, $title)
    {
        $payload = [
            'tx_ref' => $txRef,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => $redirectUrl,
            'customer' => $customer,
            'customizations' => ['title' => $title],
            'payment_options' => 'card,mobilemoneytanzania,ussd',
        ];

        $result = $this->request('POST', '/payments', $payload);

        if (($result['status'] ?? null) !== 'success' || empty($result['data']['link'])) {
            throw new RuntimeException('Flutterwave did not return a checkout link: ' . json_encode($result));
        }

        return $result['data']['link'];
    }

    /**
     * Verifies a transaction by its Flutterwave-assigned id. Returns the
     * raw 'data' payload (status, amount, currency, tx_ref, etc.) so the
     * caller can cross-check everything itself rather than trust one field.
     */
    public function verifyTransaction($flwTransactionId)
    {
        $result = $this->request('GET', "/transactions/{$flwTransactionId}/verify");

        if (($result['status'] ?? null) !== 'success' || !isset($result['data'])) {
            throw new RuntimeException('Flutterwave verification failed: ' . json_encode($result));
        }

        return $result['data'];
    }

    private function request($method, $path, array $body = null)
    {
        $ch = curl_init(self::BASE_URL . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->secretKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            throw new RuntimeException("Could not reach Flutterwave: {$error}");
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Flutterwave returned a non-JSON response.');
        }

        return $decoded;
    }
}
