<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogLike;
use App\Models\Patient;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('is_published', true)
            ->withCount('comments')
            ->orderByDesc('published_at')
            ->paginate(6);

        $patientId = Auth::guard('patient')->id();
        $likedIds = $patientId
            ? BlogLike::where('patient_id', $patientId)->pluck('blog_id')->all()
            : [];

        return view('WebSite.blog.index', compact('blogs', 'likedIds'));
    }

    public function show(Blog $blog)
    {
        abort_unless($blog->is_published, 404);

        $blog->increment('views');

        $related = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $siteSetting = SiteSetting::current();
        $patientId = Auth::guard('patient')->id();
        $liked = $blog->isLikedByPatient($patientId);
        $comments = $blog->comments()->with('patient.translations')->latest()->get();

        return view('WebSite.blog.show', compact('blog', 'related', 'siteSetting', 'liked', 'comments'));
    }

    public function like(Request $request, Blog $blog)
    {
        abort_unless($blog->is_published, 404);

        if (!Auth::guard('patient')->check()) {
            return response()->json([
                'ok' => false,
                'auth_required' => true,
                'message' => 'يلزم تسجيل الدخول كمريض للإعجاب بالمقال',
            ], 401);
        }

        $patientId = Auth::guard('patient')->id();
        $existing = BlogLike::where('blog_id', $blog->id)->where('patient_id', $patientId)->first();

        if ($existing) {
            $existing->delete();
            $blog->decrement('likes');
            return response()->json([
                'ok' => true,
                'liked' => false,
                'likes' => max(0, (int) $blog->fresh()->likes),
                'message' => 'تم إلغاء الإعجاب',
            ]);
        }

        BlogLike::create([
            'blog_id' => $blog->id,
            'patient_id' => $patientId,
        ]);
        $blog->increment('likes');

        return response()->json([
            'ok' => true,
            'liked' => true,
            'likes' => (int) $blog->fresh()->likes,
            'message' => 'تم تسجيل إعجابك',
        ]);
    }

    public function comment(Request $request, Blog $blog)
    {
        abort_unless($blog->is_published, 404);

        if (!Auth::guard('patient')->check()) {
            return response()->json([
                'ok' => false,
                'auth_required' => true,
                'message' => 'يلزم تسجيل الدخول كمريض لإضافة تعليق',
            ], 401);
        }

        try {
            $data = $request->validate([
                'body' => 'required|string|min:2|max:1000',
            ], [
                'body.required' => 'اكتب نص التعليق أولاً.',
                'body.min' => 'التعليق قصير جداً (حرفان على الأقل).',
                'body.max' => 'التعليق طويل جداً.',
            ]);

            $patient = Auth::guard('patient')->user();

            $comment = BlogComment::create([
                'blog_id' => $blog->id,
                'patient_id' => $patient->id,
                'body' => trim($data['body']),
            ]);

            $author = 'مريض';
            try {
                $author = $patient->name ?: ($patient->email ?: 'مريض');
            } catch (\Throwable $e) {
                $author = $patient->email ?: 'مريض';
            }

            return response()->json([
                'ok' => true,
                'success' => true,
                'message' => 'تم إضافة تعليقك',
                'comment' => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'author' => (string) $author,
                    'initial' => mb_substr((string) $author, 0, 1, 'UTF-8'),
                    'avatar' => asset('Dashboard/img/brand/hospital-logo.png'),
                    'date' => optional($comment->created_at)->diffForHumans() ?: now()->diffForHumans(),
                ],
                'comments_count' => (int) $blog->comments()->count(),
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->values()->all();

            return response()->json([
                'ok' => false,
                'message' => $messages[0] ?? 'بيانات التعليق غير صحيحة.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'تعذر إضافة التعليق: ' . $e->getMessage(),
            ], 500);
        }
    }
}