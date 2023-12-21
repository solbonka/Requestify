<?php

namespace Tests\Unit;

use App\Models\Request;
use Tests\TestCase;
use App\Http\Resources\RequestResource;
use Illuminate\Http\Request as HttpRequest;

class RequestResourceTest extends TestCase
{
    public function testToArrayMethodReturnsCorrectArray()
    {
        $request = new Request();
        $request->id = 1;
        $request->name = 'Alex';
        $request->email = 'alex@mail.ru';
        $request->user_id = 1;
        $request->status = 'Active';
        $request->message = 'Test Text';
        $request->comment = '';
        $request->created_at = '2021-01-01 00:00:00';
        $request->updated_at = '2021-02-01 00:00:00';

        $httpRequest = new HttpRequest();

        $resource = new RequestResource($request);

        $expectedArray = [
            'id' => 1,
            'name' => 'Alex',
            'email' => 'alex@mail.ru',
            'status' => 'Active',
            'message' => 'Test Text',
            'comment' => '',
            'created_at' => '01/01/2021',
            'updated_at' => '01/02/2021',
        ];

        $this->assertEquals($expectedArray, $resource->toArray($httpRequest));
    }
}
