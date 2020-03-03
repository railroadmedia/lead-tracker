<?php


namespace Railroad\LeadTracker\Services;


use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

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
     * @param string $email
     * @param string $maropostTagName
     * @param string $formName
     * @param string $formUrl
     * @param string|null $utmSource
     * @param string|null $utmMedium
     * @param string|null $utmCampaign
     * @param string|null $utmTerm
     * @return bool
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
            return $this->databaseConnection->table('leadtracker_leads')->insert(
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

        return true;
    }
}