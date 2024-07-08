<?php

namespace App\Services;

class BaseService
{
    protected function response(bool $success,string $message=null,array $result): array{
        return [
            "success"=>$success,
            "message"=>$message,
            "result"=>$result
        ];
    }
}