<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(ClassRoom $classroom)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403, 'You are not enrolled in this class.');
        }

        $classroom->load('course');
        $posts = ForumPost::where('class_room_id', $classroom->id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'courseSession'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('class.forum.index', compact('classroom', 'posts'));
    }

    public function create(ClassRoom $classroom)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403);
        }

        $classroom->load(['course.courseSessions']);
        $sessions = $classroom->course->courseSessions;

        return view('class.forum.create', compact('classroom', 'sessions'));
    }

    public function store(Request $request, ClassRoom $classroom)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$classroom->users->contains($user->id)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required_without:parent_id', 'nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'course_session_id' => ['required', 'exists:course_sessions,id'],
            'parent_id' => ['nullable', 'exists:forum_posts,id'],
        ]);

        $session = \App\Models\CourseSession::find($validated['course_session_id']);
        if ($session->course_id !== $classroom->course_id) {
            abort(403, 'The specified session does not belong to this class.');
        }

        if (isset($validated['parent_id'])) {
            $parent = ForumPost::find($validated['parent_id']);
            if ($parent->class_room_id !== $classroom->id) {
                abort(403, 'The specified parent post does not belong to this class.');
            }
        }

        $post = ForumPost::create([
            'class_room_id' => $classroom->id,
            'course_session_id' => $validated['course_session_id'],
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'],
        ]);

        if (isset($validated['parent_id'])) {
            return redirect()->back()->with('success', 'Reply posted successfully.');
        }

        return redirect()->route('forum.index', $classroom)->with('success', 'Thread created successfully.');
    }

    public function destroy(ForumPost $post)
    {
        $user = Auth::user();

        // Check if user is admin, owner of the post, or a lecturer of the classroom
        if (!$user->isAdmin() && $post->user_id !== $user->id && !$user->can('manage', $post->classRoom)) {
            abort(403, 'Unauthorized to delete this post.');
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
}
