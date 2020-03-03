<?php

namespace Railroad\LeadTracker\Tests\Functional;

use Illuminate\Http\Request;
use Railroad\LeadTracker\Middleware\LeadTrackerMiddleware;
use Railroad\LeadTracker\Tests\LeadTrackerTestCase;

class AccessCodeJsonControllerTest extends LeadTrackerTestCase
{
    public function test_test()
    {
        $request = new Request;

        $request->merge([
            'title' => 'Title is in mixed CASE'
        ]);

        $middleware = new LeadTrackerMiddleware();

        $middleware->handle($request, function ($req) {
            $this->assertEquals(true, true);
        });
    }
}
