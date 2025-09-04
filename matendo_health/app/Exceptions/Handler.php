<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            $this->logSecurityEvent($e);
        });
    }

    /**
     * Report or log an exception.
     */
    public function report(Throwable $e): void
    {
        // Log security-related exceptions with additional context
        if ($this->isSecurityException($e)) {
            $this->logSecurityException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle AJAX/API requests
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        // Handle specific exceptions
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($request, $e);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($request, $e);
        }

        if ($e instanceof AuthorizationException) {
            return $this->handleAuthorizationException($request, $e);
        }

        if ($e instanceof TokenMismatchException) {
            return $this->handleCsrfException($request, $e);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($request, $e);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException($request, $e);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($request, $e);
        }

        if ($e instanceof TooManyRequestsHttpException) {
            return $this->handleRateLimitException($request, $e);
        }

        if ($e instanceof HttpException) {
            return $this->handleHttpException($request, $e);
        }

        // Handle general exceptions
        return $this->handleGeneralException($request, $e);
    }

    /**
     * Handle API exceptions with JSON responses
     */
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        $status = 500;
        $message = 'Internal Server Error';
        $errors = [];

        if ($e instanceof ValidationException) {
            $status = 422;
            $message = 'Validation failed';
            $errors = $e->errors();
        } elseif ($e instanceof AuthenticationException) {
            $status = 401;
            $message = 'Unauthenticated';
        } elseif ($e instanceof AuthorizationException) {
            $status = 403;
            $message = 'Unauthorized';
        } elseif ($e instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'Resource not found';
        } elseif ($e instanceof NotFoundHttpException) {
            $status = 404;
            $message = 'Endpoint not found';
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = 405;
            $message = 'Method not allowed';
        } elseif ($e instanceof TooManyRequestsHttpException) {
            $status = 429;
            $message = 'Too many requests';
        } elseif ($e instanceof TokenMismatchException) {
            $status = 419;
            $message = 'CSRF token mismatch';
        } elseif ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            $message = $e->getMessage() ?: 'HTTP Exception';
        }

        $response = [
            'success' => false,
            'message' => $message,
            'status' => $status,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (config('app.debug') && !$this->isSecurityException($e)) {
            $response['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }

        return response()->json($response, $status);
    }

    /**
     * Handle validation exceptions
     */
    protected function handleValidationException(Request $request, ValidationException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput($request->except($this->dontFlash));
    }

    /**
     * Handle authentication exceptions
     */
    protected function handleAuthenticationException(Request $request, AuthenticationException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        $guards = $e->guards();
        
        if (in_array('admin', $guards)) {
            return redirect()->route('admin.login');
        }
        
        if (in_array('doctor', $guards)) {
            return redirect()->route('doctor.login');
        }

        return redirect()->route('login');
    }

    /**
     * Handle authorization exceptions
     */
    protected function handleAuthorizationException(Request $request, AuthorizationException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return response()->view('errors.403', [
            'message' => $e->getMessage() ?: 'You are not authorized to access this resource.'
        ], 403);
    }

    /**
     * Handle CSRF token exceptions
     */
    protected function handleCsrfException(Request $request, TokenMismatchException $e)
    {
        Log::warning('CSRF token mismatch', [
            'url' => $request->url(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return redirect()->back()
            ->withInput($request->except($this->dontFlash))
            ->withErrors(['csrf' => 'Your session has expired. Please try again.']);
    }

    /**
     * Handle model not found exceptions
     */
    protected function handleModelNotFoundException(Request $request, ModelNotFoundException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return response()->view('errors.404', [
            'message' => 'The requested resource could not be found.'
        ], 404);
    }

    /**
     * Handle not found HTTP exceptions
     */
    protected function handleNotFoundHttpException(Request $request, NotFoundHttpException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return response()->view('errors.404', [
            'message' => 'The page you are looking for could not be found.'
        ], 404);
    }

    /**
     * Handle method not allowed exceptions
     */
    protected function handleMethodNotAllowedException(Request $request, MethodNotAllowedHttpException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return response()->view('errors.405', [
            'message' => 'The request method is not allowed for this endpoint.'
        ], 405);
    }

    /**
     * Handle rate limit exceptions
     */
    protected function handleRateLimitException(Request $request, TooManyRequestsHttpException $e)
    {
        Log::warning('Rate limit exceeded', [
            'ip' => $request->ip(),
            'url' => $request->url(),
            'user_id' => auth()->id(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return response()->view('errors.429', [
            'message' => 'Too many requests. Please slow down.'
        ], 429);
    }

    /**
     * Handle HTTP exceptions
     */
    protected function handleHttpException(Request $request, HttpException $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        $statusCode = $e->getStatusCode();
        $view = "errors.{$statusCode}";

        if (view()->exists($view)) {
            return response()->view($view, [
                'message' => $e->getMessage() ?: 'An error occurred.'
            ], $statusCode);
        }

        return response()->view('errors.generic', [
            'status' => $statusCode,
            'message' => $e->getMessage() ?: 'An error occurred.'
        ], $statusCode);
    }

    /**
     * Handle general exceptions
     */
    protected function handleGeneralException(Request $request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return response()->view('errors.500', [
            'message' => 'Something went wrong. Please try again later.'
        ], 500);
    }

    /**
     * Check if exception is security-related
     */
    protected function isSecurityException(Throwable $e): bool
    {
        $securityExceptions = [
            AuthenticationException::class,
            AuthorizationException::class,
            TokenMismatchException::class,
            TooManyRequestsHttpException::class,
        ];

        return in_array(get_class($e), $securityExceptions);
    }

    /**
     * Log security-related exceptions with additional context
     */
    protected function logSecurityException(Throwable $e): void
    {
        $context = [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ];

        Log::channel('security')->warning('Security exception occurred', $context);
    }

    /**
     * Log general security events
     */
    protected function logSecurityEvent(Throwable $e): void
    {
        // Log suspicious patterns
        $suspiciousPatterns = [
            'sql injection', 'union select', 'drop table', 'delete from',
            'script', 'javascript:', 'vbscript:', 'onload=', 'onerror=',
            '../', '..\\', '/etc/passwd', 'cmd.exe', 'powershell'
        ];

        $exceptionMessage = strtolower($e->getMessage());
        $requestContent = strtolower(request()->getContent());

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($exceptionMessage, $pattern) !== false || 
                strpos($requestContent, $pattern) !== false) {
                
                Log::channel('security')->alert('Suspicious activity detected', [
                    'pattern' => $pattern,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'user_id' => auth()->id(),
                ]);
                break;
            }
        }
    }
}
