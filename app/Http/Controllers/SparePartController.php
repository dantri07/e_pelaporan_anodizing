<?php

namespace App\Http\Controllers;

use App\Services\SparePartService;
use App\Http\Requests\SparePart\StoreSparePartRequest;
use App\Http\Requests\SparePart\UpdateSparePartRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SparePartController extends Controller
{
    protected $sparePartService;

    public function __construct(SparePartService $sparePartService)
    {
        $this->sparePartService = $sparePartService;
        $this->middleware('permission:spare-part-list')->only('index');
        $this->middleware('permission:spare-part-create')->only(['create', 'store']);
        $this->middleware('permission:spare-part-edit')->only(['edit', 'update']);
        $this->middleware('permission:spare-part-delete')->only('destroy');
    }

    /**
     * Display a listing of spare parts.
     */
    public function index()
    {
        if (request()->ajax()) {
            $spareParts = \App\Models\SparePart::query();
            return DataTables::of($spareParts)
                ->addIndexColumn()
                ->addColumn('action', function ($sparePart) {
                    return view('spare_parts.actions', compact('sparePart'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('spare_parts.index');
    }

    /**
     * Show the form for creating a new spare part.
     */
    public function create()
    {
        return view('spare_parts.create');
    }

    /**
     * Store a newly created spare part in storage.
     */
    public function store(StoreSparePartRequest $request)
    {
        try {
            $this->sparePartService->createSparePart($request->validated());
            return redirect()->route('spare-parts.index')
                ->with('success', 'Spare part created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating spare part: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified spare part.
     */
    public function edit($id)
    {
        try {
            $sparePart = $this->sparePartService->getSparePartById($id);
            return view('spare_parts.edit', compact('sparePart'));
        } catch (\Exception $e) {
            return redirect()->route('spare-parts.index')
                ->with('error', 'Error fetching spare part: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified spare part in storage.
     */
    public function update(UpdateSparePartRequest $request, $id)
    {
        try {
            $this->sparePartService->updateSparePart($id, $request->validated());
            return redirect()->route('spare-parts.index')
                ->with('success', 'Spare part updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating spare part: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified spare part from storage.
     */
    public function destroy($id)
    {
        try {
            $this->sparePartService->deleteSparePart($id);
            return response()->json(['success' => 'Spare part deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting spare part: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get low stock notifications for AJAX polling
     */
    public function getLowStockNotifications(Request $request)
    {
        $lowStockSpareParts = $this->sparePartService->getLowStockSpareParts();
        return response()->json([
            'count' => $lowStockSpareParts->count(),
            'items' => $lowStockSpareParts->map(function($sparePart) {
                return [
                    'id' => $sparePart->id,
                    'name' => $sparePart->name,
                    'quantity' => $sparePart->quantity,
                    'edit_url' => route('spare-parts.edit', $sparePart->id),
                ];
            }),
        ]);
    }
} 