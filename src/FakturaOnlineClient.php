<?php

namespace App;

use App\Models\FakturaOnline;
use App\Models\NewFakturaOnlineResponse;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;
use Throwable;

class FakturaOnlineClient {
    private Client $client;
    private string|null $token = null;
    private string $tokenFilePath = __DIR__ . "/../private/fakturaonline-token";

    function onRequestRetry(
        int $attemptNumber,
        float $delay,
        RequestInterface &$request,
        array &$options,
        ?ResponseInterface $response,
        ?Throwable $exception
       ) {
        // Refresh token
        if ($response->getStatusCode() === 401) {
            if ($options['max_retry_attempts'] === $attemptNumber) {
                // Log it because this should not happen
            }

            $this->token = null;
            if (file_exists($this->tokenFilePath)) {
                unlink($this->tokenFilePath);
            }
            $this->loadToken();
        }
    }

    private function getAuthorizationHandler() {
        return function (callable $handler) {
              return function (RequestInterface $request, array $options) use ($handler) {
                  if ($this->token) {
                      $request = $request->withHeader('Authorization', 'Bearer ' . $this->token);
                  }

                  return $handler($request, $options);
              };
          };
    }

    private function generateHttpClient() {
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());
        $stack->push($this->getAuthorizationHandler());

        return new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.fakturaonline.cz/v0/',
            // You can set any number of default request options.
            'timeout'  => 10,
            'handler' => $stack,
            'max_retry_attempts' => 2,
            'retry_on_status' => [
                // Defaults
                503,
                429,
                // If 401 we will refresh token
                401
            ],
            'on_retry_callback'  => [$this, 'onRequestRetry'],
        ]);
    }

    /**
    * @template T
    * @param callable(): T $callback The callback function to execute.
    * @return T The result of the callback execution.
    */
    function withErrorHandling(callable $callback) {
        try {
            return $callback();
        } catch (\Exception $error) {
            $now = new \DateTime()->getTimestamp();
            file_put_contents(__DIR__."/logs/$now-message", $error->getMessage());
            file_put_contents(__DIR__."/logs/$now-trace", $error->getTraceAsString());

            throw $error;
        }
    }

    function loadToken() {
        if (file_exists($this->tokenFilePath)) {
            $this->token = trim(file_get_contents($this->tokenFilePath));
        }

        $response = $this->client->post('users/sign_in', [
            'body' => Psr7\Utils::streamFor(json_encode([
                'subscription' => [
                    'email' => $_ENV['FAKTURA_ONLINE_EMAIL'],
                    'password' => $_ENV['FAKTURA_ONLINE_PASSWORD'],
                ]
            ])),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $authHeaderValue = $response->getHeader('Authorization');

        if (!count($authHeaderValue)) {
            throw new Exception('Missing auth header from authentication for fakturaonline.cz');
        }

        $this->token = str_replace('Bearer ', '', $authHeaderValue[0]);
        file_put_contents($this->tokenFilePath, $this->token);
    }

    function __construct()
    {
       $this->client = $this->generateHttpClient();
       $this->loadToken();
    }

    function createInvoice(FakturaOnline $invoice) {
        return $this->withErrorHandling(function () use ($invoice) {
            $response = $this->client->post('invoices', [
                'body' => Psr7\Utils::streamFor(json_encode($invoice->asArray())),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $body = json_decode($response->getBody());

            return NewFakturaOnlineResponse::fromResponse((array) $body);
        });
    }

    function deleteInvoice(string $invoiceId) {
        $this->withErrorHandling(fn () => $this->client->delete("invoices/$invoiceId"));
    }

    function getInvoiceUnmapped(string $invoiceId): \stdClass | null {
        try {
            $currentInvoiceReponse = $this->withErrorHandling(fn () => $this->client->get("invoices/$invoiceId"));
            $body = json_decode($currentInvoiceReponse->getBody());

            return $body;
        } catch (\Exception $exception) {
            return null;
        }
    }

    function updateInvoice(string $invoiceId, FakturaOnline $invoice) {
        return $this->withErrorHandling(function () use ($invoiceId, $invoice) {
            $modifiedInvoice = $invoice->asArray();

            unset($modifiedInvoice['seller_attributes']);
            $body = $this->getInvoiceUnmapped($invoiceId);

            $modifiedInvoice['lines_attributes'] = array_merge(
                // Create all items
                $modifiedInvoice['lines_attributes'],
                // Delete previous
                array_map(function ($item) {
                    // Mark it as deleted
                    $item->_destroy = true;

                    return $item;
                }, $body->lines)
            );

            $response = $this->client->put("invoices/$invoiceId", [
                'body' => Psr7\Utils::streamFor(json_encode([
                    'invoice' => $modifiedInvoice
                ])),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $body = json_decode($response->getBody());

            return NewFakturaOnlineResponse::fromResponse((array) $body);
        });
    }

    function getPdfForInvoice(string $invoiceId) {
        return $this->withErrorHandling(function () use ($invoiceId) {
            $response = $this->client->get("invoices/$invoiceId.pdf");

            return $response->getBody();
        });
    }
}
