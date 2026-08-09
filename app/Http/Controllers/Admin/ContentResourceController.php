<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicProgram;
use App\Models\CareerOpening;
use App\Models\Document;
use App\Models\Event;
use App\Models\Facility;
use App\Models\GalleryAlbum;
use App\Models\HeroSlide;
use App\Models\NavigationItem;
use App\Models\NewsPost;
use App\Models\Notice;
use App\Models\Page;
use App\Models\Person;
use App\Models\PopupNotice;
use App\Models\SchoolSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContentResourceController extends Controller
{
    public function index(string $resource): JsonResponse
    {
        $definition = $this->definition($resource);
        $query = $definition['model']::query();

        foreach ($definition['order'] ?? ['id', 'desc'] as $order) {
            $query->orderBy($order[0], $order[1]);
        }

        return response()->json([
            'resource' => $resource,
            'definition' => $this->publicDefinition($definition),
            'records' => $query->get(),
        ]);
    }

    public function store(Request $request, string $resource): JsonResponse
    {
        $definition = $this->definition($resource);
        $data = $this->validatedData($request, $resource, $definition);
        if ($resource === 'navigation') {
            $data['is_primary'] = false;
        }
        $record = $definition['model']::create($data);

        return response()->json(['message' => $definition['singular'].' created.', 'record' => $record], 201);
    }

    public function update(Request $request, string $resource, int $id): JsonResponse
    {
        $definition = $this->definition($resource);
        $record = $definition['model']::query()->findOrFail($id);
        abort_if($resource === 'navigation' && $record->is_primary, 422, 'Primary navigation items are locked.');
        $data = $this->validatedData($request, $resource, $definition, $record);
        $record->update($data);

        return response()->json(['message' => $definition['singular'].' updated.', 'record' => $record->fresh()]);
    }

    public function destroy(string $resource, int $id): JsonResponse
    {
        $definition = $this->definition($resource);
        abort_if(($definition['singleton'] ?? false), 422, 'This record cannot be deleted.');
        $record = $definition['model']::query()->findOrFail($id);
        abort_if($resource === 'navigation' && $record->is_primary, 422, 'Primary navigation items are locked.');

        foreach ($definition['fields'] as $name => $field) {
            if (in_array($field['type'], ['image', 'file'], true)) {
                $this->deleteStoredFile($record->{$name});
            }
        }

        $record->delete();

        return response()->json(['message' => $definition['singular'].' deleted.']);
    }

    private function validatedData(Request $request, string $resource, array $definition, ?Model $record = null): array
    {
        if ($record) {
            $request->merge(['id' => $record->getKey()]);
        }
        $rules = [];
        foreach ($definition['fields'] as $name => $field) {
            $fieldRules = $field['rules'];
            if (($field['unique'] ?? false) === true) {
                $fieldRules[] = Rule::unique($record?->getTable() ?? (new $definition['model'])->getTable(), $name)->ignore($record?->getKey());
            }
            $rules[$name] = $fieldRules;
        }

        $data = $request->validate($rules);

        foreach ($definition['fields'] as $name => $field) {
            if (in_array($field['type'], ['lines', 'sections', 'social'], true) && array_key_exists($name, $data) && is_string($data[$name])) {
                $data[$name] = json_decode($data[$name], true, 512, JSON_THROW_ON_ERROR);
            }

            if ($field['type'] === 'checkbox') {
                $data[$name] = $request->boolean($name);
            }

            if (in_array($field['type'], ['image', 'file'], true)) {
                if ($request->hasFile($name)) {
                    $this->deleteStoredFile($record?->{$name});
                    $data[$name] = $request->file($name)->store('cms/'.$resource, 'public');
                } else {
                    unset($data[$name]);
                }
            }
        }

        return $data;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function publicDefinition(array $definition): array
    {
        return [
            'title' => $definition['title'],
            'singular' => $definition['singular'],
            'description' => $definition['description'],
            'titleField' => $definition['titleField'],
            'statusField' => $definition['statusField'] ?? null,
            'lockedField' => $definition['lockedField'] ?? null,
            'singleton' => $definition['singleton'] ?? false,
            'fields' => collect($definition['fields'])->map(fn (array $field, string $name) => [
                'name' => $name,
                'label' => $field['label'],
                'type' => $field['type'],
                'options' => $field['options'] ?? [],
                'help' => $field['help'] ?? null,
                'required' => in_array('required', $field['rules'], true),
            ])->values(),
        ];
    }

    private function definition(string $resource): array
    {
        $text = fn (string $label, bool $required = false, int $max = 255, array $extra = []) => ['label' => $label, 'type' => 'text', 'rules' => [$required ? 'required' : 'nullable', 'string', 'max:'.$max], ...$extra];
        $textarea = fn (string $label, bool $required = false, array $extra = []) => ['label' => $label, 'type' => 'textarea', 'rules' => [$required ? 'required' : 'nullable', 'string'], ...$extra];
        $number = fn (string $label) => ['label' => $label, 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']];
        $checkbox = fn (string $label) => ['label' => $label, 'type' => 'checkbox', 'rules' => ['boolean']];
        $select = fn (string $label, array $options, bool $required = true) => ['label' => $label, 'type' => 'select', 'options' => $options, 'rules' => [$required ? 'required' : 'nullable', Rule::in(array_keys($options))]];
        $date = fn (string $label, string $type = 'datetime-local', bool $required = false) => ['label' => $label, 'type' => $type, 'rules' => [$required ? 'required' : 'nullable', 'date']];
        $image = fn (string $label, bool $required = false) => ['label' => $label, 'type' => 'image', 'rules' => [$required ? 'required_without:id' : 'nullable', 'image', 'max:5120']];
        $file = fn (string $label) => ['label' => $label, 'type' => 'file', 'rules' => ['nullable', 'file', 'max:15360']];
        $lines = fn (string $label, string $help = '') => ['label' => $label, 'type' => 'lines', 'help' => $help, 'rules' => ['nullable']];

        $definitions = [
            'settings' => [
                'model' => SchoolSetting::class, 'title' => 'School identity', 'singular' => 'School profile', 'titleField' => 'official_name', 'singleton' => true,
                'description' => 'Control the school name, logo, contacts, address, map, app, social links, and footer identity.',
                'fields' => [
                    'official_name' => $text('Official school name', true), 'short_name' => $text('Short name'), 'local_name' => $text('Local name'),
                    'motto' => $text('Motto'), 'establishment_year' => $text('Established year'), 'logo_path' => $image('School logo'), 'favicon_path' => $image('Favicon'),
                    'address' => $text('Address'), 'main_phone' => $text('Main phone'), 'alternate_phone' => $text('Alternate phone'),
                    'official_email' => ['label' => 'Official email', 'type' => 'email', 'rules' => ['required', 'email', 'max:255']],
                    'admission_email' => ['label' => 'Admission email', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:255']],
                    'office_hours' => $text('Office hours'), 'google_map_url' => $textarea('Google map embed URL'), 'school_app_url' => $text('School app URL'),
                    'social_links' => ['label' => 'Social profiles', 'type' => 'social', 'rules' => ['nullable']], 'copyright_text' => $text('Copyright text'),
                ],
                'order' => [['id', 'asc']],
            ],
            'hero-slides' => [
                'model' => HeroSlide::class, 'title' => 'Hero carousel', 'singular' => 'Hero slide', 'titleField' => 'title', 'statusField' => 'is_active',
                'description' => 'Manage every homepage carousel image and its message.',
                'fields' => ['kicker' => $text('Kicker'), 'title' => $text('Headline', true), 'text' => $textarea('Supporting text'), 'image_path' => $image('Carousel image', true), 'display_order' => $number('Display order'), 'is_active' => $checkbox('Active')],
                'order' => [['display_order', 'asc'], ['id', 'asc']],
            ],
            'programs' => [
                'model' => AcademicProgram::class, 'title' => 'Academic programs & syllabus', 'singular' => 'Academic program', 'titleField' => 'name', 'statusField' => 'is_published',
                'description' => 'Manage every academic level, its homepage tab, syllabus, teaching approach, assessment, and supporting image.',
                'fields' => [
                    'name' => $text('Program name', true), 'slug' => $text('URL slug', true, 120, ['unique' => true]), 'grade_or_age_range' => $text('Grade or age range', true),
                    'short_description' => $textarea('Homepage description', true), 'full_introduction' => $textarea('Full introduction'),
                    'learning_objectives' => $lines('Key focus areas', 'One item per line.'), 'curriculum_overview' => $lines('Curriculum / syllabus', 'One subject or syllabus item per line.'),
                    'teaching_approach' => $textarea('Teaching approach'), 'assessment_approach' => $textarea('Assessment approach'), 'student_support' => $textarea('Student support'),
                    'activities' => $lines('Activities', 'One activity per line.'), 'featured_image_path' => $image('Featured image'), 'display_order' => $number('Display order'), 'is_published' => $checkbox('Published'),
                ],
                'order' => [['display_order', 'asc'], ['id', 'asc']],
            ],
            'pages' => [
                'model' => Page::class, 'title' => 'Website pages & sections', 'singular' => 'Page', 'titleField' => 'title', 'statusField' => 'status',
                'description' => 'Edit institutional, admissions, legal, student-life, and homepage section copy.',
                'fields' => [
                    'title' => $text('Page title', true), 'slug' => $text('Page key / slug', true, 150, ['unique' => true]), 'template' => $select('Template', ['standard' => 'Standard page', 'home' => 'Homepage section', 'listing' => 'Listing page']), 'eyebrow' => $text('Eyebrow label'),
                    'summary' => $textarea('Summary'), 'content' => $textarea('Main content'), 'image_path' => $image('Section image'),
                    'sections' => ['label' => 'Content sections', 'type' => 'sections', 'help' => 'Add titled content blocks in their display order.', 'rules' => ['nullable']],
                    'status' => $select('Status', ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']), 'published_at' => $date('Publish date'),
                ],
                'order' => [['title', 'asc']],
            ],
            'people' => [
                'model' => Person::class, 'title' => 'People directory', 'singular' => 'Person', 'titleField' => 'name', 'statusField' => 'is_published',
                'description' => 'Manage leadership, committee members, teachers, administration, and support staff.',
                'fields' => ['name' => $text('Full name', true), 'group_name' => $select('Directory group', ['leadership' => 'Leadership', 'committee' => 'Management committee', 'staff' => 'Teachers and staff']), 'position' => $text('Position'), 'department' => $text('Department'), 'biography' => $textarea('Biography'), 'photo_path' => $image('Profile photo'), 'display_order' => $number('Display order'), 'is_published' => $checkbox('Published')],
                'order' => [['group_name', 'asc'], ['display_order', 'asc']],
            ],
            'notices' => [
                'model' => Notice::class, 'title' => 'Notice board', 'singular' => 'Notice', 'titleField' => 'title', 'statusField' => 'status',
                'description' => 'Create and publish official notices, attachments, homepage notices, and announcements.',
                'fields' => [
                    'title' => $text('Title', true), 'slug' => $text('URL slug', true, 180, ['unique' => true]), 'notice_number' => $text('Notice number'), 'category' => $text('Category', true, 80),
                    'summary' => $textarea('Summary'), 'content' => $textarea('Full notice', true), 'featured_image_path' => $image('Featured image'), 'attachment_path' => $file('Attachment'),
                    'published_at' => $date('Published at'), 'starts_at' => $date('Visible from'), 'expires_at' => $date('Expires at'),
                    'priority' => $select('Priority', ['normal' => 'Normal', 'important' => 'Important', 'urgent' => 'Urgent', 'emergency' => 'Emergency']),
                    'status' => $select('Status', ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']), 'is_pinned' => $checkbox('Pin notice'),
                    'show_on_homepage' => $checkbox('Show on homepage'), 'show_in_announcement' => $checkbox('Show in announcement bar'), 'show_as_popup' => $checkbox('Show as popup'), 'target_audience' => $text('Target audience'),
                ],
                'order' => [['published_at', 'desc'], ['id', 'desc']],
            ],
            'news' => [
                'model' => NewsPost::class, 'title' => 'News & articles', 'singular' => 'News post', 'titleField' => 'title', 'statusField' => 'status',
                'description' => 'Publish school news, achievements, announcements, and editorial articles.',
                'fields' => ['title' => $text('Title', true), 'slug' => $text('URL slug', true, 180, ['unique' => true]), 'category' => $text('Category'), 'summary' => $textarea('Summary'), 'content' => $textarea('Article content', true), 'featured_image_path' => $image('Featured image'), 'published_at' => $date('Published at'), 'show_on_homepage' => $checkbox('Show on homepage'), 'status' => $select('Status', ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])],
                'order' => [['published_at', 'desc'], ['id', 'desc']],
            ],
            'events' => [
                'model' => Event::class, 'title' => 'Events calendar', 'singular' => 'Event', 'titleField' => 'title', 'statusField' => 'status',
                'description' => 'Manage event dates, venues, descriptions, and homepage visibility.',
                'fields' => ['title' => $text('Title', true), 'slug' => $text('URL slug', true, 180, ['unique' => true]), 'description' => $textarea('Description'), 'starts_at' => $date('Starts at', 'datetime-local', true), 'ends_at' => $date('Ends at'), 'venue' => $text('Venue'), 'map_url' => $text('Map URL'), 'status' => $select('Status', ['upcoming' => 'Upcoming', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'cancelled' => 'Cancelled']), 'show_on_homepage' => $checkbox('Show on homepage')],
                'order' => [['starts_at', 'desc']],
            ],
            'facilities' => [
                'model' => Facility::class, 'title' => 'Facilities', 'singular' => 'Facility', 'titleField' => 'name', 'statusField' => 'is_published',
                'description' => 'Manage campus facilities, categories, photos, and descriptions.',
                'fields' => ['name' => $text('Facility name', true), 'category' => $text('Category'), 'description' => $textarea('Description'), 'image_path' => $image('Facility image'), 'display_order' => $number('Display order'), 'is_published' => $checkbox('Published')],
                'order' => [['display_order', 'asc']],
            ],
            'gallery' => [
                'model' => GalleryAlbum::class, 'title' => 'Gallery albums', 'singular' => 'Gallery album', 'titleField' => 'name', 'statusField' => 'is_published',
                'description' => 'Create albums and manage their cover image, category, date, and public visibility.',
                'fields' => ['name' => $text('Album name', true), 'slug' => $text('URL slug', true, 180, ['unique' => true]), 'description' => $textarea('Description'), 'cover_image_path' => $image('Cover image'), 'album_date' => $date('Album date', 'date'), 'category' => $text('Category'), 'is_published' => $checkbox('Published')],
                'order' => [['album_date', 'desc'], ['id', 'desc']],
            ],
            'documents' => [
                'model' => Document::class, 'title' => 'Downloads & syllabuses', 'singular' => 'Document', 'titleField' => 'title', 'statusField' => 'status',
                'description' => 'Upload syllabus files, forms, calendars, policies, routines, and public downloads.',
                'fields' => ['title' => $text('Document title', true), 'category' => $text('Category', true), 'description' => $textarea('Description'), 'file_path' => $file('Document file'), 'file_type' => $text('File type'), 'published_at' => $date('Published at'), 'expires_at' => $date('Expires at'), 'status' => $select('Status', ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])],
                'order' => [['published_at', 'desc'], ['id', 'desc']],
            ],
            'popups' => [
                'model' => PopupNotice::class, 'title' => 'Popup notices', 'singular' => 'Popup notice', 'titleField' => 'title', 'statusField' => 'is_active',
                'description' => 'Control targeted site alerts, campaign messages, timing, frequency, imagery, and action buttons.',
                'fields' => ['title' => $text('Title', true), 'slug' => $text('Unique key', true, 180, ['unique' => true]), 'summary' => $textarea('Summary'), 'content' => $textarea('Content'), 'notice_type' => $select('Popup type', ['general' => 'General', 'admission' => 'Admission', 'event' => 'Event', 'emergency' => 'Emergency']), 'priority' => $select('Priority', ['normal' => 'Normal', 'important' => 'Important', 'urgent' => 'Urgent', 'emergency' => 'Emergency']), 'image_path' => $image('Popup image'), 'attachment_path' => $file('Attachment'), 'button_label' => $text('Button label'), 'button_url' => $text('Button URL'), 'display_frequency' => $select('Display frequency', ['every_visit' => 'Every visit', 'once_per_session' => 'Once per session', 'once_per_day' => 'Once per day', 'once_per_notice' => 'Once per notice']), 'target_scope' => $select('Show on', ['homepage' => 'Homepage', 'all_pages' => 'All pages', 'selected_pages' => 'Selected pages']), 'target_pages' => $lines('Selected page paths', 'One URL path per line. Used only when Show on is Selected pages.'), 'starts_at' => $date('Starts at'), 'ends_at' => $date('Ends at'), 'show_close_button' => $checkbox('Show close button'), 'allow_do_not_show_again' => $checkbox('Allow do not show again'), 'is_active' => $checkbox('Active')],
                'order' => [['id', 'desc']],
            ],
            'careers' => [
                'model' => CareerOpening::class, 'title' => 'Careers & vacancies', 'singular' => 'Career opening', 'titleField' => 'title', 'statusField' => 'status',
                'description' => 'Publish approved teaching, administration, and support vacancies with requirements and deadlines.',
                'fields' => ['title' => $text('Position title', true), 'slug' => $text('URL slug', true, 180, ['unique' => true]), 'department' => $text('Department'), 'job_type' => $select('Employment type', ['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'internship' => 'Internship']), 'location' => $text('Location'), 'summary' => $textarea('Short summary'), 'description' => $textarea('Role description', true), 'requirements' => $lines('Requirements', 'One requirement per line.'), 'application_instructions' => $textarea('Application instructions'), 'application_deadline' => $date('Application deadline', 'date'), 'status' => $select('Status', ['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed']), 'published_at' => $date('Published at'), 'is_featured' => $checkbox('Feature this vacancy')],
                'order' => [['is_featured', 'desc'], ['application_deadline', 'desc'], ['id', 'desc']],
            ],
            'navigation' => [
                'model' => NavigationItem::class, 'title' => 'Navigation & footer links', 'singular' => 'Navigation item', 'titleField' => 'label', 'statusField' => 'is_active', 'lockedField' => 'is_primary',
                'description' => 'Primary navigation is protected. Create secondary links beneath a primary item, or manage quick actions and footer links.',
                'fields' => ['menu_group' => $select('Menu location', ['header' => 'Header dropdown link', 'quick_action' => 'Homepage quick actions', 'footer_institution' => 'Footer: Institution', 'footer_academics' => 'Footer: Academics']), 'parent_label' => ['label' => 'Under:', 'type' => 'select', 'options' => ['' => 'Select primary navigation', 'Home' => 'Home', 'About Us' => 'About Us', 'Resources' => 'Resources', 'Updates' => 'Updates', 'Academic' => 'Academic', 'Levels' => 'Levels', 'Contact' => 'Contact'], 'help' => 'Required for header dropdown links.', 'rules' => ['nullable', 'required_if:menu_group,header', Rule::in(['Home', 'About Us', 'Resources', 'Updates', 'Academic', 'Levels', 'Contact'])]], 'label' => $text('Link label', true), 'href' => $text('Destination', true), 'icon' => $text('Phosphor icon class'), 'display_order' => $number('Display order'), 'is_active' => $checkbox('Active')],
                'order' => [['menu_group', 'asc'], ['is_primary', 'desc'], ['display_order', 'asc']],
            ],
        ];

        abort_unless(isset($definitions[$resource]), 404);

        return $definitions[$resource];
    }
}
