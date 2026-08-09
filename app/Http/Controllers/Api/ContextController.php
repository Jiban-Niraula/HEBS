<?php

namespace App\Http\Controllers\Api;

use App\Models\NavigationItem;
use App\Models\PopupNotice;
use App\Models\SchoolSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContextController
{
    public function __invoke(Request $request): JsonResponse
    {
        $settings = SchoolSetting::query()->first();
        $page = '/'.ltrim((string) $request->query('page', '/'), '/');
        $popup = str_starts_with($page, '/admin') ? null : PopupNotice::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->latest()
            ->get()
            ->first(function (PopupNotice $notice) use ($page): bool {
                return match ($notice->target_scope) {
                    'all_pages' => true,
                    'selected_pages' => in_array($page, $notice->target_pages ?? [], true),
                    default => $page === '/',
                };
            });

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
            'navigation' => NavigationItem::query()->where('is_active', true)->orderBy('menu_group')->orderBy('display_order')->get(),
            'popupNotice' => $popup ? [
                'id' => (string) $popup->id,
                'title' => $popup->title,
                'summary' => $popup->summary,
                'content' => $popup->content,
                'priority' => $popup->priority,
                'buttonLabel' => $popup->button_label,
                'buttonUrl' => $popup->button_url,
                'displayFrequency' => $popup->display_frequency,
                'imageUrl' => $this->mediaUrl($popup->image_path),
                'showCloseButton' => $popup->show_close_button,
                'allowDoNotShowAgain' => $popup->allow_do_not_show_again,
            ] : null,
            'adminUser' => $request->user() ? ['name' => $request->user()->name, 'email' => $request->user()->email, 'role' => $request->user()->role] : null,
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
