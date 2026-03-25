<?php

namespace App\Console\Commands;

use App\Models\AddOn;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserActiveModule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncModules extends Command
{
    protected $signature = 'modules:sync';
    protected $description = 'Sync module.json → add_ons, create Enterprise plan, update all plans with all modules';

    public function handle(): int
    {
        // ── 1. Sync add_ons from every module.json ─────────────────────────────
        $packagesPath = base_path('packages/workdo');

        if (!is_dir($packagesPath)) {
            $this->error("Packages directory not found: {$packagesPath}");
            return 1;
        }

        $synced = 0;
        foreach (File::directories($packagesPath) as $package) {
            $filePath = $package . '/module.json';
            if (!file_exists($filePath)) {
                continue;
            }

            $data = json_decode(file_get_contents($filePath), true);
            if (empty($data['name'])) {
                continue;
            }

            AddOn::updateOrCreate(
                ['module' => $data['name']],
                [
                    'name'          => $data['alias'] ?? $data['name'],
                    'monthly_price' => $data['monthly_price'] ?? 0,
                    'yearly_price'  => $data['yearly_price'] ?? 0,
                    'package_name'  => $data['package_name'] ?? strtolower($data['name']),
                    'is_enable'     => true,
                    'for_admin'     => $data['for_admin'] ?? false,
                    'priority'      => $data['priority'] ?? 0,
                ]
            );
            $synced++;
        }

        $this->info("Step 1: {$synced} modules synced into add_ons.");

        // ── 2. Collect all module names now in add_ons ─────────────────────────
        $allModules = AddOn::pluck('module')->toArray();

        if (empty($allModules)) {
            $this->warn('No modules found in add_ons — skipping plan updates.');
            return 0;
        }

        // ── 3. Create / update the Enterprise plan ─────────────────────────────
        $superAdmin = User::where('type', 'superadmin')->first();

        $enterprise = Plan::updateOrCreate(
            ['name' => 'Enterprise'],
            [
                'description'           => 'All modules, unlimited users — full access',
                'number_of_users'       => -1,
                'status'                => true,
                'free_plan'             => true,
                'modules'               => $allModules,
                'package_price_yearly'  => 0,
                'package_price_monthly' => 0,
                'storage_limit'         => 0,
                'trial'                 => false,
                'trial_days'            => 0,
                'created_by'            => $superAdmin?->id ?? 1,
            ]
        );

        $this->info("Step 2: Enterprise plan upserted (id={$enterprise->id}, " . count($allModules) . " modules).");

        // ── 4. Update every existing plan to include all modules ───────────────
        // Uses raw JSON so the update bypasses model casting (safe for both
        // MySQL and PostgreSQL).
        Plan::query()->update([
            'modules'      => json_encode(array_values($allModules)),
            'storage_limit' => 0,
        ]);

        $this->info('Step 3: All plans updated to include every module.');

        // ── 5. Assign the Enterprise plan to company@example.com ───────────────
        $company = User::where('email', 'company@example.com')->first();
        if ($company) {
            $company->active_plan     = $enterprise->id;
            $company->plan_expire_date = null;   // never expires
            $company->total_user      = -1;      // unlimited sub-users
            $company->storage_limit   = 0;
            $company->save();
            $this->info("Step 4: company@example.com assigned to Enterprise plan.");
        }

        // ── 6. Ensure all modules are in user_active_modules for that company ──
        if ($company) {
            foreach ($allModules as $moduleName) {
                UserActiveModule::firstOrCreate([
                    'user_id' => $company->id,
                    'module'  => $moduleName,
                ]);
            }
            $this->info("Step 5: UserActiveModule rows ensured for company@example.com.");
        }

        $this->info('modules:sync complete.');
        return 0;
    }
}
