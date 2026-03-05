<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Inputs que nunca serão exibidos em erros de validação
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registro de handlers de exceção
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessage(),
                    'file'    => config('app.debug') ? $e->getFile() : null,
                    'line'    => config('app.debug') ? $e->getLine() : null,
                ], 500);
            }
        });
    }

    /**
     * Retorno padrão para requisições não autenticadas (API)
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => 'Unauthenticated.'
        ], 401);
    }
}