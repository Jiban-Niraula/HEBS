<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicProgram;
use App\Models\AdmissionEnquiry;
use App\Models\CareerOpening;
use App\Models\Notice;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'metrics' => [
                ['label' => 'Published notices', 'value' => Notice::where('status', 'published')->count()],
                ['label' => 'New admission enquiries', 'value' => AdmissionEnquiry::where('status', 'new')->count()],
                ['label' => 'Published programs', 'value' => AcademicProgram::where('is_published', true)->count()],
                ['label' => 'Open career positions', 'value' => CareerOpening::where('status', 'published')->count()],
            ],
            'modules' => [
                'Website' => [['label' => 'School Identity', 'href' => '/admin/content/settings'], ['label' => 'Hero Carousel', 'href' => '/admin/content/hero-slides'], ['label' => 'Navigation & Footer', 'href' => '/admin/content/navigation'], ['label' => 'Pages & Sections', 'href' => '/admin/content/pages']],
                'Academics' => [['label' => 'Programs & Syllabus', 'href' => '/admin/content/programs'], ['label' => 'Academic Calendar Page', 'href' => '/admin/content/pages'], ['label' => 'Curriculum Documents', 'href' => '/admin/content/documents']],
                'People' => [['label' => 'Leadership', 'href' => '/admin/content/people'], ['label' => 'Management Committee', 'href' => '/admin/content/people'], ['label' => 'Teachers and Staff', 'href' => '/admin/content/people'], ['label' => 'Careers & Vacancies', 'href' => '/admin/content/careers']],
                'Updates' => [['label' => 'Notices', 'href' => '/admin/content/notices'], ['label' => 'News & Articles', 'href' => '/admin/content/news'], ['label' => 'Events', 'href' => '/admin/content/events'], ['label' => 'Popup Notices', 'href' => '/admin/content/popups']],
                'Media' => [['label' => 'Gallery Albums', 'href' => '/admin/content/gallery'], ['label' => 'Downloads', 'href' => '/admin/content/documents'], ['label' => 'Facilities', 'href' => '/admin/content/facilities']],
                'Admissions' => [['label' => 'Admission Information', 'href' => '/admissions/information'], ['label' => 'Admission Enquiries', 'href' => '/admin/enquiries']],
                'Administration' => [['label' => 'All Enquiries', 'href' => '/admin/enquiries'], ['label' => 'Administrator Users', 'href' => '/admin/users'], ['label' => 'Published Notices', 'href' => '/admin/content/notices'], ['label' => 'Public Website', 'href' => '/']],
            ],
        ]);
    }
}
