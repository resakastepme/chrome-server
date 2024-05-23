<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\ExtLog;
use App\Models\LogUrl;
use App\Models\LogText;
use App\Models\LogDomain;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class EmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function storeIDAnalisa(Request $r)
    {
        $id_user = $r['id_user'];
        $id_analisa = $r['id_analisa'];
        $title = $r['title'];
        if (!$id_user || !$id_analisa || !$title) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not complete!'
            ]);
        }
        ExtLog::create([
            'user_hash' => $id_user,
            'message' => 'Doing store ID Email!'
        ]);
        $data1 = [
            'id_user' => $id_user,
            'id_analisa' => $id_analisa,
            'title' => $title
        ];
        $q1 = Email::create($data1);
        if ($q1) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Email data stored!'
            ]);
        } else {
            $log = ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Fail to store email data!'
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Log id: ' . $log->id
            ]);
        }
    }

    public function storeAnalisaText(Request $r)
    {
        $id_email = $r['id_email'];
        $original = $r['original'];
        $assistant = $r['assistant'];
        $id_user = $r['id_user'];
        if (!$id_email || !$original || !$assistant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not complete!'
            ]);
        }
        ExtLog::create([
            'user_hash' => $id_user,
            'message' => 'Doing analyze Text!'
        ]);
        $GT = new GoogleTranslate();
        $GT->setSource();
        $GT->setTarget('id');
        $translated = $GT->translate($original);
        $data = [
            'id_email' => $id_email,
            'original' => $original,
            'translated' => $translated,
            'assistant' => $assistant
        ];
        $q1 = LogText::create($data);
        if ($q1) {
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Analyze Text Stored!'
            ]);
            return response()->json([
                'status' => 'ok',
                'message' => 'Analisa Text Stored Successfully!'
            ]);
        } else {
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Analyze Text Stored fail!'
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Database query error!'
            ]);
        }
    }

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
                $data = [
                    'id_email' => $id_email,
                    'domain' => $domain,
                    'harmless' => $harmless,
                    'malicious' => $malicious,
                    'suspicious' => $suspicious,
                    'timeout' => $timeout,
                    'undetected' => $undetected
                ];
                $q = LogDomain::create($data);
                if ($q) {
                    ExtLog::create([
                        'user_hash' => $id_user,
                        'message' => 'Successfully analyze domain!'
                    ]);
                    return response()->json($responseData['data']['attributes']['last_analysis_stats']);
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
        $ch = curl_init();
        $url = "https://www.virustotal.com/api/v3/urls";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        $data = http_build_query(array(
            'url' => $href
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
                    if($q){
                        ExtLog::create([
                            'user_hash' => $id_user,
                            'message' => 'URL Stored!'
                        ]);
                    }else{
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
