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

        $data =
            [
                'my_email_input_name' => $this->faker->email,
                'my_maropost_tag_name_input_name' => $this->faker->words(2, true),
                'my_form_name_input_name' => $this->faker->words(2, true),
                'my_utm_source_input_name' => $this->faker->word . rand(),
                'my_utm_medium_input_name' => $this->faker->word . rand(),
                'my_utm_campaign_input_name' => $this->faker->word . rand(),
                'my_utm_term_input_name' => $this->faker->words(2, true),
            ];

        config()->set(
            'lead-tracker.requests_to_capture',
            [
                [
                    'path' => $formPath,
                    'method' => 'post',
                    'form_name' => $data['my_form_name_input_name'],
                    'input_data_map' => [
                        'email' => 'my_email_input_name',
                        'maropost_tag_name' => 'my_maropost_tag_name_input_name',
                        'form_name' => 'my_form_name_input_name',
                        'utm_source' => 'my_utm_source_input_name',
                        'utm_medium' => 'my_utm_medium_input_name',
                        'utm_campaign' => 'my_utm_campaign_input_name',
                        'utm_term' => 'my_utm_term_input_name',
                    ],
                ],
            ]
        );

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

        $this->assertDatabaseHas(
            'leadtracker_leads',
            [
                'brand' => $brand,
                'email' => $data['my_email_input_name'],
                'maropost_tag_name' => $data['my_maropost_tag_name_input_name'],
                'form_name' => $data['my_form_name_input_name'],
                'utm_source' => $data['my_utm_source_input_name'],
                'utm_medium' => $data['my_utm_medium_input_name'],
                'utm_campaign' => $data['my_utm_campaign_input_name'],
                'utm_term' => $data['my_utm_term_input_name'],
            ]
        );
    }

    public function test_capture_request_success_multiple_with_same_form_path_and_method()
    {
        $brand = $this->faker->word;
        config()->set('lead-tracker.brand', $brand);

        $formPath = '/test-path';
        $formUrl = 'https://www.leadtracker.com' . $formPath;
        $formName = 'my-form-1';
        $formMethod = 'post';

        $data =
            [
                'my_email_input_name' => $this->faker->email,
                'my_maropost_tag_name_input_name' => $this->faker->words(2, true),
                'my_form_name_input_name' => $formName,
                'my_utm_source_input_name' => $this->faker->word . rand(),
                'my_utm_medium_input_name' => $this->faker->word . rand(),
                'my_utm_campaign_input_name' => $this->faker->word . rand(),
                'my_utm_term_input_name' => $this->faker->words(2, true),
            ];


        config()->set(
            'lead-tracker.requests_to_capture',
            [
                [
                    'path' => $formPath,
                    'method' => $formMethod,
                    'form_name' => 'my-other-form-that-should-not-be-tracked',
                    'input_data_map' => [
                        'email' => 'other_my_email_input_name',
                        'maropost_tag_name' => 'other_my_maropost_tag_name_input_name',
                        'form_name' => 'other_my_form_name_input_name',
                        'utm_source' => 'other_my_utm_source_input_name',
                        'utm_medium' => 'other_my_utm_medium_input_name',
                        'utm_campaign' => 'other_my_utm_campaign_input_name',
                        'utm_term' => 'other_my_utm_term_input_name',
                    ],
                ],
                [
                    'path' => $formPath,
                    'method' => $formMethod,
                    'form_name' => $formName,
                    'input_data_map' => [
                        'email' => 'my_email_input_name',
                        'maropost_tag_name' => 'my_maropost_tag_name_input_name',
                        'form_name' => 'my_form_name_input_name',
                        'utm_source' => 'my_utm_source_input_name',
                        'utm_medium' => 'my_utm_medium_input_name',
                        'utm_campaign' => 'my_utm_campaign_input_name',
                        'utm_term' => 'my_utm_term_input_name',
                    ],
                ],
            ]
        );

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

        $this->assertDatabaseHas(
            'leadtracker_leads',
            [
                'brand' => $brand,
                'email' => $data['my_email_input_name'],
                'maropost_tag_name' => $data['my_maropost_tag_name_input_name'],
                'form_name' => $data['my_form_name_input_name'],
                'utm_source' => $data['my_utm_source_input_name'],
                'utm_medium' => $data['my_utm_medium_input_name'],
                'utm_campaign' => $data['my_utm_campaign_input_name'],
                'utm_term' => $data['my_utm_term_input_name'],
            ]
        );
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
                    'input_data_map' => [
                        'email' => 'leadtracker_email',
                        'maropost_tag_name' => 'leadtracker_maropost_tag_name',
                        'form_name' => 'leadtracker_form_name',
                        'utm_source' => 'leadtracker_utm_source',
                        'utm_medium' => 'leadtracker_utm_medium',
                        'utm_campaign' => 'leadtracker_utm_campaign',
                        'utm_term' => 'leadtracker_utm_term',
                    ],
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
                    'form_name' => 'my-form',
                    'input_data_map' => [
                        'email' => 'leadtracker_email',
                        'maropost_tag_name' => 'leadtracker_maropost_tag_name',
                        'form_name' => 'leadtracker_form_name',
                        'utm_source' => 'leadtracker_utm_source',
                        'utm_medium' => 'leadtracker_utm_medium',
                        'utm_campaign' => 'leadtracker_utm_campaign',
                        'utm_term' => 'leadtracker_utm_term',
                    ],
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
