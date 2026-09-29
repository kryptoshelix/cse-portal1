<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\AchievementStatus;
use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\Activity;
use App\Models\Faculty;
use App\Models\GalleryItem;
use App\Models\News;
use App\Models\Patent;
use App\Models\Project;
use App\Models\Publication;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * DEVELOPMENT-ONLY SEEDER.
 * All content below is clearly fictional sample data (fake names, .example
 * emails, made-up events). It must never be used to populate a production
 * database, and it contains NO real institutional facts or credentials.
 *
 * Demo accounts use the conventional local-development password
 * "password" (documented in README/SETUP) purely so developers can sign in
 * quickly on a throwaway database. Production admins must be provisioned
 * with `php artisan admin:create` instead.
 */
class DatabaseSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'password';

    public function run(): void
    {
        $this->categories();
        $admins = $this->adminAccounts();
        $facultyUsers = $this->facultyAccounts(6);
        $facultyRecords = Faculty::whereIn('user_id', collect($facultyUsers)->pluck('id'))->get();
        $students = $this->studentAccounts(12);
        $this->achievements($facultyUsers, $students);
        $this->content($facultyRecords, $admins);
    }

    private function mkUser(string $name, string $email, UserRole $role, AccountStatus $status = AccountStatus::Active): User
    {
        // role/status are intentionally NOT mass-assignable on User; set them
        // explicitly here (development seeding only).
        $u = new User();
        $u->name = $name;
        $u->email = $email;
        $u->password = Hash::make(self::DEMO_PASSWORD);
        $u->forceFill(['role' => $role->value, 'status' => $status->value])->save();
        return $u;
    }

    private function categories(): void
    {
        foreach ([
            ['Academic', 'academic'], ['Sports & Games', 'sports-games'],
            ['Cultural', 'cultural'], ['Technical / Competitions', 'technical-competitions'],
            ['Research', 'research'], ['Leadership', 'leadership'],
        ] as [$name, $slug]) {
            AchievementCategory::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'description' => "Sample category: {$name}",
            ]);
        }
    }

    private function adminAccounts(): array
    {
        $super = $this->mkUser('Sample Super Admin', 'superadmin@cseportal.example', UserRole::SuperAdmin);
        $dept = $this->mkUser('Sample Dept Admin', 'deptadmin@cseportal.example', UserRole::DeptAdmin);
        return [$super, $dept];
    }

    private function facultyAccounts(int $n): array
    {
        $out = [];
        for ($i = 1; $i <= $n; $i++) {
            $u = $this->mkUser(fake()->name(), "faculty{$i}@cseportal.example", UserRole::Faculty);
            Faculty::create([
                'user_id' => $u->id,
                'employee_id' => 'CSE-F'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'designation' => ['Assistant Professor', 'Associate Professor', 'Professor'][$i % 3],
                'specialization' => ['Machine Learning', 'Cybersecurity', 'Databases', 'Networks', 'Computer Vision', 'HCI'][$i % 6],
                'qualification' => 'Ph.D. (Fictional University)',
                'joined_on' => now()->subYears($i)->startOfYear(),
                'bio' => fake()->sentence(12),
            ]);
            $out[] = $u;
        }
        return $out;
    }

    private function studentAccounts(int $n): array
    {
        $out = [];
        for ($i = 1; $i <= $n; $i++) {
            $pending = $i > $n - 2; // last two remain pending to demo approval flow
            $u = $this->mkUser(
                fake()->name(),
                "student{$i}@cseportal.example",
                UserRole::Student,
                $pending ? AccountStatus::Pending : AccountStatus::Active
            );
            Student::create([
                'user_id' => $u->id,
                'roll_number' => 'CSE22'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'program' => 'B.Tech CSE',
                'batch' => 2022 + ($i % 3),
                'cgpa' => number_format(6 + ($i % 4) + 0.5, 2),
                'public_display_consent' => !$pending && $i % 2 === 0,
            ]);
            if (!$pending) $out[] = $u;
        }
        return $out;
    }

    private function achievements(array $faculty, array $students): void
    {
        $cats = AchievementCategory::all();
        $statuses = [
            AchievementStatus::Draft, AchievementStatus::Pending, AchievementStatus::Pending,
            AchievementStatus::Rejected, AchievementStatus::Approved, AchievementStatus::Published,
            AchievementStatus::Published, AchievementStatus::Published,
        ];
        foreach ($students as $idx => $stu) {
            $status = $statuses[$idx % count($statuses)];
            $a = new Achievement();
            $a->title = fake()->sentence(6);
            $a->description = fake()->paragraph();
            $a->achievement_category_id = $cats->random()->id;
            $a->achievement_date = now()->subDays(random_int(10, 300));
            $a->issuing_organization = fake()->company();
            $a->level = ['Institutional', 'State', 'National', 'International'][$idx % 4];
            $a->owner_user_id = $stu->id;
            $a->created_by = $stu->id;
            $a->student_id = $stu->student?->id;
            $a->status = $status->value;
            $a->public_display_consent = true;
            if (in_array($status, [AchievementStatus::Pending, AchievementStatus::Approved, AchievementStatus::Published], true)) {
                $a->submitted_at = now()->subDays(random_int(2, 20));
            }
            if ($status === AchievementStatus::Rejected) {
                $a->rejection_feedback = 'Certificate copy is illegible. Please upload a clearer scan and resubmit.';
                $a->reviewed_by = 1; $a->reviewed_at = now()->subDays(3);
            }
            if (in_array($status, [AchievementStatus::Approved, AchievementStatus::Published], true)) {
                $a->reviewed_by = 1; $a->reviewed_at = now()->subDays(5); $a->approved_at = now()->subDays(5);
            }
            if ($status === AchievementStatus::Published) {
                $a->published_at = now()->subDays(4);
                $a->featured = $idx % 3 === 0;
            }
            $a->save();
        }
        // A faculty-owned submission too.
        $f = $faculty[0];
        $a = new Achievement();
        $a->title = 'Faculty Excellence Award (sample)';
        $a->achievement_category_id = $cats->firstWhere('slug', 'research')->id ?? $cats->first()->id;
        $a->achievement_date = now()->subDays(30);
        $a->level = 'National';
        $a->owner_user_id = $f->id;
        $a->created_by = $f->id;
        $a->status = AchievementStatus::Published->value;
        $a->public_display_consent = true;
        $a->submitted_at = now()->subDays(28);
        $a->reviewed_by = 1; $a->reviewed_at = now()->subDays(27); $a->approved_at = now()->subDays(27);
        $a->published_at = now()->subDays(26);
        $a->save();
    }

    private function content(array $faculty, array $admins): void
    {
        foreach (range(1, 6) as $i) {
            Activity::create([
                'title' => fake()->words(5, true),
                'slug' => Str::slug(fake()->unique()->words(5, true)),
                'description' => fake()->paragraphs(2, true),
                'type' => ['event', 'workshop', 'seminar', 'guest_lecture', 'social'][$i % 5],
                'start_date' => now()->addDays(($i - 3) * 9)->toDateString(),
                'venue' => 'Seminar Hall, Academic Block C',
                'organizer' => 'CSE Department (sample)',
                'is_published' => true,
                'published_at' => now()->subDays($i),
                'featured' => $i === 1,
            ]);
        }
        foreach (range(1, 6) as $i) {
            Project::create([
                'title' => Str::ucfirst(fake()->words(4, true)).' System',
                'description' => fake()->paragraph(),
                'category' => ['Web', 'AI/ML', 'IoT', 'Security'][$i % 4],
                'tech_stack' => ['Laravel, MySQL', 'Python, TensorFlow', 'ESP32, MQTT', 'Node.js, React'][$i % 4],
                'year' => 2023 + ($i % 3),
                'guide_faculty_id' => $faculty[$i % count($faculty)]->id,
                'members' => fake()->name().', '.fake()->name(),
                'is_published' => true,
                'published_at' => now()->subDays($i * 4),
                'featured' => $i <= 2,
            ]);
        }
        foreach (range(1, 5) as $i) {
            Publication::create([
                'title' => Str::ucfirst(fake()->sentence(9)),
                'authors' => fake()->name().' et al.',
                'journal_or_conference' => 'Journal of Fictional Computing Studies',
                'volume_issue' => 'Vol. '.random_int(3, 20).', Issue '.random_int(1, 6),
                'publication_date' => now()->subMonths(random_int(2, 30))->toDateString(),
                'doi' => '10.5555/sample.'.$i,
                'faculty_id' => $faculty[$i % count($faculty)]->id,
                'abstract' => fake()->paragraph(4),
                'is_published' => true,
                'published_at' => now()->subDays($i * 6),
            ]);
        }
        foreach (range(1, 3) as $i) {
            Patent::create([
                'title' => Str::ucfirst(fake()->words(6, true)).' Method and System',
                'patent_number' => 'SAMPLE-20'.$i.'4-00'.$i,
                'filing_date' => now()->subMonths(random_int(4, 24))->toDateString(),
                'filing_status' => $i === 3 ? 'granted' : 'applied',
                'inventors' => fake()->name().', '.fake()->name(),
                'faculty_id' => $faculty[$i % count($faculty)]->id,
                'is_published' => true,
                'published_at' => now()->subDays($i * 8),
            ]);
        }
        foreach (range(1, 6) as $i) {
            GalleryItem::create([
                'title' => Str::ucfirst(fake()->words(3, true)),
                'caption' => fake()->sentence(8),
                'image_path' => null, // placeholder thumbs rendered by CSS icon
                'taken_on' => now()->subDays($i * 11)->toDateString(),
                'is_published' => true,
                'published_at' => now()->subDays($i * 3),
            ]);
        }
        foreach (range(1, 5) as $i) {
            News::create([
                'heading' => '[SAMPLE] '.Str::ucfirst(fake()->sentence(6)),
                'body' => fake()->paragraphs(3, true),
                'excerpt' => fake()->sentence(14),
                'pinned' => $i === 1,
                'author_user_id' => $admins[0]->id,
                'is_published' => true,
                'published_at' => now()->subDays($i * 2),
            ]);
        }
    }
}
