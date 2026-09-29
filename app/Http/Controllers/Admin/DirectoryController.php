<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Student & faculty directory record management (admins only).
 */
class DirectoryController extends Controller
{
    public function __construct(private AuditLogger $audit)
    {
    }

    public function students(Request $request)
    {
        $q = Student::with('user')->when(trim((string) $request->query('q')), function ($qq) use ($request) {
            $like = '%'.addcslashes(trim((string) $request->query('q')), '%_').'%';
            $qq->whereHas('user', fn ($u) => $u->where('name', 'like', $like))->orWhere('roll_number', 'like', $like);
        });

        return view('admin.directory.students', [
            'items' => $q->orderBy('roll_number')->paginate(15)->withQueryString(),
        ]);
    }

    public function updateStudent(Request $request, Student $student)
    {
        $data = $request->validate([
            'roll_number' => ['required', 'string', 'max:50', Rule::unique('students', 'roll_number')->ignore($student->id)],
            'program' => ['nullable', 'string', 'max:100'],
            'batch' => ['nullable', 'integer', 'between:1950,2100'],
            'cgpa' => ['nullable', 'numeric', 'between:0,10'],
            'public_display_consent' => ['nullable', 'boolean'],
        ]);

        $student->update($data);
        $this->audit->log('student.updated', "Student profile #{$student->id} updated", $student);

        return back()->with('status', 'Student record updated.');
    }

    public function createStudent(Request $request)
    {
        return view('admin.directory.student-form');
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'roll_number' => ['required', 'string', 'max:50', 'unique:students,roll_number'],
            'program' => ['nullable', 'string', 'max:100'],
            'batch' => ['nullable', 'integer', 'between:1950,2100'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => DB::raw('NULL'), // placeholder replaced below
            ]);
            // Admin-provisioned account: active immediately, no usable password until reset.
            $user->forceFill(['role' => UserRole::Student, 'status' => AccountStatus::Active])->save();
            Student::create([
                'user_id' => $user->id,
                'roll_number' => $data['roll_number'],
                'program' => $data['program'] ?? 'B.Tech CSE',
                'batch' => $data['batch'] ?? null,
            ]);
            $this->audit->log('student.created', "Student account #{$user->id} provisioned by admin", $user);
        });

        return redirect()->route('admin.students.index')->with('status', 'Student created. Share password-reset instructions with them.');
    }

    public function facultyIndex(Request $request)
    {
        $q = Faculty::with('user')->when(trim((string) $request->query('q')), function ($qq) use ($request) {
            $like = '%'.addcslashes(trim((string) $request->query('q')), '%_').'%';
            $qq->whereHas('user', fn ($u) => $u->where('name', 'like', $like))->orWhere('employee_id', 'like', $like);
        });

        return view('admin.directory.faculty', ['items' => $q->paginate(15)->withQueryString()]);
    }

    public function updateFaculty(Request $request, Faculty $faculty)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'string', 'max:50', Rule::unique('faculty', 'employee_id')->ignore($faculty->id)],
            'designation' => ['nullable', 'string', 'max:100'],
            'specialization' => ['nullable', 'string', 'max:150'],
            'qualification' => ['nullable', 'string', 'max:150'],
            'joined_on' => ['nullable', 'date'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $faculty->update($data);
        $this->audit->log('faculty.updated', "Faculty profile #{$faculty->id} updated", $faculty);

        return back()->with('status', 'Faculty record updated.');
    }
}
