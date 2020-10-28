<?php

namespace App\Console\Commands;

use App\Helpers\PackageManager;
use App\Models\User;
use Illuminate\Console\Command;

class CreateGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create games';

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
     * @return mixed
     */
    public function handle()
    {
        $count = random_int(config('settings.bots.games.min_count'), config('settings.bots.games.max_count'));

        // retrieve bots
        $bots = User::active()
            ->bots()
            ->inRandomOrder()
            ->limit($count)
            ->get();

        // if bots exist
        if (!$bots->isEmpty()) {
            // get all game service classes
            $gameServiceClasses = [];
            $packageManager = new PackageManager();
            foreach ($packageManager->getEnabled() as $package) {
                if ($package->type === 'game') {
                    $gameServiceClass = $package->namespace . 'Services\\GameService';

                    if (class_exists($gameServiceClass) && method_exists($gameServiceClass, 'createRandomGame')) {
                        $gameServiceClasses[] = $gameServiceClass;
                    }
                }
            }

            // number of games available for play
            $n = count($gameServiceClasses);

            if ($n > 0) {
                // loop through bots users
                foreach ($bots as $bot) {
                    // pick a random game
                    $i = random_int(0, $n - 1);
                    // create random game
                    $gameServiceClasses[$i]::createRandomGame($bot);
                }
            }
        }
    }
}
