<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public string $resellerId = 'SC949F78';
    public string $softwareHouseId = 'SC949F78';

    public function payment(Request $request){
        $order = $request->input('order');

        $amount = 1000; // 10.00 EUR

        $paymentIntent = $this->createPaymentIntent($amount, $order);

        if (isset($paymentIntent['error'])) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pago',
                'error' => $paymentIntent['error'],
            ], $paymentIntent['status']);
        }

        $paymentIntentId = $paymentIntent['id'];

        $response = $this->unlinkedRefund('', $paymentIntentId);

        return response()->json($response);
    }


    // PAYMENT INTENTS

    public function createPaymentIntent($amount, $order){
        // URL de la API de Dojo
        $url = 'https://api.dojo.tech/payment-intents';

        // Cuerpo de la solicitud
        $body = [
            'amount' => [
                'value' => $amount,
                'currencyCode' => 'EUR',
            ],
            'reference' => $order,
            'paymentMethods' => [
                'Card',
                'Wallet',
            ],
        ];

        // Encabezados de la solicitud
        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
        ];

        // Realizar la solicitud POST
        $response = Http::withHeaders($headers)->post($url, $body);

        // Manejo de la respuesta
        if ($response->successful()) {
            // La solicitud fue exitosa
            return $response->json();
        } else {
            // Manejo de errores
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    public function checkPaymentIntentStatus($paymentIntent){
        // URL de la API de Dojo
        $url = 'https://api.dojo.tech/payment-intents/' . $paymentIntent;

        // Encabezados de la solicitud
        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        // Realizar la solicitud GET
        $response = Http::withHeaders($headers)->get($url);

        // Manejo de la respuesta
        if ($response->successful()) {
            // La solicitud fue exitosa
            return $response->json();
        } else {
            // Manejo de errores
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    // TERMINALS

    public function getTerminals(){
        $url = 'https://api.dojo.tech/terminals';

        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        $response = Http::withHeaders($headers)->get($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    public function getTerminal($terminalId){
        $url = 'https://api.dojo.tech/terminals/' . $terminalId;

        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        $response = Http::withHeaders($headers)->get($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    // TERMINAL SESSIONS

    public function createTerminalSession($terminalId, $paymentIntentId){
        $resellerId = 'SC949F78';
        $softwareHouseId = 'SC949F78';

        // URL de la API de Dojo
        $url = 'https://api.dojo.tech/terminal-sessions';

        // Cuerpo de la solicitud
        $body = [
            'terminalId' => $terminalId,
            'details' => [
                'matchedRefund' => [
                    'paymentIntentId' => $paymentIntentId,
                ],
                'sessionType' => 'Sale',
            ],
        ];

        // Encabezados de la solicitud
        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $resellerId,
            'software-house-id' => $softwareHouseId,
            'Content-Type' => 'application/json',
        ];

        // Realizar la solicitud POST
        $response = Http::withHeaders($headers)->post($url, $body);

        // Manejo de la respuesta
        if ($response->successful()) {
            // La solicitud fue exitosa
            return $response->json();
        } else {
            // Manejo de errores
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    public function unlinkedRefund($terminalId, $paymentIntentId){
        $resellerId = 'SC949F78';
        $softwareHouseId = 'SC949F78';

        // URL de la API de Dojo
        $url = 'https://api.dojo.tech/terminal-sessions';

        // Cuerpo de la solicitud
        $body = [
            'terminalId' => $terminalId,
            'details' => [
                'matchedRefund' => [
                    'paymentIntentId' => $paymentIntentId,
                ],
                'sessionType' => 'MatchedRefund',
            ],
        ];

        // Encabezados de la solicitud
        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
            'Content-Type' => 'application/json',
        ];

        // Realizar la solicitud POST
        $response = Http::withHeaders($headers)->post($url, $body);

        // Manejo de la respuesta
        if ($response->successful()) {
            // La solicitud fue exitosa
            return $response->json();
        } else {
            // Manejo de errores
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    public function getTerminalSession($terminalId){
        $url = 'https://api.dojo.tech/terminal-sessions/' . $terminalId;

        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        $response = Http::withHeaders($headers)->get($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

    // CANCEL

    public function cancel($terminalSession){
        $url = 'https://api.dojo.tech/terminal-sessions/' . $terminalSession . '/cancel';

        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        $response = Http::withHeaders($headers)->put($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }


    // SIGNATURE

    public function signatureVerification($terminalSession, $accepted){
        $url = 'https://api.dojo.tech/terminal-sessions/' . $terminalSession . '/signature';

        $headers = [
            'Version' => '2024-02-05',
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        $body = [
            'accepted' => $accepted,
        ];

        $response = Http::withHeaders($headers)->put($url, $body);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }
    }

}
