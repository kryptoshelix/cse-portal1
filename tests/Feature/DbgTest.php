<?php
namespace Tests\Feature;
use App\Enums\UserRole;
use Tests\TestCase;
class DbgTest extends TestCase {
    public function test_dbg(): void {
        $student = $this->makeUser(UserRole::Student);
        $r = $this->actingAs($student)->get('/login');
        fwrite(STDERR, "STATUS: ".$r->getStatusCode()." LOC: ".var_export($r->headers->get('Location'), true)." SESSIONUSER: ".var_export(auth()->check(), true)."\n");
        $this->assertTrue(true);
    }
}
