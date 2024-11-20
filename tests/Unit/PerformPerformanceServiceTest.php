<?php

namespace Tests\Unit;

use App\Http\Controllers\PerformanceController;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Http\Requests\PerformPerformanceRequest;

class PerformPerformanceServiceTest extends TestCase
{
   /** @test */
public function it_returns_performance_score_on_successful_response()
{
    Http::fake([
        'https://www.googleapis.com/pagespeedonline/v5/runPagespeed' => Http::response([
            'lighthouseResult' => [
                'categories' => [
                    'performance' => [
                        'score' => 1.00, 
                    ],
                ],
            ],
        ], 200),
    ]);

    $request = new PerformPerformanceRequest();
    $request->replace(['url' => 'https://alforsan.com', 'platform' => 'desktop']);
    $controller = new PerformanceController();
    $response = $controller->__invoke($request);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertJsonStringEqualsJsonString(
        json_encode(['performanceScore' => 100]), 
        $response->getContent()
    );
}

}
