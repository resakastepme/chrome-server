<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dataset;
use Stichoza\GoogleTranslate\GoogleTranslate;

class DatasetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(Request $r)
    {
        $text = $r['text'];
        if(!$text){
            return response()->json([
                'status' => 'error',
                'message' => 'Text is empty, process canceled!'
            ]);
        }
        $GT = new GoogleTranslate();
        $GT->setSource();
        $GT->setTarget('id');
        $translated = $GT->translate($text);
        $data = [
            'original' => $text,
            'translated' => $translated
        ];
        $q1 = Dataset::create($data);
        if($q1){
            return response()->json([
                'status' => 'ok',
                'message' => 'Successfuly added!'
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Fail to execute!'
            ]);
        }
    }
}
