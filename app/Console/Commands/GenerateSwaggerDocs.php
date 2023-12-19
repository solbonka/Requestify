<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Generator;

class GenerateSwaggerDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-swagger-docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $swagger = Generator::scan([app_path()]);
        $jsonFile = public_path('swagger.json');
        file_put_contents($jsonFile, $swagger->toJson());
        $this->info('Swagger documentation generated successfully.');
    }
}
