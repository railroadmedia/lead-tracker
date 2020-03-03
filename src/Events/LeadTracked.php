<?php


namespace Railroad\LeadTracker\Events;


class LeadTracked
{
    /**
     * Return array attributes:
     * [
     *    'email' => string,
     *    'maropost_tag_name' => string,
     *    'form_name' => string,
     *    'form_page_url' => string,
     *    'utm_source' => string, // nullable
     *    'utm_medium' => string, // nullable
     *    'utm_campaign' => string, // nullable
     *    'utm_term' => string, // nullable
     * ];
     *
     * @var array
     */
    public $leadData;

    /**
     * LeadTracked constructor.
     *
     * @param array $leadData
     */
    public function __construct(array $leadData)
    {
        $this->leadData = $leadData;
    }
}