<?php

namespace App\Http\Controllers;

use App\Services\ActionService;
use App\Http\Requests\Action\StoreActionRequest;
use App\Http\Requests\Action\UpdateActionRequest;
use App\Models\SparePart;
use App\Models\MachineReport;
use App\Notifications\ActionCreated;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActionController extends Controller
{
    protected $actionService;

    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
        $this->middleware('permission:action-list')->only('index');
        $this->middleware('permission:action-create')->only(['create', 'store']);
        $this->middleware('permission:action-edit')->only(['edit', 'update']);
        $this->middleware('permission:action-delete')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            $actions = \App\Models\Action::with(['technician', 'sparePart', 'machineReports'])
                ->select('actions.*');

            return DataTables::of($actions)
                ->addIndexColumn()
                ->addColumn('technician', function ($action) {
                    return $action->technician ? $action->technician->name : '-';
                })
                ->addColumn('machine_report', function ($action) {
                    if ($action->machineReports->isEmpty()) {
                        return null;
                    }
                    $report = $action->machineReports->first();
                    return [
                        'machine_name' => $report->machine_name,
                        'edit_url' => route('machine-reports.edit', $report->id)
                    ];
                })
                ->addColumn('spare_part', function ($action) {
                    return $action->sparePart;
                })
                ->addColumn('actions', function ($action) {
                    $buttons = '';
                    
                    if (auth()->user()->can('action-edit') && 
                        ($action->technician_id === auth()->id() || auth()->user()->hasRole('admin'))) {
                        $buttons .= '<a href="' . route('actions.edit', $action->action_id) . '" class="btn btn-sm btn-warning mr-1">
                            <i class="fas fa-edit"></i>
                        </a>';
                    }
                    
                    if (auth()->user()->can('action-delete') && 
                        ($action->technician_id === auth()->id() || auth()->user()->hasRole('admin'))) {
                        $buttons .= '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $action->action_id . '">
                            <i class="fas fa-trash"></i>
                        </button>';
                    }
                    
                    return $buttons;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('actions.index');
    }

    public function create()
    {
        $spareParts = SparePart::all();
        $report = null;
        
        if (request()->has('report_id')) {
            $report = MachineReport::findOrFail(request()->report_id);
            // Check if the current user is the assigned technician
            if ($report->technician_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                abort(403, 'You are not authorized to add actions to this report.');
            }
        }
        
        return view('actions.create', compact('spareParts', 'report'));
    }

    public function store(StoreActionRequest $request)
    {
        try {
            \Log::info('ActionController@store request', $request->all());
            $action = $this->actionService->createAction(
                $request->except('images'),
                $request->file('images')
            );
            
            // Attach to machine report if provided
            if ($request->has('report_id')) {
                $report = MachineReport::findOrFail($request->report_id);
                $action->machineReports()->attach($report->id);
                
                // Notify the report creator
                if ($report->user_id !== auth()->id()) {
                    $report->user->notify(new ActionCreated($action, $report));
                }
            }
            
            $message = 'Action created successfully.';
            if ($request->has('report_id')) {
                return redirect()->route('machine-reports.edit', $request->report_id)
                    ->with('success', $message);
            }
            
            return redirect()->route('actions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('ActionController@store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $action = $this->actionService->getActionById($id);
            
            // Check if the current user is the technician who created the action or an admin
            if ($action->technician_id !== auth()->id()) {
                abort(403, 'You are not authorized to edit this action.');
            }
            
            $spareParts = SparePart::all();
            return view('actions.edit', compact('action', 'spareParts'));
        } catch (\Exception $e) {
            return redirect()->route('actions.index')
                ->with('error', 'Error fetching action: ' . $e->getMessage());
        }
    }

    public function update(UpdateActionRequest $request, $id)
    {
        try {
            \Log::info('ActionController@update request', $request->all());
            $action = $this->actionService->getActionById($id);
            
            // Check if the current user is the technician who created the action or an admin
            if ($action->technician_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                abort(403, 'You are not authorized to update this action.');
            }
            
            $action = $this->actionService->updateAction(
                $id,
                $request->except('images'),
                $request->file('images')
            );
            
            $message = 'Action updated successfully.';
            if ($action->machineReports->isNotEmpty()) {
                return redirect()->route('machine-reports.edit', $action->machineReports->first()->id)
                    ->with('success', $message);
            }
            
            return redirect()->route('actions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('ActionController@update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $action = $this->actionService->getActionById($id);
            
            // Check if the current user is the technician who created the action or an admin
            if ($action->technician_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json(['error' => 'You are not authorized to delete this action.'], 403);
            }
            
            $this->actionService->deleteAction($id);
            return response()->json(['success' => 'Action deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting action: ' . $e->getMessage()], 500);
        }
    }
} 