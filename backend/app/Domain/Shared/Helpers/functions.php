<?php

use Illuminate\Http\JsonResponse;

/**
 * @return JsonResponse
 */
function responseOk(): JsonResponse
{
    return response()->json([
        'data' => [
            'status' => 'success'
        ]
    ]);
}

/**
 * @param string|null $message
 * @param int $code
 * @return JsonResponse
 */
function responseFailed(?string $message = null, int $code = 400): JsonResponse
{
    return response()->json([
        'data' => [
            'message' => $message
        ]
    ], $code);
}

/**
 * @param string|null $code
 * @return string|null
 */
function getMessage(string $code = null): ?string
{
    return __("messages.$code");
}

/**
 * Проверяет, одинаковы ли элементы массивов.
 *
 * @param array $array1 Первый массив.
 * @param array $array2 Второй массив.
 * @return bool Результат сравнения.
 */
function areArraysEqual(array $array1, array $array2): bool
{
    return count(array_diff($array1, $array2)) === 0;
}
