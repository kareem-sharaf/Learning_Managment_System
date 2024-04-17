<?php
namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class ApiErrorResponse implements Responsable
{

    public function __construct(
        protected string $message,
        // protected Throwable $e ,
        protected int $code=Response::HTTP_INTERNAL_SERVER_ERROR,
        protected array $headers =[],
    ){}


    public function toResponse($request){

        return response()->json([
             'message' => $this->message,
            // 'data' => $this->e
        ], $this->code , $this->headers);
    }
}



