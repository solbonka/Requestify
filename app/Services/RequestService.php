<?php

namespace App\Services;

use App\Models\Request;

class RequestService
{
    public function resolve(Request $request, string $comment): string
    {
        if ($request->status === 'Active') {
            $request->update([
                'status' => 'Resolved',
                'comment' => $comment,
            ]);
            return 'Request resolved successfully';
        }
        return 'The request has already been resolved';
    }
}
