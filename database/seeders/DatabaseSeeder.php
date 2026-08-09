<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hebs.edu.np'],
            ['name' => 'School Administrator', 'password' => Hash::make(env('HEBS_ADMIN_PASSWORD', 'change-me-now')), 'role' => 'administrator', 'is_active' => true]
        );

        DB::table('theme_palettes')->updateOrInsert(['slug' => 'academic-navy'], [
            'name' => 'Academic Navy',
            'colors' => json_encode([
                'brand' => '#12355b',
                'brandDark' => '#0b2038',
                'accent' => '#b8860b',
                'heading' => '#172033',
                'body' => '#334155',
                'background' => '#f7f9fc',
                'surface' => '#ffffff',
                'border' => '#d7dee8',
            ]),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $paletteId = DB::table('theme_palettes')->where('slug', 'academic-navy')->value('id');

        DB::table('school_settings')->updateOrInsert(['official_name' => 'Hamro English Boarding School'], [
            'official_name' => 'Hamro English Boarding School',
            'short_name' => 'HEBS',
            'motto' => 'Discipline, Knowledge, Character',
            'establishment_year' => '2058 B.S.',
            'address' => 'Itahari - 04, Sunsari',
            'main_phone' => '+977-25-000000',
            'official_email' => 'info@hamroenglishschool.edu.np',
            'office_hours' => 'Sunday-Friday, 9:30 AM-4:30 PM',
            'theme_palette_id' => $paletteId,
            'logo_path' => '/images/hebs-facebook-profile.jpg',
            'google_map_url' => 'https://www.google.com/maps?q=26.667878,87.270457&z=17&output=embed',
            'school_app_url' => '#',
            'social_links' => json_encode(['facebook' => 'https://www.facebook.com/Hamro.E.B.school', 'instagram' => '', 'youtube' => '']),
            'copyright_text' => '2026 Hamro English Boarding School. All Rights Reserved.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        collect([
            ['Montessori', 'montessori', 'Early years', 'Play, explore, and build strong foundational skills in a deeply nurturing and safe environment.', ['Phonics & Numeracy', 'Motor Skills', 'Social Interaction'], ['Early literacy', 'Number sense', 'Practical life'], 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=1400&auto=format&fit=crop'],
            ['Pre-primary', 'pre-primary', 'Nursery-UKG', 'Foundational literacy, numeracy, habits, and guided play for young learners.', ['Language readiness', 'Numeracy foundations', 'Learning habits'], ['English', 'Nepali', 'Mathematics', 'Creative activity'], 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?q=80&w=1400&auto=format&fit=crop'],
            ['Primary', 'primary', 'Grade 1-5', 'Curiosity-led learning that builds confidence and core academic skills through an engaging, modern curriculum.', ['Mathematics', 'Science & Technology', 'Languages'], ['English', 'Nepali', 'Mathematics', 'Science', 'Social Studies'], 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?q=80&w=1400&auto=format&fit=crop'],
            ['Secondary', 'secondary', 'Grade 6-10', 'Fostering inquiry, critical thinking, examination readiness, and practical real-world application.', ['Compulsory Mathematics', 'Optional Math / Computer', 'Science & Environment'], ['English', 'Nepali', 'Mathematics', 'Science', 'Social Studies', 'Computer'], 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1400&auto=format&fit=crop'],
            ['Grade 11-12', 'grade-11-12', 'Higher secondary', 'Preparing for higher studies and professional life with clarity, responsibility, and purpose.', ['Science', 'Management', 'Humanities'], ['Stream subjects', 'English', 'Nepali', 'Project work'], 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1400&auto=format&fit=crop'],
        ])->each(function (array $program, int $index): void {
            DB::table('academic_programs')->updateOrInsert(['slug' => $program[1]], [
                'name' => $program[0],
                'grade_or_age_range' => $program[2],
                'short_description' => $program[3],
                'full_introduction' => $program[3],
                'learning_objectives' => json_encode($program[4]),
                'curriculum_overview' => json_encode($program[5]),
                'activities' => json_encode(['Reading', 'Projects', 'Creative work', 'Sports and school activities']),
                'featured_image_path' => $program[6],
                'display_order' => $index + 1,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        collect([
            ['Admission enquiry form available', 'admission-enquiry-open', 'Admission', 'Important', 'Families can submit an admission enquiry for the new academic session.', 'published'],
            ['Parent meeting schedule published', 'parent-meeting-schedule', 'Administrative', 'Normal', 'The office has published the next parent meeting schedule.', 'published'],
            ['Terminal examination routine', 'terminal-examination-routine', 'Examination', 'Important', 'Please review the examination routine and prepare students accordingly.', 'published'],
        ])->each(function (array $notice): void {
            DB::table('notices')->updateOrInsert(['slug' => $notice[1]], [
                'title' => $notice[0], 'category' => $notice[2], 'priority' => strtolower($notice[3]),
                'summary' => $notice[4], 'content' => $notice[4], 'status' => $notice[5],
                'published_at' => now(), 'show_on_homepage' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        collect([
            ['Students complete science exhibition', 'science-exhibition', 'Learning', 'Learners presented working models and classroom research projects.', 'https://images.unsplash.com/photo-1511629091441-ee46146481b6?q=80&w=1400&auto=format&fit=crop'],
            ['Inter-house sports week concluded', 'inter-house-sports-week', 'Student Life', 'Students participated in athletics, football, volleyball, and indoor games.', 'https://images.unsplash.com/photo-1544531586-fde5298cdd40?q=80&w=1400&auto=format&fit=crop'],
            ['Reading culture campaign begins', 'reading-culture-campaign', 'Community', 'The library team introduced weekly reading circles across grades.', '/images/school-hero.jpg'],
        ])->each(function (array $post): void {
            DB::table('news_posts')->updateOrInsert(['slug' => $post[1]], [
                'title' => $post[0], 'category' => $post[2], 'summary' => $post[3], 'content' => $post[3],
                'featured_image_path' => $post[4], 'status' => 'published', 'published_at' => now(), 'show_on_homepage' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        collect([
            ['Admission counselling day', 'admission-counselling-day', 'School Office', 'upcoming'],
            ['Result distribution', 'result-distribution', 'Classrooms', 'upcoming'],
            ['Orientation program', 'orientation-program', 'Assembly Hall', 'upcoming'],
        ])->each(function (array $event, int $index): void {
            DB::table('events')->updateOrInsert(['slug' => $event[1]], [
                'title' => $event[0], 'venue' => $event[2], 'status' => $event[3],
                'starts_at' => now()->addDays(($index + 1) * 7), 'description' => 'Official school event information will be updated by the administration.',
                'show_on_homepage' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        DB::table('people')->updateOrInsert(['name' => 'School Principal', 'group_name' => 'leadership'], [
            'position' => 'Principal', 'biography' => 'Our principal guides the school community with a focus on steady learning, respectful conduct, and family partnership.', 'is_published' => true, 'display_order' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        collect([
            ['Science and activity rooms', 'Academic', 'Purposeful spaces for experiments, projects, and collaborative learning.'],
            ['Library and reading space', 'Learning', 'A quiet place for reading, research, and weekly reading circles.'],
            ['Sports and assembly areas', 'Student Life', 'Shared spaces for physical activity, events, and school community programs.'],
        ])->each(function (array $facility, int $index): void {
            DB::table('facilities')->updateOrInsert(['name' => $facility[0]], [
                'category' => $facility[1], 'description' => $facility[2], 'is_published' => true, 'display_order' => $index + 1, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        collect([
            ['School life', 'school-life', 'A curated album of classroom activity, sports, and school events.', 'School Life', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1400&auto=format&fit=crop'],
            ['Learning in action', 'learning-in-action', 'Classroom projects, practical work, and purposeful learning.', 'Academics', 'https://images.unsplash.com/photo-1574169208507-84376144848b?q=80&w=1400&auto=format&fit=crop'],
            ['Community moments', 'community-moments', 'Events, celebrations, and the people who make school life meaningful.', 'Community', 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=1200&auto=format&fit=crop'],
        ])->each(function (array $album): void {
            DB::table('gallery_albums')->updateOrInsert(['slug' => $album[1]], [
                'name' => $album[0], 'description' => $album[2], 'category' => $album[3], 'cover_image_path' => $album[4], 'album_date' => now()->toDateString(), 'is_published' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        DB::table('documents')->updateOrInsert(['title' => 'Admission information and checklist'], [
            'category' => 'Admissions', 'description' => 'The current admission overview and document checklist.', 'file_type' => 'PDF', 'status' => 'published', 'published_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        collect([
            ['Montessori to Grade 12', 'A steady foundation for a meaningful future.', 'A school community where academic discipline, personal guidance, and character grow together.', '/images/school-hero.jpg'],
            ['A complete learning pathway', 'Learning with purpose, growing with care.', 'Every stage is shaped around the changing needs of learners and their families.', 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1800&q=85'],
            ['School life', 'Confidence grows through participation.', 'Classroom learning, activity, sport, creativity, and community belong together.', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=85'],
        ])->each(function (array $slide, int $index): void {
            DB::table('hero_slides')->updateOrInsert(['title' => $slide[1]], [
                'kicker' => $slide[0], 'text' => $slide[2], 'image_path' => $slide[3], 'display_order' => $index + 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        $navigation = [
            ['header', null, 'Home', '/', 'ph-house'],
            ['header', null, 'About Us', '/our-school/about', 'ph-info'],
            ['header', null, 'Resources', '/gallery', 'ph-folder-open'],
            ['header', null, 'Updates', '/updates/news', 'ph-megaphone'],
            ['header', null, 'Academic', '/academics', 'ph-books'],
            ['header', null, 'Levels', '/academics', 'ph-graduation-cap'],
            ['header', null, 'Contact', '/contact', 'ph-phone'],
            ['header', 'About Us', 'About', '/our-school/about', 'ph-info'],
            ['header', 'About Us', 'Teachers', '/our-school/teachers', 'ph-chalkboard-teacher'],
            ['header', 'About Us', 'Administration', '/our-school/administration', 'ph-users-three'],
            ['header', 'About Us', 'Support Team', '/our-school/support-team', 'ph-hand-heart'],
            ['header', 'About Us', 'Message From Executives', '/our-school/executives-message', 'ph-chats-circle'],
            ['header', 'Resources', 'Gallery', '/gallery', 'ph-images'],
            ['header', 'Resources', 'Downloads', '/downloads', 'ph-download-simple'],
            ['header', 'Updates', 'Events', '/updates/events', 'ph-calendar-dots'],
            ['header', 'Updates', 'News & Announcements', '/updates/news', 'ph-newspaper'],
            ['header', 'Updates', 'Notices', '/updates/notices', 'ph-bell-ringing'],
            ['header', 'Academic', 'Scholarship', '/admissions/scholarships', 'ph-medal'],
            ['header', 'Academic', 'Fee Structure', '/academics/fee-structure', 'ph-receipt'],
            ['header', 'Levels', 'Pre School', '/academics/montessori', 'ph-student'],
            ['header', 'Levels', 'Pre-Primary Education', '/academics/pre-primary', 'ph-student'],
            ['header', 'Levels', 'Primary Education', '/academics/primary', 'ph-student'],
            ['header', 'Levels', 'Secondary Education', '/academics/secondary', 'ph-student'],
            ['header', 'Levels', 'Higher Secondary', '/academics/grade-11-12', 'ph-student'],
            ['quick_action', 'Live Updates', 'Notice Board', '/updates/notices', 'ph-bell-ringing'],
            ['quick_action', 'Photo Albums', 'School Gallery', '/gallery', 'ph-images'],
            ['quick_action', 'Latest Stories', 'Articles', '/updates/news', 'ph-newspaper'],
            ['quick_action', 'Forms & Syllabuses', 'Downloads', '/downloads', 'ph-download-simple'],
            ['footer_institution', null, 'School Profile', '/our-school/about', 'ph-info'],
            ['footer_institution', null, 'Executive Messages', '/our-school/executives-message', 'ph-chats-circle'],
            ['footer_institution', null, 'Teachers Directory', '/our-school/teachers', 'ph-chalkboard-teacher'],
            ['footer_institution', null, 'Administration', '/our-school/administration', 'ph-users-three'],
            ['footer_institution', null, 'Careers / Vacancies', '/our-school/careers', 'ph-briefcase'],
            ['footer_academics', null, 'Learning Pathways', '/academics', 'ph-path'],
            ['footer_academics', null, 'Course Syllabus', '/downloads', 'ph-book-open-text'],
            ['footer_academics', null, 'Notice Board', '/updates/notices', 'ph-bell-ringing'],
            ['footer_academics', null, 'Downloads', '/downloads', 'ph-download-simple'],
        ];
        collect($navigation)->each(function (array $item, int $index): void {
            DB::table('navigation_items')->updateOrInsert(['menu_group' => $item[0], 'label' => $item[2], 'href' => $item[3]], [
                'parent_label' => $item[1], 'icon' => $item[4], 'display_order' => $index + 1,
                'is_primary' => $item[0] === 'header' && in_array($item[2], ['Home', 'About Us', 'Resources', 'Updates', 'Academic', 'Levels', 'Contact'], true),
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        $pages = [
            ['home-about', 'home', 'About School', 'An institutional environment where students are known, guided, and encouraged to participate.', 'Established with a vision to deliver holistic education, HEBS blends academic rigor with respectful relationships and strong family communication. Our facilities and faculty ensure a foundation built on moral values and practical knowledge.', [['title' => 'Established', 'body' => '2058 B.S.'], ['title' => 'Learning Pathway', 'body' => 'Pre to +2'], ['title' => 'Location', 'body' => 'Itahari']], 'Home', '/images/school-hero.jpg'],
            ['home-gallery', 'home', 'Life at HEBS', 'Beyond the classroom walls.', null, [], 'Home', null],
            ['home-admissions', 'home', 'Start your journey with HEBS', 'Whether you are seeking admissions for the upcoming term, or simply wish to explore our campus and curriculum, our administration is ready to assist you.', null, [], 'Home', null],
            ['home-updates', 'home', 'Notice Board', 'Official communication', null, [['title' => 'Recent News', 'body' => 'School highlights']], 'Home', null],
            ['home-academics', 'home', 'Academic Pathways', 'Structured for Success', null, [], 'Home', null],
            ['about', 'standard', 'A school community built around steady progress.', 'Hamro English Boarding School provides a connected learning journey from Montessori through Grade 12 in Itahari, Sunsari.', null, [['title' => 'Our institutional profile', 'body' => 'The school brings together classroom learning, personal guidance, co-curricular activity, and clear communication with families.'], ['title' => 'History and direction', 'body' => 'Established in 2058 B.S., the school continues to develop a disciplined, caring environment where students build academic foundations and confidence.']], 'Our School', null],
            ['mission-values', 'standard', 'Mission, vision, and values', 'The principles that shape learning, relationships, and everyday school life.', null, [['title' => 'Mission', 'body' => 'To provide purposeful education that develops knowledge, character, discipline, creativity, and respect for others.'], ['title' => 'Vision', 'body' => 'To be a trusted school community where every learner is prepared for further education and responsible citizenship.'], ['title' => 'Core values', 'body' => 'Respect, responsibility, curiosity, integrity, cooperation, and perseverance guide our work.']], 'Our School', null],
            ['administration', 'standard', 'School administration', 'The administration coordinates daily operations, academic communication, student records, and support for families.', null, [['title' => 'School office', 'body' => 'The school office supports admissions, records, official notices, certificates, fee enquiries, and communication.'], ['title' => 'Academic coordination', 'body' => 'Academic coordinators work with teachers and leadership to maintain routines, assessments, and student support.']], 'Our School', null],
            ['support-team', 'standard', 'Support team', 'A dependable support team helps keep the campus safe, organized, welcoming, and ready for learning each day.', null, [['title' => 'Student support', 'body' => 'Staff members assist learners with routines, wellbeing, participation, and access to guidance.'], ['title' => 'Campus operations', 'body' => 'Operations staff care for facilities, safety, cleanliness, materials, and daily school needs.']], 'Our School', null],
            ['executives-message', 'standard', 'Message from the executives', 'Our leadership is committed to steady academic progress, respectful relationships, and responsible preparation for every learner.', null, [['title' => 'A shared responsibility', 'body' => 'Education is strongest when students, teachers, families, and leadership work with clarity and trust.'], ['title' => 'Purposeful growth', 'body' => 'We continue to strengthen teaching, participation, facilities, and communication.']], 'Our School', null],
            ['careers', 'standard', 'Careers and vacancies', 'Approved teaching and support vacancies will be published here with requirements, deadlines, and application instructions.', null, [['title' => 'Current opportunities', 'body' => 'There are no publicly listed vacancies at this time.'], ['title' => 'Responsible applications', 'body' => 'Applicants should use only official contact details and published instructions.']], 'Our School', null],
            ['academic-calendar', 'standard', 'Academic calendar', 'Key academic dates are published here by the school administration.', null, [['title' => 'Current session', 'body' => 'The office publishes term dates, examinations, holidays, parent meetings, and result distribution dates.'], ['title' => 'Check official updates', 'body' => 'Please confirm time-sensitive dates through the latest notice.']], 'Academics', null],
            ['fee-structure', 'standard', 'Fee structure', 'Fee information is confirmed by the school office according to academic level, session, and the approved schedule.', null, [['title' => 'Request the current schedule', 'body' => 'Contact the school office for the current approved fee schedule.'], ['title' => 'Confirm before payment', 'body' => 'Families should rely on the latest official document or direct confirmation.']], 'Academics', null],
            ['admission-information', 'standard', 'Admissions', 'We welcome families looking for a structured, caring environment from Montessori to Grade 12.', null, [['title' => 'Current admission status', 'body' => 'Admission enquiries are open. Contact the office to confirm availability.'], ['title' => 'Process and documents', 'body' => 'Review eligibility, assessment, process, and required documents before visiting.'], ['title' => 'Talk to admissions', 'body' => 'The admission office can guide you toward the right program and arrange a visit.']], 'Join the school', null],
            ['admission-process', 'standard', 'A clear admission process for families', 'Our admission team helps families understand the right program, required documents, and next steps.', null, [['title' => '1. Make an enquiry', 'body' => 'Share student and guardian details through the enquiry form or admission desk.'], ['title' => '2. Discuss the program', 'body' => 'The team explains eligibility, available places, and assessment requirements.'], ['title' => '3. Complete documentation', 'body' => 'Visit the school and submit documents for the relevant academic program.']], 'Admissions', null],
            ['required-documents', 'standard', 'Required documents', 'Keep the requested student and guardian records ready for admission.', null, [['title' => 'Typical documents', 'body' => 'Birth certificate, previous school report, photographs, guardian identification, and program-specific records.'], ['title' => 'Confirm before visiting', 'body' => 'Requirements may vary by grade and session. Contact admissions for the current checklist.']], 'Admissions', null],
            ['scholarships', 'standard', 'Scholarships and support', 'School-approved scholarship opportunities are posted here when applications open.', null, [['title' => 'Responsible communication', 'body' => 'Scholarship details are published with eligibility, deadlines, and approved contacts.']], 'Admissions', null],
            ['faqs', 'standard', 'Frequently asked questions', 'Answers to common questions from prospective families.', null, [['title' => 'Which programs are available?', 'body' => 'The school provides Montessori through Grade 12 programs.'], ['title' => 'How do I start?', 'body' => 'Submit an admission enquiry or call the school office.'], ['title' => 'Can I visit?', 'body' => 'Contact the office during working hours to arrange a visit.']], 'Admissions', null],
            ['student-life', 'standard', 'Learning continues beyond the classroom', 'School life gives students opportunities to practise teamwork, responsibility, creativity, and confidence.', null, [['title' => 'Activities and participation', 'body' => 'Clubs, sports, cultural programs, competitions, visits, and student leadership create opportunities to participate.'], ['title' => 'A balanced routine', 'body' => 'Activities complement academic work and encourage students to discover strengths.']], 'School Life', null],
            ['achievements', 'standard', 'Achievements', 'We recognize the work, participation, and progress of students, teachers, teams, and the wider community.', null, [['title' => 'District-level quiz competition finalist', 'body' => 'School achievement published by the administration.'], ['title' => 'Inter-school football runner-up', 'body' => 'School achievement published by the administration.'], ['title' => 'Science exhibition participation', 'body' => 'School achievement published by the administration.']], 'School Life', null],
            ['articles', 'standard', 'Articles', 'Articles on parenting, learning, study skills, teaching, child development, and community life.', null, [['title' => 'Editorial publishing', 'body' => 'Published editorial content can be maintained here by the administration.']], 'Updates', null],
            ['privacy', 'standard', 'Privacy policy', 'The school collects and uses only the information required to respond to enquiries and operate this website.', null, [['title' => 'Information we collect', 'body' => 'Enquiry forms collect details needed to respond, and administrative records are restricted to authorized staff.'], ['title' => 'Your choices', 'body' => 'Contact the school office to request correction where permitted.']], 'Legal', null],
            ['terms', 'standard', 'Website terms', 'This website publishes official school information for families, students, and public visitors.', null, [['title' => 'Using published information', 'body' => 'Confirm time-sensitive information with the school office.'], ['title' => 'Content and photography', 'body' => 'School content and approved photographs must not be reused in a misleading way.']], 'Legal', null],
        ];
        collect($pages)->each(function (array $page): void {
            DB::table('pages')->updateOrInsert(['slug' => $page[0]], [
                'template' => $page[1], 'title' => $page[2], 'summary' => $page[3], 'content' => $page[4], 'sections' => json_encode($page[5]),
                'eyebrow' => $page[6], 'seo' => json_encode(['eyebrow' => $page[6]]), 'image_path' => $page[7], 'status' => 'published', 'published_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
    }
}
