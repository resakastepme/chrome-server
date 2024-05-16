<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\Dataset;

class DatasetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $text = $_POST['text'];
        if(!$text){
            return response()->json([
                'status' => 'error',
                'message' => 'Text is empty, process canceled!'
            ]);
        }
        $data = [
            'text' => $text
        ];
        $q = Dataset::create($data);
        if($q){
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
