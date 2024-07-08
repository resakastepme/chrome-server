<?php

namespace App\Http\Controllers;

use App\Models\ExtLog;
use App\Models\LogUrl;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function storeAnalisaURL(Request $r)
    {
        $id_user = $r['id_user'];
        $id_analisa = $r['id_analisa'];
        $href = $r['href'];
        $index = $r['index'];
        $self_url = '';
        $test = array(
            $id_user, $id_analisa, $href, $index
        );
        if (!$id_user || !$id_analisa || !$href || !$index) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not complete!',
                'data' => $test
            ]);
        }

        function getFinalUrl($url)
        {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            curl_exec($ch);

            $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            curl_close($ch);

            return $finalUrl;
        }

        $finalUrl = getFinalUrl($href);

        $ch = curl_init();
        $url = "https://www.virustotal.com/api/v3/urls";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        $data = http_build_query(array(
            'url' => $finalUrl
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $headers = array(
            'accept: application/json',
            'x-apikey: c72b57abb6787b0854d428b9892c0a6a28a7076f7550a508ed8eb46d7326b4a8',
            'content-type: application/x-www-form-urlencoded'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json([
                'error' => 'cURL error: ' . curl_error($ch)
            ]);
        } else {
            $data = json_decode($response, true);
            $id = $data['data']['id'];
            $data['data']['links']['self'] = $self_url;
            preg_match('/u-(.*?)-/', $id, $matches);
            $extractedId = $matches[1];

            if (json_last_error() === JSON_ERROR_NONE) {

                $ch = curl_init();
                $url = "https://www.virustotal.com/api/v3/urls/" . $extractedId;
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $headers = array(
                    'accept: application/json',
                    'x-apikey: c72b57abb6787b0854d428b9892c0a6a28a7076f7550a508ed8eb46d7326b4a8',
                    'content-type: application/x-www-form-urlencoded'
                );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($ch);
                if ($response == false) {
                    ExtLog::create([
                        'user_hash' => $id_user,
                        'message' => 'JSON decode error: 1 ' . json_last_error_msg()
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'JSON decode error: 2 ' . json_last_error_msg()
                    ]);
                } else {
                    $dataJson = json_decode($response, true);
                    $dataAnalyze = $dataJson['data']['attributes']['last_analysis_stats'];

                    $data = [
                        'id_email' => $id_analisa,
                        'href' => $href,
                        'harmless' => $dataJson['data']['attributes']['last_analysis_stats']['harmless'],
                        'malicious' => $dataJson['data']['attributes']['last_analysis_stats']['malicious'],
                        'suspicious' => $dataJson['data']['attributes']['last_analysis_stats']['suspicious'],
                        'timeout' => $dataJson['data']['attributes']['last_analysis_stats']['timeout'],
                        'undetected' => $dataJson['data']['attributes']['last_analysis_stats']['undetected'],
                        'self_url' => $self_url
                    ];
                    $q = LogUrl::create($data);
                    if ($q) {
                        ExtLog::create([
                            'user_hash' => $id_user,
                            'message' => 'URL Stored!'
                        ]);
                    } else {
                        ExtLog::create([
                            'user_hash' => $id_user,
                            'message' => 'URL Stored fail!'
                        ]);
                    }
                    return response()->json([
                        'status' => 'ok',
                        'data' => $dataAnalyze,
                        'index' => $index
                    ]);
                }
                curl_close($ch);
            } else {
                ExtLog::create([
                    'user_hash' => $id_user,
                    'message' => 'JSON decode error: 3 ' . json_last_error_msg()
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'JSON decode error: 4 ' . json_last_error_msg()
                ]);
            }
        }

        curl_close($ch);
    }
}
