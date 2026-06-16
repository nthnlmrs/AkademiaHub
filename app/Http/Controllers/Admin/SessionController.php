<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\CourseSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(ClassRoom $classroom)
    {
        $classroom->load(['course', 'course.courseSessions']);
        return view('admin.sessions.index', compact('classroom'));
    }

    public function create(ClassRoom $classroom)
    {
        $classroom->load('course');
        $nextNumber = $classroom->course->courseSessions()->max('session_number') + 1;
        return view('admin.sessions.create', compact('classroom', 'nextNumber'));
    }

    public function store(Request $request, ClassRoom $classroom)
    {
        $validated = $request->validate($this->sessionRules());

        $classroom->load('course');
        $classroom->course->courseSessions()->create($validated);

        return redirect()->route('admin.classrooms.sessions.index', $classroom)
            ->with('success', 'Session created successfully.');
    }

    public function edit(ClassRoom $classroom, CourseSession $session)
    {
        return view('admin.sessions.edit', compact('classroom', 'session'));
    }

    public function update(Request $request, ClassRoom $classroom, CourseSession $session)
    {
        $validated = $request->validate($this->sessionRules());

        $session->update($validated);

        return redirect()->route('admin.classrooms.sessions.index', $classroom)
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(ClassRoom $classroom, CourseSession $session)
    {
        $session->delete();

        return redirect()->route('admin.classrooms.sessions.index', $classroom)
            ->with('success', 'Session deleted successfully.');
    }

    public function storeActivity(Request $request, CourseSession $session)
    {
        $validated = $request->validate([
            'type'     => ['required', 'string', 'in:video,file,link'],
            'title'    => ['required', 'string', 'max:255'],
            'url'      => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip', 'max:20480'],
        ]);

        $data = [
            'type'  => $validated['type'],
            'title' => $validated['title'],
        ];

        if ($validated['type'] === 'file') {
            if ($request->hasFile('document')) {
                $data['file_path'] = $request->file('document')->store('session-documents', 'public');
            }
        } else {
            $url = $validated['url'] ?? null;
            if ($url && !str_starts_with($url, 'http')) {
                $url = 'https://' . $url;
            }
            $data['url'] = $url;
        }

        $session->activities()->create($data);

        return back()->with('success', 'Activity added successfully!');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** Shared validation rules for session store/update. */
    private function sessionRules(): array
    {
        return [
            'session_number' => ['required', 'integer', 'min:1'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'interactive_text' => ['nullable', 'string'],
        ];
    }
}
