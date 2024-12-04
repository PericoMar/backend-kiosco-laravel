<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public string $resellerId = 'SC949F78';
    public string $softwareHouseId = 'SC949F78';
    // VCMKONCSIP0MA - tm_sandbox_673cb09aa673538c27f3dea2 - simulates a successful chip and pin payment. (Simula un pago satisfactorio con Chip y Pin) ✅
    // VCMKONCDIP0VA - tm_sandbox_673cb0b0f6eaab0d89c10291 - simulates a declined chip and pin payment. (Simula la negación del pago con chip y pin) ✅
    // VCMKONCSCP0MA - tm_sandbox_673cb0ddf6eaab0d89c10297 - simulates a contactless payment with device verification. (Simula en pago con contactless) ✅
    // VCMKONCSIS0VA - tm_sandbox_673cb0f1f6eaab0d89c1029e - simulates a signature payment. (Simula el pago con intento de firma) ✅
    // VCMKONCVIP0VA - tm_sandbox_673cb10831038ac820817c23 - simulates an unsuccessful payment result.(Simula un pago NO satisfactorio) ✅ NO Satisfactorio porque se expira el pago.
    // VCMKONCTIP0MA - tm_sandbox_673cb12131038ac820817c26 - simulates a "TIMED_OUT" payment result. (Simula un Time Out) ✅ tm_sandbox_674ede97e438a77b9219165b
    // VCMKONCCIP0MA - tm_sandbox_673cb136a673538c27f3deb0 - simulates a "CANCELED" payment result. (Simula una cancelación del proceso). ✅
    // Terminal fisico tm_sandbox_673cb07e31038ac820817c18

    public string $terminalId = 'tm_sandbox_673cb09aa673538c27f3dea2';

    public function payment(Request $request){
        $amount = $request->input('amount');
        Log::info('Amount: ' . $amount);
        Log::info('Request: ' . json_encode($request->all()));

        $amount = $amount * 100;
        $order = 'ORD-123456';

        $paymentIntent = $this->createPaymentIntent($amount, $order);

        if (isset($paymentIntent['error'])) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pago',
                'error' => $paymentIntent['error'],
            ], $paymentIntent['status']);
        }

        $paymentIntentId = $paymentIntent['id'];

        $response = $this->createTerminalSession($this->terminalId, $paymentIntentId);

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
            'Version' => env('DOJO_API_VERSION'),
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
            'Version' => env('DOJO_API_VERSION'),
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
            'Version' => env('DOJO_API_VERSION'),
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
            'Version' => env('DOJO_API_VERSION'),
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
                'sale' => [
                    'paymentIntentId' => $paymentIntentId,
                ],
                'sessionType' => 'Sale',
            ],
        ];

        // Encabezados de la solicitud
        $headers = [
            'Version' => env('DOJO_API_VERSION'),
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $resellerId,
            'software-house-id' => $softwareHouseId,
        ];

        Log::info('Creating terminal session with body: ' . json_encode($body));
        Log::info('Headers: ' . json_encode($headers));

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
            'Version' => env('DOJO_API_VERSION'),
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

    public function getTerminalSession($terminalSessionId){
        $url = 'https://api.dojo.tech/terminal-sessions/' . $terminalSessionId;

        $headers = [
            'Version' => env('DOJO_API_VERSION'),
            'Authorization' => 'Basic ' . env('DOJO_API_KEY'),
            'reseller-id' => $this->resellerId,
            'software-house-id' => $this->softwareHouseId,
        ];

        $response = Http::withHeaders($headers)->get($url);

        return $response->json();
    }

    // CANCEL

    public function cancelPayment($terminalSessionId){
        $url = 'https://api.dojo.tech/terminal-sessions/' . $terminalSessionId . '/cancel';

        $headers = [
            'Version' => env('DOJO_API_VERSION'),
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

    public function signatureVerification(Request $request, $terminalSession){
        $accepted = $request->input('accepted');

        $url = 'https://api.dojo.tech/terminal-sessions/' . $terminalSession . '/signature';

        $headers = [
            'Version' =>  env('DOJO_API_VERSION'),
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
