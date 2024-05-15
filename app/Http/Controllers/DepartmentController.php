<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psy\Util\Str;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with('employees')->get();
        return response()->json([
            'status'     => 'success',
            'departments with employees' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $department = Department::create([
                'name'        => $request->name,
                'description' => $request->description,
            ]);
            DB::commit();

            return response()->json([
                'status'     => 'Added to the department',
                'department' => $department,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return response()->json([
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return response()->json([
            'status'     => 'success',
            'department' => $department,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $newData = [];
        try {
            DB::beginTransaction();
            if (isset($request->name)) {
                $newData['name'] = $request->name;
            }
            if (isset($request->description)) {
                $newData['description'] = $request->description;
            }

            DB::commit();

            $department->update($newData);
            return response()->json([
                'status'     => 'Modified successfully',
                'department' => $department,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return response()->json([
                'status' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return response()->json([
            'status' => 'Deleted successfully',
        ]);
    }

    public function departmentRestore(string $id)
    {
        $department = Department::withTrashed()->where('id', $id)->restore();
        return response()->json([
            'status' => 'done',
        ]);
    }

    public function forceDelete(string $id)
    {
        $department = Department::withTrashed()->where('id', $id)->forceDelete();
        return response()->json([
            'status' => 'done',
        ]);
    }

    public function showSoftDeletedDepartments()
    {
        $SoftDeletedDepartments = Department::onlyTrashed()->get();

        return response()->json([
            'soft_deleted_departments' => $SoftDeletedDepartments,
        ]);
    }
}
