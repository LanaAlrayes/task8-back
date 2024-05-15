<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        return response()->json([
            'statue' => 'success',
            'employees' => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'department_id' => $request->department_id,
                'email'         => $request->email,
                'position'      => $request->position,
            ]);

            if ($request->has('project_id')) {
                $employee->projects()->attach($request->project_id);
            }

            DB::commit();

            return response()->json([
                'status' => 'Added to the employee',
                'employee' => $employee,
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error($th);
            return response()->json([
                'status' => 'error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json([
            'status'   => 'success',
            'employee' => $employee
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $newData = [];
        try {
            DB::beginTransaction();
            if (isset($request->email)) {
                $newData['email'] = $request->email;
            }
            if (isset($request->position)) {
                $newData['position'] = $request->position;
            }

            DB::commit();

            $employee->update($newData);
            return response()->json([
                'status'     => 'Modified successfully',
                'employee' => $employee,
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
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json([
            'status' => 'Deleted successfully',
        ]);
    }
}
