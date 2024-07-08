<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExtUser;
use App\Models\ExtLog;

class ExtUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function validating(Request $r)
    {
        $user_hash = $r['user_hash'];
        $device = $r['device'];
        if (!$user_hash || !$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not complete!'
            ]);
        }
        $q1 = ExtUser::where('user_hash', $user_hash)->first();
        if ($q1) {
            $log = ExtLog::create([
                'user_hash' => $user_hash,
                'message' => 'UserId is Valid!'
            ]);
            $log_id = $log->id;
            return response()->json([
                'status' => 'ok',
                'message' => 'Log id: ' . $log_id
            ]);
        } else {
            $data = [
                'user_hash' => $user_hash,
                'device' => $device
            ];
            $q2 = ExtUser::create($data);
            if ($q2) {
                $log = ExtLog::create([
                    'user_hash' => $user_hash,
                    'message' => 'New UserId is Valid!'
                ]);
                $log_id = $log->id;
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Log id: ' . $log_id
                ]);
            } else {
                $log = ExtLog::create([
                    'user_hash' => $user_hash,
                    'message' => 'New UserId, Fail to add!'
                ]);
                $log_id = $log->id;
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error Log Id: ' . $log_id
                ]);
            }
        }
    }

    public function change(Request $r)
    {
        $user_hash = $r['user_hash'];

        if (!$user_hash) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not complete!'
            ]);
        }

        $q1 = ExtUser::where('user_hash', $user_hash)->first();

        if ($q1) {
            ExtLog::create([
                'user_hash' => $user_hash,
                'message' => 'Changing userId completed!'
            ]);
            return response()->json([
                'status' => 'ok',
                'message' => $q1->user_hash
            ]);
        } else {
            $l1 = ExtLog::create([
                'user_hash' => $user_hash,
                'message' => 'Changing userId failed!'
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Log: ' . $l1->id
            ]);
        }
    }
}
