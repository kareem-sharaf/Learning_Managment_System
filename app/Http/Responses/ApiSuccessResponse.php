<?php
namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class ApiSuccessResponse implements Responsable
{

    public function __construct(
        protected string $message,
        protected mixed $data,
        protected int $code=Response::HTTP_OK,
        protected array $headers =[],
    ){}


    public function toResponse($request){

        return response()->json([
             'message' => $this->message,
            'data' => $this->data
        ], $this->code , $this->headers);
    }
}



