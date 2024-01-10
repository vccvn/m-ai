<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IconGetClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icon:get-class {prefix=fa} {file=0}';

    /**
     * The console get icon class.
     *
     * @var string
     */
    protected $description = 'get icon class';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(($f = $this->argument('file') ) && file_exists($path = base_path($f))){
            $content = file_get_contents($path);
            $expression = '/\.'. $this->argument('prefix') .'\-([^\:]+)\:before/i';
            echo "$expression\n";
            preg_match_all($expression, $content, $matches);
            print_r($matches);
            // return 0;
            if($matches[2]){
                $data = [
                    'flaticon' => [],
                    'bxs' => [],
                    'bxl' => []
                ];
                $t = count($matches[2]);
                foreach ($matches[2] as $i => $class) {
                    $data[$matches[1][$i]][] = $class;
                }
                file_put_contents(storage_path('logs/icons.json'), json_encode($data, JSON_PRETTY_PRINT));
            }
        }
        return Command::SUCCESS;
    }
}
