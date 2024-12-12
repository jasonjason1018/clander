<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $fileName = $this->argument('name');
        $filePath = base_path("app/Services/$fileName.php");

        if (!File::exists(app_path('Services'))) {
            File::makeDirectory(app_path('Services'));
        }

        if (File::exists($filePath)) {
            $this->error("Service $fileName already exists!");
        }

        $serviceContent = "<?php\n\nnamespace App\Services;\n\nclass {$fileName}\n{\n    // Your service logic here\n}\n";

        File::put($filePath, $serviceContent);

        $this->info("Service {$fileName} created successfully in the Services directory.");
    }
}
