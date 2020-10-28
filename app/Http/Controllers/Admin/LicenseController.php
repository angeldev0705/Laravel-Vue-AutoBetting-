<?php

namespace App\Http\Controllers\Admin;

use App\Services\LicenseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LicenseController extends Controller
{
    public function index() {
        return [
            'code'  => config('app.license.code'),
            'email' => config('app.license.email'),
        ];
    }

    public function register(Request $request)
    {
        $success = FALSE;
        $message = '';

        try {
            $licenseService = new LicenseService();
            $result = $licenseService->register($request->code, $request->email);

            if ($result->success) {
                $success = TRUE;
                $message = __('Your license is successfully registered!');
                $licenseService->save($request->code, $request->email, $result->message);
            } else {
                $message = $result->message;
                $licenseService->save('', '', '');
            }
        } catch(\Exception $e) {
            $message = $e->getMessage();
        }

        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }
}
