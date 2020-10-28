<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function store(Request $request)
    {
        try {
            // save uploaded file in storage
            $path = $request->file->storeAs(
                $request->folder,
                $request->name . '-' . time() . '.' . $request->file->extension(),
                'public'
            );

            return $this->successResponse(['path' => $path ? '/storage/' . $path : '']);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
