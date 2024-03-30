<?php

namespace Footility\Foocost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


class GenerateAppStructure extends Command
{
  protected $signature = 'foo:generate-structure';
  protected $description = 'Genera una struttura JSON dell\'applicazione basata sui modelli.';

  public function handle()
  {

    $appStructure = array();

    $entities = $this->getEntityDescription('models', app_path('Models'));
    $viewMappings = config('foocost.view_mappings');


    foreach ($entities as $entity => $path) {

      //conteggio dei campi
      $modelClassName = "App\\Models\\$entity"; // Nome completo della classe del modello come stringa
      $modelInstance = app($modelClassName);
      $columnsCount = count($modelInstance->getFillable());
      $appStructure[$entity]['fields'] = $columnsCount;

      //but entity are no controller, need to convert
      $entityControllerPath = app_path("Http/Controllers") . "/" . $entity . "Controller.php";
      $appStructure[$entity]['actions'] = $this->countDevUnit($entityControllerPath, ['public function']);

      //be logics
      $appStructure[$entity]['BE Logics'] = $this->countDevUnit($entityControllerPath, [
        'if' => '/if\s*\((.*?)\)/', // Trova istruzioni @if
        'foreach' => '/foreach\s*\((.*?)\)/', // Trova istruzioni @foreach
        'switch' => '/switch\s*\((.*?)\)/', // Trova istruzioni @switch
      ]);

      //the entityes are models, so i can extract relations
      $appStructure[$entity]['relations'] = $this->countDevUnit($path, [
        'hasOne',
        'hasMany',
        'belongsTo',
        'belongsToMany',
        'morphOne',
        'morphTo',
        'morphMany',
        'morphToMany',
        'morphedByMany',
      ]);

      //conteggio delle viste
      $mEntity = in_array($entity, array_keys($viewMappings)) ? $viewMappings[$entity] : $entity;
      $views = $this->getEntityDescription('views', base_path("resources/views/" . Str::plural(strtolower($mEntity))));
      $appStructure[$entity]['views'] = count($views);

      //controllo dei campi nelle viste
      $feLogicsCount = 0;
      $formFields = 0;

      foreach ($views as $eView => $viewPath) {
        $feLogicsCount += $this->countDevUnit($viewPath, [
          'if' => '/@if\s*\((.*?)\)/', // Trova istruzioni @if
          'foreach' => '/@foreach\s*\((.*?)\)/', // Trova istruzioni @foreach
          'switch' => '/@switch\s*\((.*?)\)/', // Trova istruzioni @switch
        ]);

        $formFields += $this->countDevUnit($viewPath, [
          '<input',
          '<select',
          '<form',
          '<button'
        ]);
      }

      $appStructure[$entity]['FE Logics'] = $feLogicsCount;
      $appStructure[$entity]['Forms'] = $formFields;
    }


    file_put_contents('app_structure.json', json_encode($appStructure, JSON_PRETTY_PRINT));

  }

  protected function getEntityDescription($typeKey, $path)
  {
    if (!file_exists($path)) {
      $this->warn("il file $path non esiste");
      return array($typeKey => []);
    }

    $files = File::allFiles($path);
    $descriptions = [];

    foreach ($files as $file) {
      // Estrapola il nome del modello dal nome del file, escludendo l'estensione ".php"
      $entityName = $file->getFilenameWithoutExtension();

      // Usa il nome dell'entità come chiave e il percorso completo del file come valore
      $descriptions[$entityName] = $file->getPathname();
    }

    return $descriptions;
  }


  /**
   * Conta le occorrenze totali di una lista di stringhe o pattern regex in un file.
   *
   * @param string $filePath Il percorso completo al file da analizzare.
   * @param array $patterns Un array di stringhe o pattern regex da cercare.
   * @return int La somma totale delle occorrenze di tutti i pattern.
   */
  function countDevUnit($filePath, $patterns)
  {
    // Assicurati che il file esista prima di procedere.
    if (!file_exists($filePath)) {
      $this->error("Il file {$filePath} non esiste.");
      return 0;
    }

    // Leggi il contenuto del file.
    $content = file_get_contents($filePath);
    $totalOccurrences = 0;

    foreach ($patterns as $pattern) {
      if (@preg_match($pattern, '') !== false) {
        // Il pattern è una regex valida, usa preg_match_all
        preg_match_all($pattern, $content, $matches);
        $totalOccurrences += count($matches[0]);
      } else {
        // Pattern considerato come stringa semplice
        $totalOccurrences += substr_count($content, $pattern);
      }
    }

    return $totalOccurrences;
  }


}
