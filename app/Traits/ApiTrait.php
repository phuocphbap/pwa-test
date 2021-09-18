<?php

namespace App\Traits;

trait ApiTrait
{
    protected function respondSuccess($data)
    {
        return response()->json([
            'sucesss' => true,
            'data' => $data,
        ], 200);
    }

    protected function respondError($message, $errors = true)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], 400);
    }


    protected function snakeToCamel($data)
    {
        if (is_array($data)) {
            $respond = [];
            foreach ($data as $key => $value) {
                if(is_array($value)) {
                    foreach ($value as $index => $inner) {
                        $respond[$key][lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $index))))] = $inner;
                    }
                } else {
                    $respond[lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))))] = $value;
                }
            }
            return $respond;
        }
        return $data;
    }
}
