<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use App\Models\LogText;
use App\Models\LogDomain;
use App\Models\LogUrl;
use App\Models\LogFile;

class RiwayatController extends Controller
{
    public function index(Request $r)
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
