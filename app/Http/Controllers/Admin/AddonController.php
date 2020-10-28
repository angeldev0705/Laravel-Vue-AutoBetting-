<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PackageManager;
use App\Helpers\ReleaseManager;
use App\Http\Controllers\Controller;
use App\Services\ArtisanService;
use App\Services\DotEnvService;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AddonController extends Controller
{
    public function index(PackageManager $packageManager, ReleaseManager $releaseManager)
    {
        return [
            'releases' => $releaseManager->getInfo(),
            'packages' => $packageManager->getAll()
        ];
    }

    public function get($packageId)
    {
        return [
            'code' => env($this->getCodeVariableName($packageId))
        ];
    }

    /**
     * Disable add-on
     *
     * @param $packageId
     * @param PackageManager $packageManager
     * @return array
     */
    public function disable($packageId, PackageManager $packageManager)
    {
        $package = $packageManager->get($packageId);

        if (!$package) {
            return $this->errorResponse(__('Package ":id" does not exist.', ['id' => $packageId]));
        }

        try {
            if (Storage::disk('local')->put('packages/' . $packageId . '/disabled', '')) {
                return $this->successResponse(__('Add-on ":name" successfully disabled.', ['name' => $package->name]));
            } else {
                return $this->errorResponse(__('Could not disable the add-on. Please check that storage/app folder is writable.'));
            }
        } catch(\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Enable add-on
     *
     * @param $packageId
     * @param PackageManager $packageManager
     * @return array
     */
    public function enable($packageId, PackageManager $packageManager)
    {
        $package = $packageManager->get($packageId);

        if (!$package) {
            return $this->errorResponse(__('Package ":id" does not exist.', ['id' => $packageId]));
        }

        try {
            if (Storage::disk('local')->delete('packages/' . $packageId . '/disabled')) {
                return $this->successResponse(__('Add-on ":name" successfully enabled.', ['name' => $package->name]));
            } else {
                return $this->errorResponse(__('Could not enable the add-on. Please check that storage/app folder is writable.'));
            }
        } catch(\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function install($packageId, Request $request, LicenseService $licenseService, PackageManager $packageManager, ReleaseManager $releaseManager, DotEnvService $dotEnvService)
    {
        set_time_limit(1800);

        $releases = $releaseManager->getInfo();
        $package = $packageManager->get($packageId);

        if (!$package)
            return $this->errorResponse(__('Package ":id" does not exist.', ['id' => $packageId]));

        if (!isset($package->hash) || !isset($releases->{'add-ons'}->{$package->id}))
            return $this->errorResponse(__('Package ":id" can not be installed or upgraded.', ['id' => $packageId]));

        $response = $licenseService->download($request->code, env('LICENSEE_EMAIL'), $package->hash, $releases->{'add-ons'}->{$package->id}->version);

        // if response is not binary (files)
        if (is_object($response))
            return $this->errorResponse($response->message);

        // save code as env variable
        $dotEnvService->save([
            $this->getCodeVariableName($packageId) => $request->code
        ]);

        // unzip package
        try {
            $zipFileName = 'releases/' . $packageId . '.zip';
            $storage = Storage::disk('local');
            $storage->put($zipFileName, $response);

            // extract downloaded zip archive
            $zip = new ZipArchive();
            if ($zip->open($storage->path($zipFileName)) === TRUE) {
                $zip->extractTo(base_path());
                $zip->close();
            }

            // delete zip archive
            $storage->delete($zipFileName);
        } catch(\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

        // If the package is being installed (hence $package->enabled is FALSE) then
        //  register its service provider and register autoload once again (because currently loaded function is not aware of new classes)
        if (!$package->enabled) {
            spl_autoload_register([new PackageManager(), 'autoload']);
            foreach ($package->providers as $provider) {
                app()->register($provider);
            }
        }

        // run migrations
        ArtisanService::migrateAndSeed();
        // clear cache
        ArtisanService::clearAllCaches();

        return $this->successResponse(__('Add-on ":name" successfully installed or updated. Please check the add-on documentation to see if there are any extra steps required.', ['name' => $package->name]));
    }

    public function changelog($packageId)
    {
        $path = base_path('packages/' . $packageId . '/CHANGELOG.txt');

        return ['changelog' => File::exists($path) ? File::get($path) : __('No changelog found.')];
    }

    private function getCodeVariableName($packageId)
    {
        return strtoupper(str_replace('-', '_', $packageId) . '_PURCHASE_CODE');
    }
}
