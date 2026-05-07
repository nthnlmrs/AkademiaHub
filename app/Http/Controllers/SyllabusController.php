<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SyllabusController extends Controller
{
    public function show(ClassRoom $classroom)
    {
        if (!Auth::user()->can('view', $classroom)) {
            abort(403, 'Unauthorized');
        }

        $classroom->load(['course', 'syllabus']);
        $syllabus = $classroom->syllabus;

        return view('class.syllabus', compact('classroom', 'syllabus'));
    }

    public function store(Request $request, ClassRoom $classroom)
    {
        if (!Auth::user()->can('manage', $classroom)) {
            abort(403, 'Only lecturers of this class can manage the syllabus.');
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $data = ['content' => $validated['content'] ?? null];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Delete old file if exists
            $oldSyllabus = $classroom->syllabus;
            if ($oldSyllabus && $oldSyllabus->file_path) {
                Storage::disk('public')->delete($oldSyllabus->file_path);
            }
            $data['file_path'] = $file->store('syllabi/' . $classroom->id, 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        Syllabus::updateOrCreate(
            ['class_room_id' => $classroom->id],
            $data
        );

        return redirect()->back()->with('success', 'Syllabus saved successfully.');
    }
}
