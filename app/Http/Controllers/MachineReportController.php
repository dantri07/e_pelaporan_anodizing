<?php

namespace App\Http\Controllers;

use App\Services\MachineReportService;
use App\Http\Requests\MachineReport\StoreMachineReportRequest;
use App\Http\Requests\MachineReport\UpdateMachineReportRequest;
use App\Models\User;
use App\Models\Action;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use App\Notifications\MachineReportAssigned;

class MachineReportController extends Controller
{
    protected $machineReportService;

    public function __construct(MachineReportService $machineReportService)
    {
        $this->machineReportService = $machineReportService;
        $this->middleware('permission:machine-report-list')->only('index');
        $this->middleware('permission:machine-report-create')->only(['create', 'store']);
        $this->middleware('permission:machine-report-edit')->only(['edit', 'update']);
        $this->middleware('permission:machine-report-delete')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            $reports = \App\Models\MachineReport::with(['user', 'actions'])->select('machine_reports.*');
            return DataTables::of($reports)
                ->addIndexColumn()
                ->addColumn('user', function ($report) {
                    return $report->user ? $report->user->name : '-';
                })
                ->addColumn('action', function ($report) {
                    if ($report->actions && $report->actions->count() > 0) {
                        return $report->actions->first()->status;
                    }
                    return '-';
                })
                ->addColumn('media', function ($report) {
                    if ($report->media && $report->media->where('file_type', 'image')->count() > 0) {
                        $images = $report->media->where('file_type', 'image')->take(3);
                        $html = '<div class="d-flex gap-1">';
                        foreach ($images as $media) {
                            $html .= '<a href="' . asset('storage/' . $media->file_path) . '" 
                                    data-lightbox="report-' . $report->id . '" 
                                    data-title="' . $report->machine_name . '">
                                    <img src="' . asset('storage/' . $media->file_path) . '" 
                                    alt="Report Image" 
                                    class="rounded border" 
                                    style="width:40px;height:40px;object-fit:cover;">
                                </a>';
                        }
                        if ($report->media->where('file_type', 'image')->count() > 3) {
                            $remainingImages = $report->media->where('file_type', 'image')->skip(3);
                            foreach ($remainingImages as $media) {
                                $html .= '<a href="' . asset('storage/' . $media->file_path) . '" 
                                        data-lightbox="report-' . $report->id . '" 
                                        data-title="' . $report->machine_name . '" 
                                        style="display:none;">
                                    </a>';
                            }
                            $html .= '<div class="bg-secondary text-white rounded border d-flex align-items-center justify-content-center" 
                                    style="width:40px;height:40px;font-size:12px;">+' . 
                                    ($report->media->where('file_type', 'image')->count() - 3) . 
                                    '</div>';
                        }
                        $html .= '</div>';
                        return $html;
                    }
                    return '-';
                })
                ->addColumn('actions', function ($report) {
                    return view('machine_reports.actions', compact('report'))->render();
                })
                ->rawColumns(['actions', 'media'])
                ->make(true);
        }
        return view('machine_reports.index');
    }

    public function create()
    {
        $users = User::all();
        $actions = Action::all();
        $technicians = User::role('technician')->get();
        return view('machine_reports.create', compact('users', 'actions', 'technicians'));
    }

    public function store(StoreMachineReportRequest $request)
    {
        try {
            $report = $this->machineReportService->createMachineReport($request->validated());

            // Handle media upload (for create)
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('machine_report_media', 'public');
                    $mime = $file->getMimeType();
                    $type = str_starts_with($mime, 'image') ? 'image' : (str_starts_with($mime, 'video') ? 'video' : 'other');
                    $report->media()->create([
                        'file_path' => $path,
                        'file_type' => $type,
                    ]);
                }
            }

            // Kirim notifikasi ke teknisi yang dipilih
            if ($report->technician_id) {
                $technician = \App\Models\User::find($report->technician_id);
                if ($technician) {
                    \Log::info('Attempting to send email to technician', [
                        'technician_id' => $technician->id,
                        'technician_email' => $technician->email,
                        'report_id' => $report->id
                    ]);
                    try {
                        $technician->notify(new MachineReportAssigned($report));
                        \Log::info('Email notification sent successfully');
                    } catch (\Exception $e) {
                        \Log::error('Failed to send email notification', [
                            'error' => $e->getMessage(),
                            'technician_id' => $technician->id,
                            'report_id' => $report->id
                        ]);
                    }
                } else {
                    \Log::warning('Technician not found', ['technician_id' => $report->technician_id]);
                }
            } else {
                \Log::info('No technician assigned to report', ['report_id' => $report->id]);
            }
            return redirect()->route('machine-reports.index')
                ->with('success', 'Machine report created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating machine report', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $report = $this->machineReportService->getMachineReportById($id);
            if (auth()->id() !== $report->user_id) {
                abort(403, 'You are not authorized to edit this report.');
            }
            $users = User::all();
            $actions = Action::all();
            $technicians = User::role('technician')->get();
            return view('machine_reports.edit', compact('report', 'users', 'actions', 'technicians'));
        } catch (\Exception $e) {
            return redirect()->route('machine-reports.index')
                ->with('error', 'Error fetching machine report: ' . $e->getMessage());
        }
    }

    public function update(UpdateMachineReportRequest $request, $id)
    {
        try {
            $report = $this->machineReportService->getMachineReportById($id);
            if (auth()->id() !== $report->user_id) {
                abort(403, 'You are not authorized to update this report.');
            }
            
            $oldTechnicianId = $report->technician_id;
            $report = $this->machineReportService->updateMachineReport($id, $request->validated());
            
            // Handle media upload
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('machine_report_media', 'public');
                    $mime = $file->getMimeType();
                    $type = str_starts_with($mime, 'image') ? 'image' : (str_starts_with($mime, 'video') ? 'video' : 'other');
                    $report->media()->create([
                        'file_path' => $path,
                        'file_type' => $type,
                    ]);
                }
            }
            
            // Send notification if technician is assigned or changed
            if ($report->technician_id && $report->technician_id !== $oldTechnicianId) {
                $technician = \App\Models\User::find($report->technician_id);
                if ($technician) {
                    \Log::info('Attempting to send email to technician for updated report', [
                        'technician_id' => $technician->id,
                        'technician_email' => $technician->email,
                        'report_id' => $report->id
                    ]);
                    try {
                        $technician->notify(new MachineReportAssigned($report));
                        \Log::info('Email notification sent successfully for updated report');
                    } catch (\Exception $e) {
                        \Log::error('Failed to send email notification for updated report', [
                            'error' => $e->getMessage(),
                            'technician_id' => $technician->id,
                            'report_id' => $report->id
                        ]);
                    }
                } else {
                    \Log::warning('Technician not found for updated report', ['technician_id' => $report->technician_id]);
                }
            }
            
            return redirect()->route('machine-reports.index')
                ->with('success', 'Machine report updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating machine report', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $report = $this->machineReportService->getMachineReportById($id);
            if (auth()->id() !== $report->user_id) {
                return response()->json(['error' => 'You are not authorized to delete this report.'], 403);
            }
            $this->machineReportService->deleteMachineReport($id);
            return response()->json(['success' => 'Machine report deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting machine report: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $report = $this->machineReportService->getMachineReportDetail($id);
        return view('machine_reports.show', compact('report'));
    }

    public function destroyMedia($mediaId)
    {
        try {
            $media = \App\Models\MachineReportMedia::findOrFail($mediaId);
            $report = $media->machineReport;
            
            // Check if user is authorized to delete the media
            if (auth()->id() !== $report->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this media file.'
                ], 403);
            }
            
            // Delete the file from storage
            if (\Storage::disk('public')->exists($media->file_path)) {
                \Storage::disk('public')->delete($media->file_path);
            }
            
            // Delete the media record
            $media->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Media file deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting media file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media file: ' . $e->getMessage()
            ], 500);
        }
    }
} 