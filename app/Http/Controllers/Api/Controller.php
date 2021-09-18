<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function responseJSON($data = null, $mess = '', $status = true, $code = 200)
    {
        return response()->json([
            'data' => $data,
            'mess' => $mess,
            'status' => $status,
        ], $code);
    }
}
