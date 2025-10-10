<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Exception;

trait ApiResponseTrait
{
    /**
     * Başarılı response döndür
     */
    protected function successResponse($data = null, string $message = 'İşlem başarılı', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Hata response döndür
     */
    protected function errorResponse(string $message = 'Bir hata oluştu', int $statusCode = Response::HTTP_BAD_REQUEST, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Validation hatası response döndür
     */
    protected function validationErrorResponse(ValidationException $exception): JsonResponse
    {
        return $this->errorResponse(
            'Doğrulama hatası',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $exception->errors()
        );
    }

    /**
     * Yetki hatası response döndür
     */
    protected function unauthorizedResponse(string $message = 'Bu işlem için yetkiniz yok'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Bulunamadı hatası response döndür
     */
    protected function notFoundResponse(string $message = 'Kayıt bulunamadı'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Server hatası response döndür
     */
    protected function serverErrorResponse(string $message = 'Sunucu hatası'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Paginated response döndür
     */
    protected function paginatedResponse($data, string $message = 'Veriler listelendi'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more_pages' => $data->hasMorePages(),
            ]
        ]);
    }

    /**
     * Exception handling
     */
    protected function handleException(Exception $exception): JsonResponse
    {
        // Log exception
        \Log::error('API Exception: ' . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Return appropriate response based on exception type
        switch (true) {
            case $exception instanceof ValidationException:
                return $this->validationErrorResponse($exception);

            case $exception instanceof NotFoundHttpException:
                return $this->notFoundResponse();

            case $exception instanceof AccessDeniedHttpException:
                return $this->unauthorizedResponse();

            case $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException:
                return $this->notFoundResponse('Kayıt bulunamadı');

            case $exception instanceof \Illuminate\Auth\AuthenticationException:
                return $this->errorResponse('Kimlik doğrulama gerekli', Response::HTTP_UNAUTHORIZED);

            default:
                // Don't expose internal errors in production
                $message = app()->environment('production') 
                    ? 'Bir hata oluştu' 
                    : $exception->getMessage();
                
                return $this->serverErrorResponse($message);
        }
    }

    /**
     * Resource created response
     */
    protected function createdResponse($data, string $message = 'Kayıt oluşturuldu'): JsonResponse
    {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Resource updated response
     */
    protected function updatedResponse($data, string $message = 'Kayıt güncellendi'): JsonResponse
    {
        return $this->successResponse($data, $message);
    }

    /**
     * Resource deleted response
     */
    protected function deletedResponse(string $message = 'Kayıt silindi'): JsonResponse
    {
        return $this->successResponse(null, $message);
    }

    /**
     * No content response
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}