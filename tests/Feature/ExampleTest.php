<?php

namespace Tests\Feature;

use App\Models\NavigationItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_admin_dashboard_route_is_available(): void
    {
        $this->post('/admin/login', ['email' => 'admin@hebs.edu.np', 'password' => 'change-me-now']);
        $response = $this->get('/admin');

        $response->assertStatus(200);
    }

    public function test_the_main_public_destinations_are_available(): void
    {
        foreach (['/our-school/about', '/our-school/teachers', '/our-school/administration', '/our-school/support-team', '/our-school/executives-message', '/our-school/careers', '/academics', '/academics/montessori', '/academics/pre-primary', '/academics/primary', '/academics/secondary', '/academics/grade-11-12', '/academics/fee-structure', '/admissions/information', '/admissions/scholarships', '/school-life/facilities', '/updates/notices', '/updates/news', '/updates/events', '/gallery', '/downloads', '/contact'] as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_navigation_content_endpoints_are_available(): void
    {
        foreach (['about', 'administration', 'support-team', 'executives-message', 'careers', 'scholarships', 'fee-structure', 'privacy', 'terms'] as $slug) {
            $this->getJson("/api/v1/public/information/{$slug}")
                ->assertOk()
                ->assertJsonStructure(['page' => ['eyebrow', 'title', 'summary', 'sections']]);
        }

        $this->getJson('/api/v1/public/people/staff')->assertOk();

        foreach (['montessori', 'pre-primary', 'primary', 'secondary', 'grade-11-12'] as $slug) {
            $this->getJson("/api/v1/public/programs/{$slug}")
                ->assertOk()
                ->assertJsonPath('program.slug', $slug);
        }
    }

    public function test_admin_can_authenticate_and_load_the_dashboard_api(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@hebs.edu.np',
            'password' => 'change-me-now',
        ])->assertOk();

        $this->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonStructure(['metrics', 'modules'])
            ->assertJsonMissing(['href' => '#']);
    }

    public function test_admin_can_load_every_content_management_module(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@hebs.edu.np',
            'password' => 'change-me-now',
        ])->assertOk();

        foreach (['settings', 'hero-slides', 'navigation', 'pages', 'programs', 'people', 'notices', 'news', 'events', 'facilities', 'gallery', 'documents', 'popups'] as $resource) {
            $this->getJson("/api/v1/admin/content/{$resource}")
                ->assertOk()
                ->assertJsonStructure(['resource', 'definition' => ['title', 'fields'], 'records']);
        }
    }

    public function test_primary_navigation_is_locked_and_secondary_links_use_a_primary_parent(): void
    {
        $this->postJson('/api/v1/auth/login', ['email' => 'admin@hebs.edu.np', 'password' => 'change-me-now'])->assertOk();

        $primary = NavigationItem::query()->where('is_primary', true)->orderBy('display_order')->get();
        $this->assertSame(['Home', 'About Us', 'Resources', 'Updates', 'Academic', 'Levels', 'Contact'], $primary->pluck('label')->all());

        $home = $primary->firstWhere('label', 'Home');
        $this->patchJson('/api/v1/admin/content/navigation/'.$home->id, [
            'menu_group' => 'header', 'parent_label' => 'About Us', 'label' => 'Changed', 'href' => '/changed',
            'icon' => 'ph-house', 'display_order' => 0, 'is_active' => true,
        ])->assertStatus(422);
        $this->deleteJson('/api/v1/admin/content/navigation/'.$home->id)->assertStatus(422);

        $definition = $this->getJson('/api/v1/admin/content/navigation')->assertOk();
        $under = collect($definition->json('definition.fields'))->firstWhere('name', 'parent_label');
        $this->assertSame('select', $under['type']);
        $this->assertSame('Resources', $under['options']['Resources']);

        $created = $this->postJson('/api/v1/admin/content/navigation', [
            'menu_group' => 'header', 'parent_label' => 'Resources', 'label' => 'Library test', 'href' => '/downloads',
            'icon' => 'ph-books', 'display_order' => 90, 'is_active' => true,
        ])->assertCreated()->assertJsonPath('record.is_primary', false);

        $this->getJson('/api/v1/context?page=%2F')->assertOk()->assertJsonFragment([
            'label' => 'Library test', 'parent_label' => 'Resources', 'is_primary' => false,
        ]);

        $this->deleteJson('/api/v1/admin/content/navigation/'.$created->json('record.id'))->assertOk();
    }

    public function test_admin_page_edit_is_immediately_visible_on_the_public_home_api(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@hebs.edu.np',
            'password' => 'change-me-now',
        ])->assertOk();

        $page = Page::where('slug', 'home-about')->firstOrFail();
        $updatedTitle = 'A dynamic school profile';

        $this->patchJson("/api/v1/admin/content/pages/{$page->id}", [
            'title' => $updatedTitle,
            'slug' => $page->slug,
            'template' => $page->template,
            'eyebrow' => $page->eyebrow,
            'summary' => $page->summary,
            'content' => $page->content,
            'sections' => $page->sections,
            'status' => $page->status,
            'published_at' => optional($page->published_at)->toDateTimeString(),
        ])->assertOk();

        $this->getJson('/api/v1/public/home')
            ->assertOk()
            ->assertJsonPath('homeSections.home-about.title', $updatedTitle);
    }

    public function test_admin_can_create_and_publish_a_popup_ad(): void
    {
        $this->postJson('/api/v1/auth/login', ['email' => 'admin@hebs.edu.np', 'password' => 'change-me-now'])->assertOk();

        $response = $this->postJson('/api/v1/admin/content/popups', [
            'title' => 'Admission week',
            'slug' => 'admission-week-test',
            'summary' => 'Admissions are open this week.',
            'content' => 'Contact the school office for an appointment.',
            'notice_type' => 'admission',
            'priority' => 'important',
            'display_frequency' => 'every_visit',
            'target_scope' => 'homepage',
            'target_pages' => [],
            'show_close_button' => true,
            'allow_do_not_show_again' => true,
            'is_active' => true,
        ])->assertCreated();

        $this->getJson('/api/v1/context?page=%2F')
            ->assertOk()
            ->assertJsonPath('popupNotice.title', 'Admission week');

        $this->getJson('/api/v1/context?page=%2Fupdates%2Fnews')
            ->assertOk()
            ->assertJsonPath('popupNotice', null);

        $this->deleteJson('/api/v1/admin/content/popups/'.$response->json('record.id'))->assertOk();
    }

    public function test_admin_can_publish_a_career_opening(): void
    {
        $this->postJson('/api/v1/auth/login', ['email' => 'admin@hebs.edu.np', 'password' => 'change-me-now'])->assertOk();

        $response = $this->postJson('/api/v1/admin/content/careers', [
            'title' => 'Primary Teacher',
            'slug' => 'primary-teacher-test',
            'department' => 'Primary School',
            'job_type' => 'full_time',
            'location' => 'Itahari - 04, Sunsari',
            'summary' => 'Join the primary teaching team.',
            'description' => 'Plan and deliver purposeful classroom learning.',
            'requirements' => ['Bachelor degree', 'Teaching experience'],
            'application_instructions' => 'Contact the administration office.',
            'application_deadline' => now()->addMonth()->toDateString(),
            'status' => 'published',
            'published_at' => now()->toDateTimeString(),
            'is_featured' => true,
        ])->assertCreated();

        $this->getJson('/api/v1/public/careers')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Primary Teacher']);

        $this->deleteJson('/api/v1/admin/content/careers/'.$response->json('record.id'))->assertOk();
    }

    public function test_administrator_can_manage_users_but_cannot_delete_self(): void
    {
        $this->postJson('/api/v1/auth/login', ['email' => 'admin@hebs.edu.np', 'password' => 'change-me-now'])->assertOk();

        $response = $this->postJson('/api/v1/admin/users', [
            'name' => 'Content Editor',
            'email' => 'editor-test@hebs.edu.np',
            'role' => 'editor',
            'password' => 'editor-password',
            'is_active' => true,
        ])->assertCreated();

        $userId = $response->json('user.id');
        $this->patchJson('/api/v1/admin/users/'.$userId, [
            'name' => 'Website Editor',
            'email' => 'editor-test@hebs.edu.np',
            'role' => 'editor',
            'password' => '',
            'is_active' => false,
        ])->assertOk()->assertJsonPath('user.is_active', false);

        $this->deleteJson('/api/v1/admin/users/'.$userId)->assertOk();
        $this->deleteJson('/api/v1/admin/users/1')->assertStatus(422);
    }

    public function test_an_admission_enquiry_can_be_submitted(): void
    {
        $this->post('/admission-enquiries', [
            'student_name' => 'Test Student',
            'guardian_name' => 'Test Guardian',
            'telephone' => '9800000000',
            'desired_program' => 'Primary',
            'preferred_contact_method' => 'phone',
            'privacy_consent' => '1',
        ])->assertSessionHas('success');
    }
}
