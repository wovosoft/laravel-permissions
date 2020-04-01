<?php

namespace Wovosoft\LaravelPermissions\Traits;

trait ExceptionHandlerTrait
{
    public function initializeExceptionHandlerTrait()
    {

    }

    private function handleExceptions(\Exception $exception)
    {
        if (env('APP_DEBUG')) {
            return response()->json([
                "code" => $exception->getCode(),
                "status" => false,
                "title" => 'Failed!',
                "type" => "warning",
                "msg" => $exception->getMessage(),
                "line" => $exception->getLine(),
                "file" => $exception->getFile(),
                "trace" => $exception->getTrace(),
            ], 404, [], JSON_PRETTY_PRINT);
        }
        return response()->json([
            "status" => false,
            "title" => 'Failed!',
            "type" => "warning",
            "msg" => "Operation Failed"
        ], 404);
    }
}
