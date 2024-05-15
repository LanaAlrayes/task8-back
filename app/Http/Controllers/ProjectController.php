<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('employees');
        return response()->json([
            'status' => 'success',
            'projects with employees' => $projects,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            DB::beginTransaction();

            $project = Project::create([
                'name'        => $request->name,
                'description' => $request->description,
            ]);

            if ($request->has('employees_id')) {
                $project->employees()->attach($request->employee_id);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'project' => $project,
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
    public function show(Project $project)
    {
        return response()->json([
            'status'  => 'success',
            'project' => $project
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
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

            $project->update($newData);
            return response()->json([
                'status'     => 'Modified successfully',
                'project'    => $project,
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
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json([
            'status' => 'Deleted successfully',
        ]);
    }
}
