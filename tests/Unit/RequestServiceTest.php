<?php

namespace Tests\Unit;

use App\Models\Request;
use App\Services\RequestService;
use Tests\TestCase;

class RequestServiceTest extends TestCase
{
    public function testResolveActiveRequest()
    {
        $request = new Request();
        $request->status = 'Active';
        $adminId = 1;
        $comment = 'Resolved';

        $expectedMessage = 'Request resolved successfully';

        $requestService = new RequestService();

        $result = $requestService->resolve($request, $adminId, $comment);

        $this->assertIsArray($result);
        $this->assertEquals($expectedMessage, $result['message']);
    }

    public function testResolveResolvedRequest()
    {
        $request = new Request();
        $request->status = 'Resolved';
        $adminId = 1;
        $comment = 'Resolved';

        $expectedMessage = 'The request has already been resolved';

        $requestService = new RequestService();

        $result = $requestService->resolve($request, $adminId, $comment);

        $this->assertIsArray($result);
        $this->assertEquals($expectedMessage, $result['message']);
    }
}
