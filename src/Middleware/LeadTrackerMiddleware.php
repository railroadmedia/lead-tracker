<?php

namespace Railroad\LeadTracker\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Railroad\LeadTracker\Services\LeadTrackerService;
use Throwable;

class LeadTrackerMiddleware
{
    /**
     * @var LeadTrackerService
     */
    private $leadTrackerService;

    /**
     * LeadTrackerMiddleware constructor.
     * @param LeadTrackerService $leadTrackerService
     */
    public function __construct(LeadTrackerService $leadTrackerService)
    {
        $this->leadTrackerService = $leadTrackerService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response = null)
    {
        // we'll wrap the entire thing in a try catch since we do not want this system to ever stop execution
        try {

            foreach (config('lead-tracker.requests_to_capture') as $requestToCaptureData) {

                // load data map
                $inputDataMap = $requestToCaptureData['input_data_map'];

                if (strtolower($request->getMethod()) == strtolower($requestToCaptureData['method']) &&
                    strtolower($request->path()) == strtolower(trim($requestToCaptureData['path'], '/')) &&
                    strtolower($request->get($inputDataMap['form_name'])) ==
                    strtolower($requestToCaptureData['form_name']) &&
                    config('lead-tracker.brand') == $requestToCaptureData['brand']) {

                    // fail if there is no input map
                    if (empty($inputDataMap)) {
                        error_log('Failed to track lead (LeadTracker) input data map is missing for request.');
                        error_log(
                            'Path: ' .
                            $requestToCaptureData['path'] .
                            ' - Request data: ' .
                            var_export($request->all(), true)
                        );

                        return;
                    }

                    // check all the data we need exists
                    if (empty($request->get($inputDataMap['email'])) ||
                        empty($request->get($inputDataMap['form_name']))) {

                        // we cannot track this request due to missing information
                        error_log('Failed to track lead (LeadTracker) some required data is missing from the request.');
                        error_log('Request data: ' . var_export($request->all(), true));

                        return;
                    }

                    $this->leadTrackerService->trackLead(
                        $request->get($inputDataMap['email']),
                        $request->get($inputDataMap['form_name']),
                        $request->header('referer', ''),
                        $request->get($inputDataMap['utm_source']),
                        $request->get($inputDataMap['utm_medium']),
                        $request->get($inputDataMap['utm_campaign']),
                        $request->get($inputDataMap['utm_term'])
                    );

                    return;
                }
            }

        } catch (Throwable $throwable) {
            error_log('Failed to track lead (LeadTracker) due to exception.');
            error_log($throwable);
        }
    }
}