<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear base de datos si no existe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = env('DB_DATABASE');

        // Cambiar temporalmente la DB de conexión
        Config::set('database.connections.mysql.database', null);

        // Reconectar
        DB::purge('mysql');

        // Crear DB
        DB::statement("CREATE DATABASE IF NOT EXISTS `$database`");

        $this->info("Base de datos '$database' creada o ya existe.");
    }
}
