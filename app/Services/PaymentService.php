<?php

namespace App\Services;

use App\Helpers\PaymentMask;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Http;


class PaymentService
{
    public function charge(array $payload): array
    {
        $maskedRequest = PaymentMask::maskRequestPayload($payload);

        try {///önemli

            $response = Http::withHeaders([
                    'Key' => config('payments.providers.mock_api.access_key'),
                    'Secret' => config('payments.providers.mock_api.access_secret_key'),
                    'Accept'    => 'application/json',
                ])
                ->post(config('payments.providers.mock_api.api_url').'/api.php',$payload);
            ///   
            $rawResponse = $response->json() ?? [];
            
            $maskedResponse = PaymentMask::maskResponsePayload($rawResponse);
            $success = $response->successful() && ($rawResponse['status'] == 'success' ?? false);
            
            PaymentLog::create([
                'user_id' => request()->user()->id,
                'providers' => 'mock',
                'request_payload_masked' => $maskedRequest,
                'response_payload_masked' => $maskedResponse,
                'status' => $success ? 'success' : 'failure',
                'amount' => $payload['amount'] ?? 0,
                'currency' => $payload['currency'] ?? 'TRY',
                'external_ref' => $rawResponse['reference'] ?? null,
                'error_message' => $success ? null : ($rawResponse['message'] ?? 'Unknown error'),
            ]);

            return [
                'success' => $success,
                'external_ref' => $rawResponse['reference'] ?? null,
                'message' => $rawResponse['message'] ?? null,
                'raw' => $rawResponse,
            ];
        } catch (\Throwable $e) {
            PaymentLog::create([
                'user_id' => request()->user()->id,
                'providers' => 'mock',
                'request_payload_masked' => $maskedRequest,
                'response_payload_masked' => ['exception' => class_basename($e), 'message' => 'masked'],
                'status' => 'failure',
                'amount' => $payload['amount'] ?? 0,
                'currency' => $payload['currency'] ?? 'TRY',
                'external_ref' => null,
                'error_message' => 'Request failed' . $e->getMessage(),
            ]);

            return [
                'success' => false,
                'external_ref' => null,
                'message' => 'Request failed',
                'raw' => [],
            ];
        }
    }
}



