<?php

namespace App\Http\Controllers\Public;

use App\Models\AcademicProgram;
use App\Models\CareerOpening;
use App\Models\Document;
use App\Models\Event;
use App\Models\Facility;
use App\Models\GalleryAlbum;
use App\Models\NewsPost;
use App\Models\Notice;
use App\Models\Page;
use App\Models\Person;
use Illuminate\Http\JsonResponse;

class SitePageController
{
    public function information(string $slug): JsonResponse
    {
        $page = Page::query()->where('slug', $slug)->where('status', 'published')->firstOrFail();

        return response()->json([
            'page' => [
                'eyebrow' => $page->eyebrow ?: 'Hamro English Boarding School',
                'title' => $page->title,
                'summary' => $page->summary,
                'content' => $page->content,
                'image' => $page->image_path,
                'sections' => $page->sections ?? [],
            ],
            'slug' => $slug,
        ]);
    }

    public function programs(): JsonResponse
    {
        return response()->json([
            'programs' => AcademicProgram::query()->where('is_published', true)->orderBy('display_order')->get(),
        ]);
    }

    public function program(string $slug): JsonResponse
    {
        $program = AcademicProgram::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();

        return response()->json(['program' => $program]);
    }

    public function people(string $group): JsonResponse
    {
        $labels = ['leadership' => 'Leadership', 'committee' => 'Management Committee', 'staff' => 'Teachers and Staff'];
        abort_unless(isset($labels[$group]), 404);

        return response()->json([
            'title' => $labels[$group],
            'people' => Person::query()->where('group_name', $group)->where('is_published', true)->orderBy('display_order')->get(),
        ]);
    }

    public function facilities(): JsonResponse
    {
        return response()->json(['facilities' => Facility::query()->where('is_published', true)->orderBy('display_order')->get()]);
    }

    public function notices(): JsonResponse
    {
        return response()->json(['notices' => Notice::query()->where('status', 'published')->latest('published_at')->paginate(10)->withQueryString()]);
    }

    public function notice(string $slug): JsonResponse
    {
        return response()->json(['notice' => Notice::query()->where('slug', $slug)->where('status', 'published')->firstOrFail()]);
    }

    public function news(): JsonResponse
    {
        return response()->json(['posts' => NewsPost::query()->where('status', 'published')->latest('published_at')->paginate(10)->withQueryString()]);
    }

    public function newsPost(string $slug): JsonResponse
    {
        return response()->json(['post' => NewsPost::query()->where('slug', $slug)->where('status', 'published')->firstOrFail()]);
    }

    public function events(): JsonResponse
    {
        return response()->json(['events' => Event::query()->whereIn('status', ['upcoming', 'ongoing'])->orderBy('starts_at')->paginate(10)->withQueryString()]);
    }

    public function event(string $slug): JsonResponse
    {
        return response()->json(['event' => Event::query()->where('slug', $slug)->firstOrFail()]);
    }

    public function gallery(): JsonResponse
    {
        return response()->json(['albums' => GalleryAlbum::query()->where('is_published', true)->latest('album_date')->get()]);
    }

    public function downloads(): JsonResponse
    {
        return response()->json(['documents' => Document::query()->where('status', 'published')->latest('published_at')->get()]);
    }

    public function contact(): JsonResponse
    {
        return response()->json([]);
    }

    public function careers(): JsonResponse
    {
        $page = Page::query()->where('slug', 'careers')->where('status', 'published')->first();

        return response()->json([
            'page' => $page ? ['eyebrow' => $page->eyebrow ?: 'Our School', 'title' => $page->title, 'summary' => $page->summary, 'content' => $page->content] : null,
            'openings' => CareerOpening::query()
                ->where('status', 'published')
                ->where(fn ($query) => $query->whereNull('application_deadline')->orWhereDate('application_deadline', '>=', today()))
                ->orderByDesc('is_featured')
                ->orderBy('application_deadline')
                ->get(),
        ]);
    }
}
