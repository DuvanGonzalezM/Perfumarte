<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->guardAgainstRunningOnAWorkingDatabase($app);

        return $app;
    }

    /**
     * Las pruebas Feature usan RefreshDatabase, que empieza por migrate:fresh.
     * Si la conexión apunta a una base que no sea la de pruebas, el primer test
     * borra los datos reales. Antes esto no se notaba: `make test` destruía la
     * base de desarrollo sin decir nada.
     */
    protected function guardAgainstRunningOnAWorkingDatabase(Application $app): void
    {
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");

        if ($database === ':memory:' || str_ends_with((string) $database, '_testing')) {
            return;
        }

        throw new \RuntimeException(
            "Las pruebas apuntan a la base '{$database}', que no es una base de pruebas. ".
            'RefreshDatabase la borraría entera. Defina DB_DATABASE en phpunit.xml '.
            'con un nombre terminado en "_testing" y cree esa base antes de continuar.'
        );
    }
}
