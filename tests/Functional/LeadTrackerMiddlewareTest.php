<?php

namespace Railroad\LeadTracker\Tests\Functional;

use Illuminate\Http\Request;
use Railroad\LeadTracker\Middleware\LeadTrackerMiddleware;
use Railroad\LeadTracker\Tests\LeadTrackerTestCase;

class LeadTrackerMiddlewareTest extends LeadTrackerTestCase
{
    public function test_capture_request_success()
    {
        $brand = $this->faker->word;
        config()->set('lead-tracker.brand', $brand);

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
                'leadtracker_email' => $this->faker->email,
                'leadtracker_maropost_tag_name' => $this->faker->words(2, true),
                'leadtracker_form_name' => $this->faker->words(2, true),
                'leadtracker_utm_source' => $this->faker->word . rand(),
                'leadtracker_utm_medium' => $this->faker->word . rand(),
                'leadtracker_utm_campaign' => $this->faker->word . rand(),
                'leadtracker_utm_term' => $this->faker->words(2, true),
            ];

        $dataWithoutPrefix =
            [
                'brand' => $brand,
                'email' => $data['leadtracker_email'],
                'maropost_tag_name' => $data['leadtracker_maropost_tag_name'],
                'form_name' => $data['leadtracker_form_name'],
                'utm_source' => $data['leadtracker_utm_source'],
                'utm_medium' => $data['leadtracker_utm_medium'],
                'utm_campaign' => $data['leadtracker_utm_campaign'],
                'utm_term' => $data['leadtracker_utm_term'],
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

        $this->assertDatabaseHas('leadtracker_leads', $dataWithoutPrefix);
    }

    public function test_capture_request_no_match()
    {
        $formPath = '/test-path-2';
        $formUrl = 'https://www.leadtracker.com' . $formPath;

        config()->set(
            'lead-tracker.requests_to_capture',
            [
                [
                    'path' => $formPath . '-3',
                    'method' => 'get',
                ],
            ]
        );

        $data =
            [
                'leadtracker_email' => $this->faker->email,
                'leadtracker_maropost_tag_name' => $this->faker->words(2, true),
                'leadtracker_form_name' => $this->faker->words(2, true),
                'leadtracker_utm_source' => $this->faker->word . rand(),
                'leadtracker_utm_medium' => $this->faker->word . rand(),
                'leadtracker_utm_campaign' => $this->faker->word . rand(),
                'leadtracker_utm_term' => $this->faker->words(2, true),
            ];

        $dataWithoutPrefix =
            [
                'email' => $data['leadtracker_email'],
                'maropost_tag_name' => $data['leadtracker_maropost_tag_name'],
                'form_name' => $data['leadtracker_form_name'],
                'utm_source' => $data['leadtracker_utm_source'],
                'utm_medium' => $data['leadtracker_utm_medium'],
                'utm_campaign' => $data['leadtracker_utm_campaign'],
                'utm_term' => $data['leadtracker_utm_term'],
            ];

        $request = Request::create($formUrl, 'POST', $data);

        /**
         * @var $middleware LeadTrackerMiddleware
         */
        $middleware = app()->make(LeadTrackerMiddleware::class);

        $middleware->handle(
            $request,
            function ($req) {
            }
        );

        $this->assertDatabaseMissing('leadtracker_leads', $dataWithoutPrefix);
        $this->assertDatabaseMissing('leadtracker_leads', ['id' => 1]);
    }

    public function test_capture_request_data_missing()
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

        // form_name
        $data =
            [
                'leadtracker_email' => $this->faker->email,
                'leadtracker_maropost_tag_name' => $this->faker->words(2, true),

                'leadtracker_utm_source' => $this->faker->word . rand(),
                'leadtracker_utm_medium' => $this->faker->word . rand(),
                'leadtracker_utm_campaign' => $this->faker->word . rand(),
                'leadtracker_utm_term' => $this->faker->words(2, true),
            ];

        $request = Request::create($formUrl, 'POST', $data);

        /**
         * @var $middleware LeadTrackerMiddleware
         */
        $middleware = app()->make(LeadTrackerMiddleware::class);

        $middleware->handle(
            $request,
            function ($req) {
            }
        );

        // email
        $data =
            [

                'leadtracker_maropost_tag_name' => $this->faker->words(2, true),
                'leadtracker_form_name' => $this->faker->words(2, true),
                'leadtracker_utm_source' => $this->faker->word . rand(),
                'leadtracker_utm_medium' => $this->faker->word . rand(),
                'leadtracker_utm_campaign' => $this->faker->word . rand(),
                'leadtracker_utm_term' => $this->faker->words(2, true),
            ];

        $request = Request::create($formUrl, 'POST', $data);

        /**
         * @var $middleware LeadTrackerMiddleware
         */
        $middleware = app()->make(LeadTrackerMiddleware::class);

        $middleware->handle(
            $request,
            function ($req) {
            }
        );

        // maropost tag
        $data =
            [
                'leadtracker_email' => $this->faker->email,

                'leadtracker_form_name' => $this->faker->words(2, true),
                'leadtracker_utm_source' => $this->faker->word . rand(),
                'leadtracker_utm_medium' => $this->faker->word . rand(),
                'leadtracker_utm_campaign' => $this->faker->word . rand(),
                'leadtracker_utm_term' => $this->faker->words(2, true),
            ];

        $request = Request::create($formUrl, 'POST', $data);

        /**
         * @var $middleware LeadTrackerMiddleware
         */
        $middleware = app()->make(LeadTrackerMiddleware::class);

        $middleware->handle(
            $request,
            function ($req) {
            }
        );

        $this->assertDatabaseMissing('leadtracker_leads', ['id' => 1]);
    }
}
