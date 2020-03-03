<?php

namespace Railroad\LeadTracker\Tests\Functional;

use Illuminate\Http\Request;
use Railroad\LeadTracker\Middleware\LeadTrackerMiddleware;
use Railroad\LeadTracker\Tests\LeadTrackerTestCase;

class AccessCodeJsonControllerTest extends LeadTrackerTestCase
{
    public function test_test()
    {
        $formPath = '/test-path';
        $formUrl = 'https://www.leadtracker.com' . $formPath;

        config()->set(
            'lead-tracker.requests_to_capture',
            [
                [
                    'path' => $formPath,
                    'method' => 'post',
                ],
            ]
        );

        $data =
            [
                'email' => $this->faker->email,
                'maropost_tag_name' => $this->faker->words(2, true),
                'form_name' => $this->faker->words(2, true),
                'form_page_url' => $formUrl,
                'utm_source' => $this->faker->word . rand(),
                'utm_medium' => $this->faker->word . rand(),
                'utm_campaign' => $this->faker->word . rand(),
                'utm_term' => $this->faker->words(2, true),
            ];

        $request = Request::create($formUrl, 'POST', $data);

        /**
         * @var $middleware LeadTrackerMiddleware
         */
        $middleware = app()->make(LeadTrackerMiddleware::class);

        $middleware->handle(
            $request,
            function ($req) {
                $this->assertEquals(true, true);
            }
        );

        $this->assertDatabaseHas('leadtracker_leads', $data);
    }
}
