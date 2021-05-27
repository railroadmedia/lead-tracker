<?php


namespace Railroad\LeadTracker\Events;


class LeadTracked
{
    /**
     * Return array attributes:
     * [
     *    'id' => integer
     *    'brand' => string
     *    'email' => string
     *    'form_name' => string
     *    'form_page_url' => string
     *    'utm_source' => string // nullable
     *    'utm_medium' => string // nullable
     *    'utm_campaign' => string // nullable
     *    'utm_term' => string // nullable
     *    'maropost_tag_name' => string // nullable
     *    'customer_io_customer_id' => string // nullable
     *    'customer_io_event_name' => string // nullable
     * ];
     *
     * @var null|array
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