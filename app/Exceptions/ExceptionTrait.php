<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionTrait
{
    public function apiException($request, $exception)
    {

        if ($this->isModel($exception)) {
            return $this->modelResponse($exception);
        }

        if ($this->isHttp($exception)) {
            return $this->httpResponse($exception);
        }

        return parent::render($request, $exception);
    }

    protected function isModel($exception)
    {
        return $exception instanceof ModelNotFoundException;
    }

    protected function isHttp($exception)
    {
        $exception instanceof NotFoundHttpException;
    }

    protected function modelResponse($exception)
    {
        return response()->json([
            'error' => 'Product Model found.'
        ], Response::HTTP_NOT_FOUND);
    }

    protected function httpResponse($exception)
    {
        return response()->json([
            'error' => 'Incorrect route'
        ], Response::HTTP_NOT_FOUND);
    }
}
