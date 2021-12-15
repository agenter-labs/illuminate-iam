<?php

namespace AgenterLab\IAM\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class PermissionSeed extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'iam:permission-seed {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permission seeder';

    /**
     * Permission map
     */
    private $permissionMap = [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $nameSpace = Str::slug($this->argument('module'), '-');

        $resourceFile = resource_path('permissions/' . $nameSpace . '.php');

        if (!file_exists($resourceFile)) {
            $this->line('');

            $this->error("The permission file does not exists.");
            $this->line('');
            return 1;
        }

        $resource = include $resourceFile;

        $permissions = $resource['permissions'] ?? [];

        $permissionClass = Config::get('iam.models.permission', 'App\Permission');

        foreach ($permissions as $module => $action) {

            $actions = explode(',', $action);

            foreach ($actions as $perm) {

                $permissionValue = $this->permissionMap[$perm] ?? $prem;

                $permissionClass::firstOrCreate([
                    'name' => $permissionValue . '-' . $module,
                    'title' => ucfirst($permissionValue) . ' ' . ucfirst($module)
                ]);
            }
        }

        $roles = $resource['roles'] ?? [];
        $roleClass = Config::get('iam.models.role', 'App\Role');

        foreach ($roles as $roleName => $modules) {
            $roleTitle = Str::headline($roleName);
            $role = $roleClass::firstOrCreate([
                'name' => Str::slug($roleName, '-'),
                'title' => $roleTitle,
                'is_system' => 1
            ]);

            $this->info('Creating Role '. $roleTitle);

            // Reading role permission modules
            foreach ($modules as $module => $value) {

                foreach (explode(',', $value) as $perm) {

                    $permissionValue = $this->permissionMap[$perm] ?? $prem;

                    $role->attachPermission($permissionValue . '-' . $module);
                }
            }
        }
    }
}