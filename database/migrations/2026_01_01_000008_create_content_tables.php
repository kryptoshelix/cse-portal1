<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type', 30)->default('event'); // event, workshop, seminar, guest_lecture, social
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('venue')->nullable();
            $table->string('organizer')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'start_date']);
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('tech_stack')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->foreignId('guide_faculty_id')->nullable()->constrained('faculty')->nullOnDelete();
            $table->string('members')->nullable(); // comma-separated display names
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'year']);
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('authors');
            $table->string('journal_or_conference')->nullable();
            $table->string('volume_issue', 100)->nullable();
            $table->date('publication_date')->nullable();
            $table->string('doi')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('faculty_id')->nullable()->constrained('faculty')->nullOnDelete();
            $table->text('abstract')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'publication_date']);
        });

        Schema::create('patents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('patent_number', 100)->nullable();
            $table->date('filing_date')->nullable();
            $table->string('filing_status', 30)->default('applied'); // applied | granted
            $table->string('inventors');
            $table->foreignId('faculty_id')->nullable()->constrained('faculty')->nullOnDelete();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'filing_date']);
        });

        Schema::create('gallery', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('caption')->nullable();
            $table->string('image_path');
            $table->date('taken_on')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'taken_on']);
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('heading');
            $table->string('slug')->unique();
            $table->text('body')->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('pinned')->default(false);
            $table->foreignId('author_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'published_at']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('action', 100);
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'contact_messages', 'news', 'gallery', 'patents', 'publications', 'projects', 'activities'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
