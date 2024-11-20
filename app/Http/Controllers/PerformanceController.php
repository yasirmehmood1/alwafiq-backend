<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerformPerformanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(PerformPerformanceRequest $request)
    {
        try {
            $url = $request->input('url');
            $response = Http::retry(3, 100)->get("https://www.googleapis.com/pagespeedonline/v5/runPagespeed", [
                'url' => $url,
                'type' => strtolower( $request->input('platform')),
            ]);
            if ($response->successful()) {
                $data = $response->json();
                $performanceScore = $data['lighthouseResult']['categories']['performance']['score'];
                return response()->json(['performanceScore' => $performanceScore * 100]); 
            } else {
                return response()->json(['error' => 'Unable to fetch performance data'], 500);
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
