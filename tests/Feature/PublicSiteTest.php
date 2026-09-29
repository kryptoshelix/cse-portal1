<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Models\Achievement;
use App\Models\Activity;
use App\Models\News;
use App\Models\Student;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    public function test_home_page_renders(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_public_pages_render(): void
    {
        foreach (['/about', '/contact', '/achievements', '/activities', '/projects',
                  '/publications', '/patents', '/faculty-directory', '/students',
                  '/gallery', '/news'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_only_published_achievements_are_listed(): void
    {
        $student = $this->makeUser(\App\Enums\UserRole::Student);
        $published = $this->makeAchievement($student, 'published');
        $published->forceFill(['is_published' => true, 'published_at' => now()])->save();
        $draft = $this->makeAchievement($student, 'draft');
        $approvedNotPublished = $this->makeAchievement($student, 'approved');

        $res = $this->get('/achievements')->assertOk();
        $res->assertSee($published->title);
        $res->assertDontSee($draft->title);
        $res->assertDontSee($approvedNotPublished->title);
    }

    public function test_draft_detail_page_is_not_publicly_accessible(): void
    {
        $student = $this->makeUser(\App\Enums\UserRole::Student);
        $draft = $this->makeAchievement($student, 'draft');

        // Published-only route should 404 on a draft id (IDOR / enumeration protection).
        $this->get('/achievements/'.$draft->id)->assertNotFound();
    }

    public function test_unpublished_news_and_activities_hidden(): void
    {
        News::create(['title' => 'Visible News Item', 'slug' => 'visible-news', 'body' => 'x',
            'is_published' => true, 'published_at' => now()]);
        News::create(['title' => 'Secret News Item', 'slug' => 'secret-news', 'body' => 'x',
            'is_published' => false]);
        Activity::create(['title' => 'Public Event', 'slug' => 'public-event', 'description' => 'x',
            'is_published' => true, 'published_at' => now()]);
        Activity::create(['title' => 'Private Event', 'slug' => 'private-event', 'description' => 'x',
            'is_published' => false]);

        $this->get('/news')->assertOk()->assertSee('Visible News Item')->assertDontSee('Secret News Item');
        $this->get('/news/visible-news')->assertOk();
        $this->get('/news/secret-news')->assertNotFound();
        $this->get('/activities')->assertOk()->assertSee('Public Event')->assertDontSee('Private Event');
    }

    public function test_students_without_consent_are_not_listed(): void
    {
        $a = $this->makeUser(\App\Enums\UserRole::Student);
        Student::where('user_id', $a->id)->update(['public_display_consent' => true]);
        $b = $this->makeUser(\App\Enums\UserRole::Student);
        Student::where('user_id', $b->id)->update(['public_display_consent' => false]);

        $res = $this->get('/students')->assertOk();
        $res->assertSee($a->student->roll_number);
        $res->assertDontSee($b->student->roll_number);
    }

    public function test_contact_form_accepts_valid_submission(): void
    {
        $this->post('/contact', [
            'name' => 'Jane Visitor',
            'email' => 'jane@example.com',
            'subject' => 'Enquiry',
            'message' => 'I would like to know more about the department research areas.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', ['email' => 'jane@example.com']);
    }

    public function test_contact_form_rejects_invalid_input(): void
    {
        $this->post('/contact', ['name' => '', 'email' => 'not-an-email', 'message' => 'hi'])
            ->assertSessionHasErrors(['email']);
    }

    public function test_public_pages_do_not_leak_private_evidence(): void
    {
        $student = $this->makeUser(\App\Enums\UserRole::Student);
        $a = $this->makeAchievement($student, 'published');
        $a->forceFill(['is_published' => true, 'published_at' => now()])->save();

        $this->get('/achievements/'.$a->id)
            ->assertOk()
            ->assertDontSee('documents/')      // no raw storage paths
            ->assertDontSee($student->email);  // private fields not shown
    }
}
