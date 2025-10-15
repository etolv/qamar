<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeneratedSalaryRequest;
use App\Models\GeneratedSalary;
use App\Services\GeneratedSalaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GeneratedSalaryController extends Controller
{

    public function __construct(protected GeneratedSalaryService $generatedSalaryService)
    {
        $this->middleware('can:read_generated_salary')->only('index', 'show');
        $this->middleware('can:create_generated_salary')->only('create', 'store', 'import');
        $this->middleware('can:update_generated_salary')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.salary.generated.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            GeneratedSalary::with([
                'employee' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                },
                'salary'
            ])->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                [$from, $to] = explode(' to ', $request->date);
                $query->whereBetween('created_at', [$from, $to]);
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when($request->month, function ($query) use ($request) {
                $query->where('month', $request->month);
            })->latest()
        )->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->addColumn('month_name', function ($item) {
            return _t($item->month->name);
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.salary.generated.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGeneratedSalaryRequest $request)
    {
        $data = $request->afterValidation();
        $generatedSalary = $this->generatedSalaryService->generate($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('generated-salary.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
