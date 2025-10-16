<?php

namespace App\Http\Controllers;

use App\Services\MachineService;
use App\Http\Requests\Machine\StoreMachineRequest;
use App\Http\Requests\Machine\UpdateMachineRequest;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    protected $machineService;

    public function __construct(MachineService $machineService)
    {
        $this->machineService = $machineService;
        $this->middleware('permission:machine-list')->only(['index', 'show']);
        $this->middleware('permission:machine-create')->only(['create', 'store']);
        $this->middleware('permission:machine-edit')->only(['edit', 'update']);
        $this->middleware('permission:machine-delete')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            $machines = $this->machineService->getMachinesForDataTable();
            return \Yajra\DataTables\Facades\DataTables::of($machines)
                ->addIndexColumn()
                ->addColumn('image', function ($machine) {
                    if ($machine->media && $machine->media->where('file_type', 'image')->count() > 0) {
                        $images = $machine->media->where('file_type', 'image')->take(3);
                        $html = '<div class="d-flex gap-1">';
                        foreach ($images as $media) {
                            $html .= '<a href="' . asset('storage/' . $media->file_path) . '" 
                                    data-lightbox="machine-' . $machine->id . '" 
                                    data-title="' . $machine->name . '">
                                    <img src="' . asset('storage/' . $media->file_path) . '" 
                                    alt="Machine Image" 
                                    class="rounded border" 
                                    style="width:40px;height:40px;object-fit:cover;">
                                </a>';
                        }
                        if ($machine->media->where('file_type', 'image')->count() > 3) {
                            $remainingImages = $machine->media->where('file_type', 'image')->skip(3);
                            foreach ($remainingImages as $media) {
                                $html .= '<a href="' . asset('storage/' . $media->file_path) . '" 
                                        data-lightbox="machine-' . $machine->id . '" 
                                        data-title="' . $machine->name . '" 
                                        style="display:none;">
                                    </a>';
                            }
                            $html .= '<div class="bg-secondary text-white rounded border d-flex align-items-center justify-content-center" 
                                    style="width:40px;height:40px;font-size:12px;">+' . 
                                    ($machine->media->where('file_type', 'image')->count() - 3) . 
                                    '</div>';
                        }
                        $html .= '</div>';
                        return $html;
                    }
                    return '-';
                })
                ->addColumn('actions', function ($machine) {
                    return view('machines.actions', compact('machine'))->render();
                })
                ->rawColumns(['image', 'actions'])
                ->make(true);
        }
        return view('machines.index');
    }

    public function create()
    {
        return view('machines.create');
    }

    public function store(StoreMachineRequest $request)
    {
        try {
            $data = $request->validated();
            $machine = $this->machineService->createMachine($data);

            // Handle media upload
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('machine_media', 'public');
                    $mime = $file->getMimeType();
                    $type = str_starts_with($mime, 'image') ? 'image' : (str_starts_with($mime, 'video') ? 'video' : 'other');
                    $machine->media()->create([
                        'file_path' => $path,
                        'file_type' => $type,
                    ]);
                }
            }

            return redirect()->route('machines.index')
                ->with('success', 'Machine created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating machine', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $machine = $this->machineService->getMachineById($id);
        return view('machines.show', compact('machine'));
    }

    public function edit($id)
    {
        $machine = $this->machineService->getMachineById($id);
        return view('machines.edit', compact('machine'));
    }

    public function update(UpdateMachineRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $machine = $this->machineService->updateMachine($id, $data);

            // Handle media upload
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('machine_media', 'public');
                    $mime = $file->getMimeType();
                    $type = str_starts_with($mime, 'image') ? 'image' : (str_starts_with($mime, 'video') ? 'video' : 'other');
                    $machine->media()->create([
                        'file_path' => $path,
                        'file_type' => $type,
                    ]);
                }
            }

            return redirect()->route('machines.index')
                ->with('success', 'Machine updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating machine', [
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
            $machine = $this->machineService->getMachineById($id);
            
            // Delete all media files
            foreach ($machine->media as $media) {
                if (\Storage::disk('public')->exists($media->file_path)) {
                    \Storage::disk('public')->delete($media->file_path);
                }
            }
            
            $this->machineService->deleteMachine($id);
            return response()->json(['success' => 'Machine deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting machine: ' . $e->getMessage()], 500);
        }
    }

    public function destroyMedia($mediaId)
    {
        try {
            $media = \App\Models\MachineMedia::findOrFail($mediaId);
            $machine = $media->machine;
            
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