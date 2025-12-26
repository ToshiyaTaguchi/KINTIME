<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\Admin\AttendanceCorrectionController as AdminAttendanceCorrectionController;

class AttendanceCorrectionRouterController extends Controller
{
    public function index(
        Request $request,
        AttendanceCorrectionController $userController,
        AdminAttendanceCorrectionController $adminController
    ) {
        $user = $request->user();

        if ($user?->can('admin')) {
            return app()->call([$adminController, 'index']);
        }

        return app()->call([$userController, 'index']);
    }
}
