<?php
     
namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Contracts\Auth\Guard;
use File;
use App\Models\Setup\DBVersion;
use Schema;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class UpdateDB
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct( Guard $auth, Filesystem $files)
    {
        $this->auth = $auth;
        $this->files = $files;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {                
        
        $this->updateRun( "updateDB",new DBVersion);           

        return $next($request);
    }

    /**
     * run update by file n table
     */
    public function updateRun( $file, $modelName )
    {
        $model = $modelName;

        // open and read the file
        $file = fopen(base_path( $file.'.txt' ), "r");

        $line_of_text = '';
        while (($c = fgets($file)) !== false) {
            $line_of_text .= $c;
        }
        // create array "students" and put each row of the text file into individual student record
        $file = explode("\n", $line_of_text);
        
        //menata file txt
        $version = [];
        $versionIteration = [];

        if(!Schema::hasTable( $modelName->getTable() ))
        {
            foreach ($file as $key => $value) {                
                if(strstr($value, ' ', true) == 'Version')
                {
                    if(!empty($versionIteration))
                    {
                        $version[] = $versionIteration;
                    }

                    $versionIteration = [
                        "key" => $key,
                        "value" => $value,
                        "query" => ''
                    ];

                }
                else
                {   
                    $versionIteration["query"] .= $value;
                }

                if( (count($file)-1) == $key)
                {
                    $version[] = $versionIteration;
                }
            }
        }
        else
        {
            foreach ($file as $key => $value) {
                if(strstr($value, ' ', true) == 'Version')
                {                    

                    if(!empty($versionIteration))
                    {
                        $version[] = $versionIteration;
                    }

                    $versionIteration = [
                        "key" => $key,
                        "value" => $value,
                        "query" => ''
                    ];

                    $exist = $modelName->where('version',str_replace('Version ', '', $value))->count();

                    if($exist > 0)
                    {
                        break;
                    }
                }
                else
                {   
                    $versionIteration["query"] .= $value;
                }

            }
        }
        
        $collection = collect($version);

        $reversed = $collection->reverse();
        
        // dd($reversed->all());
        \DB::beginTransaction();

        // execute data sql
        foreach ($reversed->all() as $key => $value) 
        {      
            // dipisahkan ;
            $query = explode(';',$value['query']);
            
            foreach ($query as $keys => $values) {

                $migrateLog = explode(' ',$values);

                // untuk migration
                if( $migrateLog[0] == 'migrate' )
                {
                    if($migrateLog[1] == 'all')
                    {
                        $this->runMigration();
                    }
                    else
                    {
                        $this->runMigrationFile( $migrateLog[1], $migrateLog[2] );                        
                    }
                    continue;
                }

                // untuk sql
                $values = trim($values);                
                if(!empty($values))
                {
                    \DB::statement($values.';');
                }

            }            

            $model->create([
                'query' => $value['query'],
                'version' => str_replace('Version ', '', $value['value'])
            ]);
        }

        \DB::commit();
    }
    /**
     * find all migration file and run
     */
    public function runMigration()
    {
        $path = base_path()."/database/migrations";

        $files = $this->getMigrationFiles($path);

        $this->requireFiles($path, $files);

        foreach ($files as $file) {
            $migration = $this->resolve($file);
            $migration->up();
        }
    }

    /**
     * run one migration file and run 
     * @param  string  $files name of files migrations
     * @param  string  $type up or down(rollback)
     */
    public function runMigrationFile($files,$type = 'up')
    {
        $path = base_path()."/database/migrations";

        $files = [
            $files
        ];
        
        $this->requireFiles($path, $files);

        foreach ($files as $file) {
            $migration = $this->resolve($file);
            $migration->$type();
        }
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string  $path
     * @return array
     */
    public function getMigrationFiles($path)
    {
        $files = $this->files->glob($path.'/*_*.php');
        // Once we have the array of files in the directory we will just remove the
        // extension and take the basename of the file which is all we need when
        // finding the migrations that haven't been run against the databases.
        if ($files === false) {
            return [];
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));

        }, $files);

        // Once we have all of the formatted file names we will sort them and since
        // they all start with a timestamp this should give us the migrations in
        // the order they were actually created by the application developers.
        sort($files);

        return $files;
    }

    /**
     * Require in all the migration files in a given path.
     *
     * @param  string  $path
     * @param  array   $files
     * @return void
     */
    public function requireFiles($path, array $files)
    {
        foreach ($files as $file) {
            $this->files->requireOnce($path.'/'.$file.'.php');
        }
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($file)
    {
        $file = implode('_', array_slice(explode('_', $file), 4));

        $class = Str::studly($file);

        return new $class;
    }
}
