<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\Email;
use App\Models\ExtLog;
use App\Models\LogUrl;
use App\Models\LogFile;
use App\Models\LogText;
use App\Models\LogDomain;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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
        $from = $r['from'];
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
            'title' => $title,
            'sender' => $from
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

    // TEXT CONTROLLER ✅
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

    // DOMAIN CONTROLLER ✅
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

    // URL CONTROLLER ✅
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

    // FILE CONTROLLER ✅
    public function mimeToExtension($mime)
    {
        $mimeToExt = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'application/x-tar' => 'tar',
            'application/x-7z-compressed' => '7z',
            'application/octet-stream' => 'dll',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'video/mp4' => 'mp4',
            'video/x-msvideo' => 'avi',
            'audio/mpeg' => 'mp3',
            'text/plain' => 'txt',
            'text/html' => 'html',
        ];

        return $mimeToExt[$mime] ?? null;
    }

    // FILE CONTROLLER ✅
    public function zipIt($fixName, $id_user)
    {
        $zip = new ZipArchive();

        $folderPath = 'public/zip-' . $id_user;
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, 0777, true);
        }

        $fixNameBasename = basename($fixName);
        $zipPath = public_path('storage/zip-' . $id_user . '/' . $fixNameBasename . '.zip');

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $filePath = public_path('storage/' . $id_user . '/' . $fixName);
            $nameInZipFile = basename($filePath);
            $zip->addFile($filePath, $nameInZipFile);
            $zip->close();
            return $zipPath;
        } else {
            return 'Failed to create zip file';
        }
    }

    // FILE CONTROLLER ✅
    public function getAnalyzeFile(Request $r)
    {
        $selfLink = $r['selfLink'];
        $index = $r['index'];
        $query_id = $r['query_id'];
        $id_user = $r['id_user'];
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $selfLink, [
            'headers' => [
                'accept' => 'application/json',
                'x-apikey' => 'c72b57abb6787b0854d428b9892c0a6a28a7076f7550a508ed8eb46d7326b4a8',
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        $status = $responseBody['data']['attributes']['status'];
        $malicious = $responseBody['data']['attributes']['stats']['malicious'];
        $suspicious = $responseBody['data']['attributes']['stats']['suspicious'];

        if($status == 'queued'){
            return response()->json([
                'status' => 'ok',
                'index' => $index,
                'data' => $responseBody
            ]);
        }else{
            LogFile::where('id', $query_id)->update([
                'suspicious' => $suspicious,
                'malicious' => $malicious
            ]);
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Analyze file complete'
            ]);
            return response()->json([
                'status' => 'ok',
                'index' => $index,
                'data' => $responseBody
            ]);
        }

    }

    // FILE CONTROLLER ✅
    public function storeAnalisaFile(Request $r)
    {
        $id_user = $r['id_user'];
        $id_analisa = $r['id_analisa'];
        $base64 = $r['base64'];
        $index = $r['index'];
        $name = $r['name'];

        if (!$id_user || !$id_analisa || !$base64 || !$index) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not complete!'
            ]);
        }

        $mime = finfo_buffer(finfo_open(), base64_decode($base64), FILEINFO_MIME_TYPE);
        $decodedData = base64_decode($base64);
        $folderPath = 'public/' . $id_user;
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, 0777, true);
        }
        $fixName = time() . Str::random(5) . '.' . $this->mimeToExtension($mime);
        $save = Storage::disk('public')->put($id_user . '/' . $fixName, $decodedData);

        $lastThing = $this->zipIt($fixName, $id_user);

        $filePath = public_path('storage/zip-' . $id_user . '/' . $fixName . '.zip');
        $fileContents = file_get_contents($filePath);
        $base64Zip = base64_encode($fileContents);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://www.virustotal.com/api/v3/files', [
            'multipart' => [
                [
                    'name' => 'file',
                    'filename' => $fixName . '.zip',
                    'contents' => 'data:application/x-zip-compressed;name=' . $fixName . '.zip;base64,' . $base64Zip,
                    'headers' => [
                        'Content-Type' => 'application/x-zip-compressed'
                    ]
                ]
            ],
            'headers' => [
                'accept' => 'application/json',
                'x-apikey' => 'c72b57abb6787b0854d428b9892c0a6a28a7076f7550a508ed8eb46d7326b4a8',
            ],
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        $selfLink = $responseBody['data']['links']['self'];

        // $veryLast = $this->getAnalyzeFile($selfLink);

        if ($save) {

            $data = [
                'id_email' => $id_analisa,
                'name' => $name,
                'self_url' => $selfLink
            ];
            $logfile = LogFile::create($data);
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Initiate analyze file'
            ]);
            return response()->json([
                'status' => 'ok',
                'selfLink' => $selfLink,
                'index' => $index,
                'query_id' => $logfile->id
            ]);
        } else {
            $logext = ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Fail Initiate analyze file'
            ]);
            return response()->json([
                'status' => 'error',
                'index' => $index
            ]);
        }
    }

    // FILE CONTROLLER ✅
    public function returnFinalURL(Request $r)
    {
        $url = $r['url'];
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_exec($ch);

        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

        return response()->json([$finalUrl]);
    }

    // TO GET RIWAYAT
    public function getRiwayat(Request $r)
    {
        $id_user = $r['id_user'];
        $q = Email::where('id_user', $id_user)->orderBy('id', 'desc')->get();
        if ($q) {
            return response()->json([
                'status' => 'ok',
                'datas' => $q
            ]);
        } else {
            return response()->json([
                'status' => 'not ok'
            ]);
        }
    }

    // TO GET RIWAYAT DETAIL
    public function riwayatDetail(Request $r)
    {
        $idAnalisa = $r['idAnalisa'];
        $idUser = $r['idUser'];

        try {
            $qEmail = Email::where('id_user', $idUser)->where('id_analisa', $idAnalisa)->first();
            $qText = LogText::where('id_email', $idAnalisa)->first();
            $qDomain = LogDomain::where('id_email', $idAnalisa)->get();
            $qUrl = LogUrl::where('id_email', $idAnalisa)->get();
            $qFile = LogFile::where('id_email', $idAnalisa)->get();

            $judul = $qEmail['title'];
            $pengirim = $qEmail['sender'];
            $tanggal = $qEmail['created_at'];

            return response()->json([
                'status' => 'ok',
                'judul' => $judul,
                'pengirim' => $pengirim,
                'tanggal' => $tanggal,
                'ringkasan' => $qText,
                'domain' => $qDomain,
                'url' => $qUrl,
                'file' => $qFile
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => $th->getMessage()
            ]);
        }
    }
}
