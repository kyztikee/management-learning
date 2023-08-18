<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            $position = strrpos($exception->getModel(), "\\");
            $modelName = substr($exception->getModel(), $position+1);
            return response()->api([], 404, 'error', 'Sorry! '.$modelName.' doesn\'t exist!');
        } else if($exception instanceof ValidationException) {
            return response()->api($exception->errors(), 422, 'error');
        }

        return parent::render($request, $exception);
    }
}
