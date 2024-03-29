<?php

namespace Footility\FooCost\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CostController extends Controller
{
  public function calculateCosts()
  {
    $costsDetails = [];
    $totalCost = 0;
    $hourlyRate = config('foocost.hourly_rate');
    $minutesPerField = config('foocost.minutes_per_field');
    $excludeTables = config('foocost.exclude_tables', []);
    $costPerField = ($hourlyRate / 60) * $minutesPerField;

    $tables = DB::select('SHOW TABLES');
    $dbName = env('DB_DATABASE');
    foreach ($tables as $table) {
      $tableName = $table->{'Tables_in_' . $dbName};

      if (in_array($tableName, $excludeTables)) {
        continue; // Salta le tabelle escluse dalla configurazione
      }

      $fields = DB::select("DESCRIBE $tableName");
      $numFields = count($fields);

      $tableCost = $numFields * $costPerField;
      $totalCost += $tableCost;

      $costsDetails[] = [
        'table' => $tableName,
        'fields' => $numFields,
        'cost' => $tableCost,
      ];
    }

    return view('foocost::costs', compact('costsDetails', 'totalCost'));
  }

}
