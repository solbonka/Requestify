<?php

namespace App\Services;

use App\Models\Request;

class RequestService
{
    public function resolve(Request $request, int $adminId, string $comment): array
    {
        if ($request->status === 'Active') {
            $request->update([
                'status' => 'Resolved',
                'comment' => $comment,
                'admin_id' => $adminId,
            ]);

            return [
                'data' => $request->toArray(),
                'message' => 'Request resolved successfully',
            ];
        }

        return [
            'data' => $request->toArray(),
            'message' => 'The request has already been resolved',
        ];
    }
}
