<?php

namespace Footility\Foocost\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class CostController extends Controller
{
    public function calculateCosts()
    {
        $hourlyRate = config('foocost.hourly_rate');
        $minutesPerUnit = config('foocost.minutes_per_field');
        $costPerMinute = $hourlyRate / 60;

        $structurePath = base_path('app_structure.json');
        if (!File::exists($structurePath)) {
            return response()->json(['error' => 'File app_structure.json non trovato.'], 404);
        }

        $structure = json_decode(File::get($structurePath), true);
        $costDetails = [];
        $totalCost = 0;
        $totalTime = 0;

        foreach ($structure as $entity => $details) {
            $entityCost = 0;
            $entityTime = 0;

            foreach ($details as $unitCount) {
                $unitTime = $unitCount * $minutesPerUnit;
                $unitCost = $unitTime * $costPerMinute;

                $entityTime += $unitTime;
                $entityCost += $unitCost;
            }

            $costDetails[] = [
                'entity' => $entity,
                'time' => $entityTime,
                'cost' => $entityCost,
            ];

            $totalTime += $entityTime;
            $totalCost += $entityCost;
        }

        return view('foocost::costs', compact('costDetails', 'totalCost', 'totalTime'));
    }
}
