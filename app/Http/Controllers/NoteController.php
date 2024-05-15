<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::all();
        return response()->json([
            'status' => 'success',
            'notes'  => $notes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function departmentStore(StoreNoteRequest $request, Department $department)
    {
        try {
            DB::beginTransaction();

            $note = $department->notes()->create([
                'title' => $request->title,
                'body'  => $request->body,
            ]);
            DB::commit();

            return response()->json([
                'status' => 'Added to the note',
                'note'   => $note,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);

            return response()->json([
                'status' => 'error',
            ], 500);
        }
    }

    public function employeeStore(StoreNoteRequest $request, Employee $employee)
    {
        try {
            DB::beginTransaction();

            $note = $employee->notes()->create([
                'title' => $request->title,
                'body'  => $request->body,
            ]);
            DB::commit();

            return response()->json([
                'status' => 'Added to the note',
                'note'   => $note,
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
    public function show(Note $note)
    {
        return response()->json([
            'status' => 'success',
            'note'   => $note,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $newData = [];
        try {
            DB::beginTransaction();
            if (isset($request->title)) {
                $newData['title'] = $request->title;
            }
            if (isset($request->body)) {
                $newData['body'] = $request->body;
            }

            DB::commit();

            $note->update($newData);
            return response()->json([
                'status' => 'Modified successfully',
                'note'   => $note,
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
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json([
            'status' => 'Deleted successfully',
        ]);
    }
}
