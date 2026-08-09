<?php

namespace App\Http\Controllers\Public;

use App\Models\AcademicProgram;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\HeroSlide;
use App\Models\NewsPost;
use App\Models\Notice;
use App\Models\Page;
use App\Models\SchoolSetting;
use Illuminate\Http\JsonResponse;

class HomeController
{
    public function __invoke(): JsonResponse
    {
        $settings = SchoolSetting::query()->first();
        $homeSections = Page::query()->where('template', 'home')->where('status', 'published')->get()->keyBy('slug');

        return response()->json([
            'school' => [
                'name' => $settings?->official_name ?? 'Hamro English Boarding School',
                'shortName' => $settings?->short_name ?? 'HEBS',
                'motto' => $settings?->motto ?? 'Discipline, Knowledge, Character',
                'establishedYear' => $settings?->establishment_year ?? '2058 B.S.',
                'address' => $settings?->address ?? 'Itahari, Sunsari, Nepal',
                'phone' => $settings?->main_phone ?? '+977-25-000000',
                'email' => $settings?->official_email ?? 'info@hamroenglishschool.edu.np',
                'officeHours' => $settings?->office_hours ?? 'Sunday-Friday, 9:30 AM-4:30 PM',
                'appUrl' => $settings?->school_app_url ?? '#',
                'mapUrl' => $settings?->google_map_url ?? 'https://maps.google.com',
                'logoUrl' => $this->mediaUrl($settings?->logo_path) ?? asset('images/hebs-facebook-profile.jpg'),
                'socialLinks' => $settings?->social_links ?? [],
                'copyrightText' => $settings?->copyright_text,
            ],
            'programs' => AcademicProgram::query()->where('is_published', true)->orderBy('display_order')->get()->map(fn (AcademicProgram $program, int $index) => [
                'key' => $program->slug, 'tab' => $program->name, 'step' => 'Step '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'title' => $program->name.($program->grade_or_age_range ? ' ('.$program->grade_or_age_range.')' : ''), 'route' => '/academics/'.$program->slug,
                'image' => $this->mediaUrl($program->featured_image_path) ?? asset('images/school-hero.jpg'), 'description' => $program->short_description,
                'focus' => 'Key Focus Areas', 'items' => $program->learning_objectives ?: $program->curriculum_overview ?: [],
            ])->values(),
            'notices' => Notice::query()->where('status', 'published')->latest('published_at')->take(4)->get()->map(fn (Notice $item) => [
                'title' => $item->title, 'date' => optional($item->published_at)->format('d M Y'), 'category' => $item->category, 'priority' => ucfirst($item->priority), 'href' => '/updates/notices/'.$item->slug,
            ])->values(),
            'news' => NewsPost::query()->where('status', 'published')->latest('published_at')->take(3)->get()->map(fn (NewsPost $item) => [
                'title' => $item->title, 'date' => optional($item->published_at)->format('d M Y'), 'summary' => $item->summary, 'category' => $item->category,
                'image' => $this->mediaUrl($item->featured_image_path) ?? asset('images/school-hero.jpg'), 'href' => '/updates/news/'.$item->slug,
            ])->values(),
            'events' => Event::query()->whereIn('status', ['upcoming', 'ongoing'])->orderBy('starts_at')->take(3)->get()->map(fn (Event $item) => [
                'title' => $item->title, 'date' => $item->starts_at->format('d M Y'), 'time' => $item->starts_at->format('g:i A'), 'venue' => $item->venue, 'href' => '/updates/events/'.$item->slug,
            ])->values(),
            'gallery' => GalleryAlbum::query()->where('is_published', true)->latest('album_date')->take(4)->get()->map(fn (GalleryAlbum $album) => [
                'name' => $album->name,
                'category' => $album->category,
                'description' => $album->description,
                'date' => optional($album->album_date)->format('d M Y'),
                'cover' => $this->mediaUrl($album->cover_image_path),
                'href' => '/gallery',
            ])->values(),
            'heroSlides' => HeroSlide::query()->where('is_active', true)->orderBy('display_order')->get()->map(fn (HeroSlide $slide) => [
                'image' => $this->mediaUrl($slide->image_path), 'kicker' => $slide->kicker, 'title' => $slide->title, 'text' => $slide->text,
            ])->values(),
            'homeSections' => $homeSections->map(fn (Page $page) => [
                'title' => $page->title, 'summary' => $page->summary, 'content' => $page->content, 'image' => $this->mediaUrl($page->image_path), 'sections' => $page->sections ?? [],
            ]),
        ]);
    }

    private function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset('storage/'.$path);
    }
}
