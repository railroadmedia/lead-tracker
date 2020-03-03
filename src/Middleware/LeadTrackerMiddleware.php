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
        try {

            foreach (config('lead-tracker.requests_to_capture') as $requestToCaptureData) {

                if (strtolower($request->getMethod()) == strtolower($requestToCaptureData['method']) &&
                    strtolower($request->path()) == strtolower(trim($requestToCaptureData['path'], '/'))) {

                    if (empty($request->get('leadtracker_email')) ||
                        empty($request->get('leadtracker_maropost_tag_name')) ||
                        empty($request->get('leadtracker_form_name'))) {

                        // we cannot track this request due to missing information
                        error_log('Failed to track lead (LeadTracker) some required data is missing from the request.');
                        error_log('Request data: ' . var_export($request->all(), true));

                        return $next($request);
                    }

                    // todo: add prefix so we dont steal other inputs when making them on the FE
                    $this->leadTrackerService->trackLead(
                        $request->get('leadtracker_email'),
                        $request->get('leadtracker_maropost_tag_name'),
                        $request->get('leadtracker_form_name'),
                        $request->fullUrl(),
                        $request->get('leadtracker_utm_source'),
                        $request->get('leadtracker_utm_medium'),
                        $request->get('leadtracker_utm_campaign'),
                        $request->get('leadtracker_utm_term')
                    );

                    return $next($request);
                }
            }

        } catch (Throwable $throwable) {
            error_log('Failed to track lead (LeadTracker) due to exception.');
            error_log($throwable);
        }

        return $next($request);
    }
}