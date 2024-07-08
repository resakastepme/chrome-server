<?php

namespace App\Http\Controllers;

use App\Models\ExtLog;
use App\Models\LogDomain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function storeAnalisaDomain(Request $r)
    {
        $domain = $r['domain'];
        $id_user = $r['id_user'];
        $id_email = $r['id_analisa'];
        if (!$domain || !$id_user) {
            return response()->json([
                'status' => 'error',
                'message' => $domain . $id_user
            ]);
        }
        $ch = curl_init();
        $url = "https://www.virustotal.com/api/v3/domains/" . $domain;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $headers = array(
            'Content-Type: application/json',
            'X-apikey: c72b57abb6787b0854d428b9892c0a6a28a7076f7550a508ed8eb46d7326b4a8'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return response()->json([
                'error' => 'cURL error: ' . curl_error($ch)
            ]);
        } else {
            $responseData = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $harmless = $responseData['data']['attributes']['last_analysis_stats']['harmless'];
                $malicious = $responseData['data']['attributes']['last_analysis_stats']['malicious'];
                $suspicious = $responseData['data']['attributes']['last_analysis_stats']['suspicious'];
                $timeout = $responseData['data']['attributes']['last_analysis_stats']['timeout'];
                $undetected = $responseData['data']['attributes']['last_analysis_stats']['undetected'];
                $openphish = $responseData['data']['attributes']['last_analysis_results']['OpenPhish']['category'];
                $data = [
                    'id_email' => $id_email,
                    'domain' => $domain,
                    'harmless' => $harmless,
                    'malicious' => $malicious,
                    'suspicious' => $suspicious,
                    'timeout' => $timeout,
                    'undetected' => $undetected,
                    'openphish' => $openphish
                ];
                $q = LogDomain::create($data);
                if ($q) {
                    ExtLog::create([
                        'user_hash' => $id_user,
                        'message' => 'Successfully analyze domain!'
                    ]);
                    // return response()->json($responseData['data']['attributes']['last_analysis_stats']);
                    return response()->json($responseData['data']['attributes']['last_analysis_results']);
                } else {
                    ExtLog::create([
                        'user_hash' => $id_user,
                        'message' => 'Failure analyze domain!'
                    ]);
                    return response()->json($responseData['data']['attributes']['last_analysis_stats'], 'Database failure!');
                }
            } else {
                ExtLog::create([
                    'user_hash' => $id_user,
                    'message' => 'JSON decode error: ' . json_last_error_msg()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'JSON decode error: ' . json_last_error_msg()
                ]);
            }
        }
        curl_close($ch);
    }
}
