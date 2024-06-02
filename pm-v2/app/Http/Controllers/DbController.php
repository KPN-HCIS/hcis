<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DbController extends Controller
{
    function dbauth(Request $req) {

    $dataValue = $req->query('data');

    // dd($dataValue);

    // Step 2: Decrypt using base64
    $decodedData = base64_decode($dataValue);

    // Step 3: Decrypt using XOR with the provided key
    $xoredData = $this->xorDecrypt($decodedData, '8888888');

    // Step 4: Decrypt using base64 again
    $finalData = base64_decode($xoredData);

    // $finalData now contains the decrypted JSON payload
    $decodedPayload = json_decode($finalData, true);

    // Dump and die to inspect the decrypted payload
    dd($finalData);

    // Use the decrypted payload as needed in your logic
    // For example:
    $userId = $decodedPayload['user_id'];

    // Return response or do further processing
    return response()->json(['user_id' => $userId]);

    }

    // XOR decryption function
    private function xorDecrypt($data, $key)
    {
        $keyLength = strlen($key);
        $dataLength = strlen($data);
        $decrypted = '';

        for ($i = 0; $i < $dataLength; $i++) {
            $decrypted .= $data[$i] ^ $key[$i % $keyLength];
        }

        return $decrypted;
    }
}
