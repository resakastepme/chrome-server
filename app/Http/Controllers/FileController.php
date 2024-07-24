<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\ExtLog;
use App\Models\LogFile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
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
    public function storeAnalisaFile(Request $r)
    {
        $id_user = $r['id_user'];
        $id_analisa = $r['id_analisa'];
        $base64 = $r['base64'];
        $index = $r['index'];
        $name = $r['name'];
        $ext = $r['ext'];

        if (!$id_user || !$id_analisa || !$base64 || !$index || !$name) {
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

        if ($ext == 1) {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://chrome.server.resaka.my.id/api/v1/store-file-data', [
                'json' => [
                    'id_email' => $id_analisa,
                    'name' => $name,
                    'self_url' => $selfLink,
                    'id_user' => $id_user,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer 1|zaQoCF4MGINb2JKOGwrKa2Tk3KtJEEHINUZLX7yM160d4f8f',
                ],
            ]);
        }


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

    public function storeFileData(Request $r)
    {
        $id_email = $r['id_email'];
        $name = $r['name'];
        $self_url = $r['self_url'];
        $id_user = $r['id_user'];

        $data = [
            'id_email' => $id_email,
            'name' => $name,
            'self_url' => $self_url
        ];
        $logfile = LogFile::create($data);
        if ($logfile) {
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Storing file data to external'
            ]);
            return response()->json(['store file data success']);
        } else {
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Failed Storing file data to external'
            ]);
            return response()->json(['store file data failed']);
        }
    }

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

        if ($status == 'queued') {
            return response()->json([
                'status' => 'ok',
                'index' => $index,
                'data' => $responseBody
            ]);
        } else {
            LogFile::where('id', $query_id)->update([
                'suspicious' => $suspicious,
                'malicious' => $malicious,
                'malwarebytes' => $responseBody['data']['attributes']['results']['Malwarebytes']['category']
            ]);
            ExtLog::create([
                'user_hash' => $id_user,
                'message' => 'Analyze file complete'
            ]);
            return response()->json([
                'status' => 'ok',
                'index' => $index,
                'data' => $responseBody,
                'malwarebytes' => $responseBody['data']['attributes']['results']['Malwarebytes']['category']
            ]);
        }
    }

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
}
