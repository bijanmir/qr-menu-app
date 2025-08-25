<?php

namespace App\Console\Commands;

use App\Services\MenuSharingService;
use Illuminate\Console\Command;

class SyncLinkedItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:sync-linked-items
                          {--restaurant= : Only sync items for a specific restaurant}
                          {--dry-run : Show what would be synced without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all linked menu items with their source items';

    protected MenuSharingService $sharingService;

    public function __construct(MenuSharingService $sharingService)
    {
        parent::__construct();
        $this->sharingService = $sharingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting linked item synchronization...');
        
        $dryRun = $this->option('dry-run');
        $restaurantId = $this->option('restaurant');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Get all linked items that need syncing
        $query = \App\Models\Item::linked()
            ->with(['sourceItem', 'category.menu.restaurant', 'sourceRestaurant'])
            ->whereHas('sourceItem');

        if ($restaurantId) {
            $query->whereHas('category.menu', function ($query) use ($restaurantId) {
                $query->where('restaurant_id', $restaurantId);
            });
        }

        $linkedItems = $query->get();
        $needsSyncItems = $linkedItems->filter(fn($item) => $item->needsSync());

        $this->info("Found {$linkedItems->count()} linked items total");
        $this->info("Found {$needsSyncItems->count()} items that need syncing");

        if ($needsSyncItems->isEmpty()) {
            $this->info('All linked items are already up to date!');
            return Command::SUCCESS;
        }

        $this->table(
            ['Restaurant', 'Item', 'Category', 'Source Restaurant', 'Last Synced'],
            $needsSyncItems->map(function ($item) {
                return [
                    $item->category->menu->restaurant->name,
                    $item->name,
                    $item->category->name,
                    $item->sourceRestaurant->name,
                    $item->last_synced_at ? $item->last_synced_at->diffForHumans() : 'Never'
                ];
            })->toArray()
        );

        if ($dryRun) {
            $this->info('DRY RUN: Would sync ' . $needsSyncItems->count() . ' items');
            return Command::SUCCESS;
        }

        if (!$this->confirm('Do you want to proceed with syncing these items?')) {
            $this->info('Sync cancelled');
            return Command::SUCCESS;
        }

        $syncedCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar($needsSyncItems->count());
        $progressBar->start();

        foreach ($needsSyncItems as $item) {
            try {
                $synced = $this->sharingService->syncLinkedItem($item);
                if ($synced) {
                    $syncedCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error syncing item {$item->name}: " . $e->getMessage());
                $errorCount++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Sync completed!");
        $this->info("Successfully synced: {$syncedCount} items");
        
        if ($errorCount > 0) {
            $this->error("Errors encountered: {$errorCount} items");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
