<?php

namespace App\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use JsonSerializable;

trait ResponderTrait
{

    /**
     * @param  array|Arrayable|JsonSerializable|null  $data
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function response(array|Arrayable|JsonSerializable|null $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => $this->isStatusTrue($status),
            'data'    => $this->morphToArray($data) ?? [],
            'message' => $message,
        ], $status);
    }

    public function paginateResponse(
        LengthAwarePaginator $paginator,
        array|Arrayable|JsonSerializable|null $data = null,
        string|null $message = null,
        int $status = 200
    ) {
        return response()->json([
            'message' => $message,
            'data'    => $this->morphToArray($data) ?? [],
            'meta'    => [
                'current_page'  => $paginator->currentPage(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'next_page_url' => $paginator->nextPageUrl(),
                'last_page'     => $paginator->lastPage(),
                'per_page'      => $paginator->perPage(),
                'total'         => $paginator->total(),
            ],
        ], $status);
    }

    /**
     * @param  array|Arrayable|JsonSerializable|null  $data
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseCreated(array|Arrayable|JsonSerializable|null $data = null, ?string $message = null, int $status = 201): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.created');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'data'    => $this->morphToArray($data) ?? [],
            'message' => $message,
        ], $status);
    }

    /**
     * @param  array|Arrayable|JsonSerializable|null  $data
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseIndex(array|Arrayable|JsonSerializable|null $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.index');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'data'    => $this->morphToArray($data) ?? [],
            'message' => $message,
        ], $status);
    }

    public function responseShow(array|Arrayable|JsonSerializable|null $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.show');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'data'    => $this->morphToArray($data) ?? [],
            'message' => $message,
        ], $status);
    }

    /**
     * @param  array|Arrayable|JsonSerializable|null  $data
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseUpdated(array|Arrayable|JsonSerializable|null $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.updated');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'data'    => $this->morphToArray($data) ?? [],
            'message' => $message,
        ], $status);
    }

    /**
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseDestroyed(?string $message = null, int $status = 200): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.deleted');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'data'    => [],
            'message' => $message,
        ], $status);
    }

    /**
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseForbidden(?string $message = null, int $status = 403): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.forbidden');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'message' => $message,
        ], $status);
    }

    /**
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseUnauthorized(?string $message = null, int $status = 401): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.unauthorized');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'message' => $message,
        ], $status);
    }

    /**
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseSuccessful(?string $message = null, int $status = 200): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.successful');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'message' => $message,
        ], $status);
    }

    /**
     * @param  string|null  $message
     * @param  int  $status
     * @return JsonResponse
     */
    public function responseFailure(?string $message = null, int $status = 400): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.failure');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'message' => $message,
        ], $status);
    }

    public function responseNotFound(?string $message = null, int $status = 404): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.not_found');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'message' => $message,
        ], $status);
    }

    public function responseValidationError(string|null $message = null, int $status = 422): JsonResponse
    {
        if ($message === null) {
            $message = trans('response.validation_error');
        }

        return response()->json([
            'success' => $this->isStatusTrue($status),
            'message' => $message,
        ], $status);
    }

    /**
     * @param  array|Arrayable|JsonSerializable|null  $data
     * @return array|JsonSerializable|Arrayable|null
     */
    private function morphToArray(array|Arrayable|JsonSerializable|null $data): array|JsonSerializable|Arrayable|null
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param  int  $status
     * @return bool
     */
    public function isStatusTrue(int $status): bool
    {
        return in_array($status, range(200, 206), true);
    }

}
