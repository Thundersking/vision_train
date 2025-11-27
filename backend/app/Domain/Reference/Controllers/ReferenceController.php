<?php

declare(strict_types=1);

namespace App\Domain\Reference\Controllers;

use App\Domain\ExerciseTemplate\Enums\ExerciseParameterUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class ReferenceController extends Controller
{
    public function units(): JsonResponse
    {
        $data = array_map(static function (ExerciseParameterUnit $unit) {
            return [
                'id' => $unit->value,
                'name' => $unit->label(),
            ];
        }, ExerciseParameterUnit::cases());

        return response()->json([
            'data' => $data,
        ]);
    }
}
