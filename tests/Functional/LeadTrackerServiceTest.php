<?php

namespace Railroad\LeadTracker\Tests\Functional;

use Carbon\Carbon;
use Railroad\LeadTracker\Events\LeadTracked;
use Railroad\LeadTracker\Services\LeadTrackerService;
use Railroad\LeadTracker\Tests\LeadTrackerTestCase;

class LeadTrackerServiceTest extends LeadTrackerTestCase
{
    /**
     * @var LeadTrackerService
     */
    private $leadTrackerService;

    protected function setUp()
    {
        parent::setUp();

        $this->leadTrackerService = app()->make(LeadTrackerService::class);
    }

    public function test_track_lead_success()
    {
        $data =
            [
                'email' => $this->faker->email,
                'maropost_tag_name' => $this->faker->words(2, true),
                'form_name' => $this->faker->words(2, true),
                'form_page_url' => $this->faker->url,
                'utm_source' => $this->faker->word . rand(),
                'utm_medium' => $this->faker->word . rand(),
                'utm_campaign' => $this->faker->word . rand(),
                'utm_term' => $this->faker->words(2, true),
            ];

        $this->expectsEvents([LeadTracked::class]);

        $inserted = $this->leadTrackerService->trackLead(
            $data['email'],
            $data['maropost_tag_name'],
            $data['form_name'],
            $data['form_page_url'],
            $data['utm_source'],
            $data['utm_medium'],
            $data['utm_campaign'],
            $data['utm_term']
        );

        $this->assertEquals(
            array_merge(['id' => 1, 'submitted_at' => Carbon::now()->toDateTimeString()], $data),
            $inserted
        );

        $this->assertDatabaseHas('leadtracker_leads', $data);
    }

    public function test_track_lead_no_duplicates()
    {
        $data =
            [
                'email' => $this->faker->email,
                'maropost_tag_name' => $this->faker->words(2, true),
                'form_name' => $this->faker->words(2, true),
                'form_page_url' => $this->faker->url,
                'utm_source' => $this->faker->word . rand(),
                'utm_medium' => $this->faker->word . rand(),
                'utm_campaign' => $this->faker->word . rand(),
                'utm_term' => $this->faker->words(2, true),
            ];

        $this->expectsEvents([LeadTracked::class]);

        $inserted = $this->leadTrackerService->trackLead(
            $data['email'],
            $data['maropost_tag_name'],
            $data['form_name'],
            $data['form_page_url'],
            $data['utm_source'],
            $data['utm_medium'],
            $data['utm_campaign'],
            $data['utm_term']
        );

        $inserted = $this->leadTrackerService->trackLead(
            $data['email'],
            $data['maropost_tag_name'],
            $data['form_name'],
            $data['form_page_url'],
            $data['utm_source'],
            $data['utm_medium'],
            $data['utm_campaign'],
            $data['utm_term']
        );

        $this->assertEquals(
            array_merge(['id' => 1, 'submitted_at' => Carbon::now()->toDateTimeString()], $data),
            $inserted
        );

        $this->assertDatabaseHas('leadtracker_leads', $data);
        $this->assertDatabaseMissing('leadtracker_leads', ['id' => 2]);
    }
}
