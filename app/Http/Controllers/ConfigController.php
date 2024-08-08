<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    /**
     * Get the quota limit from the config table.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuotaLimit()
    {
        try {
            // Fetch the quota limit from the config table
            $config = DB::table('config')->where('name', 'quota_limit')->first();

            if (!$config) {
                return response()->json(['error' => 'Quota limit not found in config.'], 404);
            }

            return response()->json(['quota_limit' => $config->value, 'unit' => "(B) Bytes", "conversion" => "1024B = 1KB"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the quota limit.'], 500);
        }
    }
}
