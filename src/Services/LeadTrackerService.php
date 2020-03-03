<?php


namespace Railroad\LeadTracker\Services;


use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Railroad\LeadTracker\Events\LeadTracked;

class LeadTrackerService
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * LeadTrackerService constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
        $this->databaseConnection = $databaseManager->connection(config('lead-tracker.database_connection_name'));
    }

    /**
     * Returns the info inserted or that already existed in the database as an array.
     *
     * @param string $email
     * @param string $maropostTagName
     * @param string $formName
     * @param string $formUrl
     * @param string|null $utmSource
     * @param string|null $utmMedium
     * @param string|null $utmCampaign
     * @param string|null $utmTerm
     * @return array
     */
    public function trackLead(
        $email,
        $maropostTagName,
        $formName,
        $formUrl,
        $utmSource = null,
        $utmMedium = null,
        $utmCampaign = null,
        $utmTerm = null
    )
    {
        $dataArray = [
            'email' => $email,
            'maropost_tag_name' => $maropostTagName,
            'form_name' => $formName,
            'form_page_url' => $formUrl,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_term' => $utmTerm,
        ];

        if (!$this->databaseConnection->table('leadtracker_leads')->where($dataArray)->exists()) {
            $this->databaseConnection->table('leadtracker_leads')->insert(
                [
                    'email' => $email,
                    'maropost_tag_name' => $maropostTagName,
                    'form_name' => $formName,
                    'form_page_url' => $formUrl,
                    'utm_source' => $utmSource,
                    'utm_medium' => $utmMedium,
                    'utm_campaign' => $utmCampaign,
                    'utm_term' => $utmTerm,
                    'submitted_at' => Carbon::now()->toDateTimeString(),
                ]
            );
        }

        $databaseArray = (array)$this->databaseConnection->table('leadtracker_leads')->where($dataArray)->first();

        event(new LeadTracked($databaseArray));

        return $databaseArray;
    }
}