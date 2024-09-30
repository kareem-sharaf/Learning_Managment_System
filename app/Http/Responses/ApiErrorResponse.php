<?php
namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class ApiErrorResponse implements Responsable
{
    public function __construct(
        protected string $message,
        protected int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        protected array $headers = [],
    ) {}

    public function toResponse($request)
    {
        return response()->json([
            'message' => $this->message,
        ], $this->code, $this->headers);
    }
}



