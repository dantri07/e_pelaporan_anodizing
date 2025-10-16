<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SparePartService;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Models\MachineReport;
use App\Models\Action;
use App\Models\SparePart;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SparePartService $sparePartService)
    {
        $this->middleware('auth');

        // Share low stock spare parts with all views
        View::composer('*', function ($view) use ($sparePartService) {
            $view->with('lowStockSpareParts', $sparePartService->getLowStockSpareParts());
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userCount = User::count();
        $machineReportCount = MachineReport::count();
        $actionCount = Action::count();
        $sparePartCount = SparePart::count();
        $roleCount = Role::count();
        $recentMachineReports = MachineReport::with('user')->latest('created_at')->take(5)->get();
        $lowestStockSpareParts = SparePart::orderBy('quantity', 'asc')->take(5)->get();
        return view('dashboard', compact('userCount', 'machineReportCount', 'actionCount', 'sparePartCount', 'roleCount', 'recentMachineReports', 'lowestStockSpareParts'));
    }
} 