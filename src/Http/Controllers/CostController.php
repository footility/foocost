<?php


namespace Footility\FooCost\Http\Controllers;

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
      return "File app_structure.json non trovato.";
    }

    $structure = json_decode(File::get($structurePath), true);
    $costsDetails = [];
    $totalCost = 0;
    $totalTime = 0;

    foreach ($structure as $entity => $details) {
      $entityCost = 0;
      $entityTime = 0;
      $entityDetails = [
        'entity' => $entity,
        // Aggiungi qui altri dettagli iniziali se necessario
      ];

      // Calcola i costi per ogni tipo di unità (relations, actions, views, ...)
      foreach ($details as $unitType => $unitCount) {
        $entityTime = $unitCount * $minutesPerUnit;
        $unitCost = $entityTime * $costPerMinute;
        $entityCost += $unitCost;
        // Salva il conteggio per ogni unità e il costo calcolato
        $entityDetails[$unitType] = $unitCount;
        $entityDetails[$unitType . '_cost'] = $unitCost;
      }

      $entityDetails['time'] = $entityTime;
      $entityDetails['cost'] = $entityCost;
      $costsDetails[] = $entityDetails;
      $totalTime += $entityTime;
      $totalCost += $entityCost;
    }

    $uniqueKeys = [];
    foreach ($structure as $entityDetails) {
      $uniqueKeys = array_unique(array_merge($uniqueKeys, array_keys($entityDetails)));
    }

    // Rimuovi 'cost' dall'elenco delle chiavi, se presente, poiché sarà gestito separatamente
    $uniqueKeys = array_filter($uniqueKeys, function ($key) {
      return $key !== 'cost' || $key !== 'time';
    });

    $totalTime = $totalTime / (2 * 60);

    return view('foocost::costs', compact('costsDetails', 'totalCost', 'totalTime', 'uniqueKeys'));
  }

}
