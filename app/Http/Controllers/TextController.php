<?php

namespace App\Http\Controllers;

use App\Models\ExtLog;
use App\Models\LogText;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TextController extends Controller
{
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
        $GT->setTarget('en');
        $translated = $GT->translate($original);
        $data = [
            'id_email' => $id_email,
            'original' => $original,
            'translated' => $translated,
            'assistant' => $assistant
        ];
        $q1 = LogText::create($data);
        $data2 = [
            'id_user' => $id_user,
            'original' => $original,
            'translated' => $translated,
            'assistant' => $assistant
        ];
        $q2 = Dataset::create($data2);
        if ($q1 && $q2) {
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

    public function translate(Request $r) {
        $text = $r['text'];
        $tr = new GoogleTranslate();
        $tr->setSource();
        $tr->setTarget('en');
        $translated = $tr->translate($text);
        return response()->json([
            'textTranslated' => $translated
        ]);
    }
}
