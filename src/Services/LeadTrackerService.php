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
            'brand' => config('lead-tracker.brand'),
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
                array_merge(
                    $dataArray,
                    [
                        'submitted_at' => Carbon::now()->toDateTimeString(),
                    ]
                )
            );
        }

        $databaseArray = (array)$this->databaseConnection->table('leadtracker_leads')->where($dataArray)->first();

        event(new LeadTracked($databaseArray));

        return $databaseArray;
    }

    /**
     * This returns all the form data that should be sent with the form submit request to be captured by LeadTracker.
     * The only input varaible that is not returned here is the email since that's the input field on the page.
     *
     * [inputName => inputValue]
     *
     * @param $formSubmitPath
     * @param $formSubmitMethod
     * @param $maropostTagName
     * @param $formName
     * @return array
     */
    public static function getRequestTrackingInputArrayFromRequest(
        $formSubmitPath,
        $formSubmitMethod,
        $maropostTagName,
        $formName
    )
    {
        $request = request();

        foreach (config('lead-tracker.requests_to_capture') as $requestToCaptureData) {

            if (strtolower($formSubmitMethod) == strtolower($requestToCaptureData['method']) &&
                strtolower(trim($formSubmitPath, '/')) == strtolower(trim($requestToCaptureData['path'], '/'))) {

                $inputDataMap = $requestToCaptureData['input_data_map'];

                return [
                    $inputDataMap['maropost_tag_name'] => $maropostTagName,
                    $inputDataMap['form_name'] => $formName,
                    $inputDataMap['utm_source'] => $request->get('utm_source'),
                    $inputDataMap['utm_medium'] => $request->get('utm_medium'),
                    $inputDataMap['utm_campaign'] => $request->get('utm_campaign'),
                    $inputDataMap['utm_term'] => $request->get('utm_term'),
                ];
            }
        }

        return [];
    }

    /**
     * This returns all the form inputs HTML that should be in the form to be captured by LeadTracker.
     *
     * @param $formSubmitPath
     * @param $formSubmitMethod
     * @param $maropostTagName
     * @param $formName
     * @return string
     */
    public static function getRequestTrackingInputsHtmlFromRequest(
        $formSubmitPath,
        $formSubmitMethod,
        $maropostTagName,
        $formName
    )
    {
        $inputArray = self::getRequestTrackingInputArrayFromRequest(
            $formSubmitPath,
            $formSubmitMethod,
            $maropostTagName,
            $formName
        );

        $html = '';

        foreach ($inputArray as $inputName => $inputValue) {
            if (!empty($inputValue)) {
                $html .= "<input type='hidden' name='$inputName' value='$inputValue'>\n";
            }
        }

        return $html;
    }
}