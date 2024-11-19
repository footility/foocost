<?php
namespace Footility\Foocost\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\ParserFactory;
use Symfony\Component\DomCrawler\Crawler;

class GenerateAppStructure extends Command
{
    protected $signature = 'foo:generate-structure';
    protected $description = 'Genera una struttura JSON dell\'applicazione basata sui modelli.';

    public function handle()
    {
        $appStructure = [];
        $entities = $this->getEntityDescription('models', app_path('Models'));
        $viewMappings = config('foocost.view_mappings');

        foreach ($entities as $entity => $path) {
            $modelClassName = "App\\Models\\$entity";
            $modelInstance = app($modelClassName);

            $columnsCount = count($modelInstance->getFillable());
            $appStructure[$entity]['fields'] = $columnsCount;

            $entityControllerPath = app_path("Http/Controllers/{$entity}Controller.php");
            $appStructure[$entity]['actions'] = $this->countMethods($entityControllerPath);
            $appStructure[$entity]['BE Logics'] = $this->countDevUnit($entityControllerPath, [
                'if' => '/if\s*\(/',
                'foreach' => '/foreach\s*\(/',
                'switch' => '/switch\s*\(/',
            ]);

            $appStructure[$entity]['relations'] = $this->countDevUnit($path, [
                'hasOne', 'hasMany', 'belongsTo', 'belongsToMany',
                'morphOne', 'morphTo', 'morphMany',
            ]);

            $viewsPath = base_path("resources/views/" . Str::plural(strtolower($entity)));
            $views = $this->getEntityDescription('views', $viewsPath);
            $appStructure[$entity]['views'] = count($views);

            $feLogicsCount = 0;
            $formFields = 0;
            foreach ($views as $viewPath) {
                $feLogicsCount += $this->countDevUnit($viewPath, [
                    '/@if\s*\(/', '/@foreach\s*\(/', '/@switch\s*\(/'
                ]);

                $crawler = new Crawler(file_get_contents($viewPath));
                $formFields += $crawler->filter('input, select, textarea, button')->count();
            }
            $appStructure[$entity]['FE Logics'] = $feLogicsCount;
            $appStructure[$entity]['Forms'] = $formFields;
        }

        file_put_contents('app_structure.json', json_encode($appStructure, JSON_PRETTY_PRINT));
        $this->info('Analisi completata: app_structure.json generato.');
    }

    protected function getEntityDescription($typeKey, $path)
    {
        if (!file_exists($path)) {
            $this->warn("Il file $path non esiste.");
            return [];
        }

        $files = File::allFiles($path);
        $descriptions = [];
        foreach ($files as $file) {
            $entityName = $file->getFilenameWithoutExtension();
            $descriptions[$entityName] = $file->getPathname();
        }
        return $descriptions;
    }

    protected function countMethods($filePath)
    {
        if (!file_exists($filePath)) {
            $this->warn("File $filePath non trovato.");
            return 0;
        }

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse(file_get_contents($filePath));

        $actions = 0;
        foreach ($stmts as $stmt) {
            if ($stmt instanceof \PhpParser\Node\Stmt\ClassMethod && $stmt->isPublic()) {
                $actions++;
            }
        }
        return $actions;
    }

    protected function countDevUnit($filePath, $patterns)
    {
        if (!file_exists($filePath)) {
            $this->warn("File $filePath non trovato.");
            return 0;
        }

        $content = file_get_contents($filePath);
        $totalOccurrences = 0;

        foreach ($patterns as $pattern) {
            if (@preg_match($pattern, '') !== false) {
                preg_match_all($pattern, $content, $matches);
                $totalOccurrences += count($matches[0]);
            } else {
                $totalOccurrences += substr_count($content, $pattern);
            }
        }
        return $totalOccurrences;
    }
}
