<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Teacher;

class TeacherController extends Controller
{
    /**
     * Teacher module dashboard with live stats.
     */
    public function dashboard()
    {
        $totalTeachers = Teacher::count();
        $activeTeachers = Teacher::where('status', true)->count();
        $inactiveTeachers = $totalTeachers - $activeTeachers;
        $totalSalary = Teacher::sum('salary');
        $recentTeachers = Teacher::latest()->take(5)->get();

        return view('teacher.dashboard', compact(
            'totalTeachers',
            'activeTeachers',
            'inactiveTeachers',
            'totalSalary',
            'recentTeachers'
        ));
    }

    /**
     * List teachers with search + status filter.
     */
    public function index(Request $request)
    {
        $query = Teacher::query();
        $search = trim((string) $request->input('search'));
        $search = preg_replace('/\s+/', ' ', $search);

        if ($search !== '') {
            $tokens = array_values(array_filter(explode(' ', strtolower($search))));
            $query->where(function ($main) use ($search, $tokens) {
                $fullLike = '%' . $search . '%';
                $main->where('first_name', 'like', $fullLike)
                    ->orWhere('last_name', 'like', $fullLike)
                    ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ['%' . strtolower($search) . '%'])
                    ->orWhere('teacher_id', 'like', $fullLike)
                    ->orWhere('email', 'like', $fullLike)
                    ->orWhere('subject', 'like', $fullLike)
                    ->orWhere('phone', 'like', $fullLike)
                    ->orWhere('cnic', 'like', $fullLike)
                    ->orWhere('address', 'like', $fullLike)
                    ->orWhere('qualification', 'like', $fullLike)
                    ->orWhere('class_id', 'like', $fullLike)
                    ->orWhere('section_id', 'like', $fullLike);

                foreach ($tokens as $token) {
                    $like = '%' . $token . '%';
                    $main->orWhere(function ($q) use ($like) {
                        $q->whereRaw('LOWER(first_name) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(last_name) LIKE ?', [$like])
                            ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", [$like])
                            ->orWhereRaw('LOWER(teacher_id) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(email) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(subject) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(phone) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(cnic) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(address) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(qualification) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(class_id) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(section_id) LIKE ?', [$like]);
                    });
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status') === 'active' ? 1 : 0);
        }

        $teachers = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('teacher.index', compact('teachers', 'search'));
    }

    public function create()
    {
        return view('teacher.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'date_of_birth'  => 'required|date|before:today',
            'cnic'           => 'required|string|max:20|unique:teachers,cnic',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|unique:teachers,email',
            'address'        => 'nullable|string',
            'qualification'  => 'required|string|max:150',
            'experience'     => 'required|numeric|min:0',
            'joining_date'   => 'required|date',
            'salary'         => 'required|numeric|min:0',
            'class_id'       => 'nullable|string|max:50',
            'section_id'     => 'nullable|string|max:50',
            'subject'        => 'required|string|max:100',
            'photo'          => 'nullable|image|max:2048',
        ]);

        $teacher = new Teacher();
        $teacher->teacher_id = 'TCH' . date('Y') . sprintf('%04d', ((int) Teacher::max('id')) + 1);
        $teacher->first_name = $validated['first_name'];
        $teacher->last_name = $validated['last_name'];
        $teacher->gender = $validated['gender'];
        $teacher->dob = $validated['date_of_birth'];
        $teacher->cnic = $validated['cnic'];
        $teacher->phone = $validated['phone'];
        $teacher->email = $validated['email'];
        $teacher->address = $validated['address'] ?? null;
        $teacher->qualification = $validated['qualification'];
        $teacher->experience = $validated['experience'];
        $teacher->joining_date = $validated['joining_date'];
        $teacher->salary = $validated['salary'];
        $teacher->class_id = $validated['class_id'] ?? null;
        $teacher->section_id = $validated['section_id'] ?? null;
        $teacher->subject = $validated['subject'];
        $teacher->status = true;

        if ($request->hasFile('photo')) {
            $teacher->photo = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->save();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Teacher added successfully.');
    }

    public function show($id)
    {
        $teacher = Teacher::findOrFail($id);

        return view('teacher.show', compact('teacher'));
    }

    public function edit($id)
    {
        $teacher = Teacher::findOrFail($id);

        return view('teacher.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'gender'         => 'required|in:Male,Female',
            'date_of_birth'  => 'required|date|before:today',
            'cnic'           => 'required|string|max:20|unique:teachers,cnic,' . $teacher->id,
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|unique:teachers,email,' . $teacher->id,
            'address'        => 'nullable|string',
            'qualification'  => 'required|string|max:150',
            'experience'     => 'required|numeric|min:0',
            'joining_date'   => 'required|date',
            'salary'         => 'required|numeric|min:0',
            'class_id'       => 'nullable|string|max:50',
            'section_id'     => 'nullable|string|max:50',
            'subject'        => 'required|string|max:100',
            'photo'          => 'nullable|image|max:2048',
            'status'         => 'nullable|boolean',
        ]);

        $teacher->first_name = $validated['first_name'];
        $teacher->last_name = $validated['last_name'];
        $teacher->gender = $validated['gender'];
        $teacher->dob = $validated['date_of_birth'];
        $teacher->cnic = $validated['cnic'];
        $teacher->phone = $validated['phone'];
        $teacher->email = $validated['email'];
        $teacher->address = $validated['address'] ?? null;
        $teacher->qualification = $validated['qualification'];
        $teacher->experience = $validated['experience'];
        $teacher->joining_date = $validated['joining_date'];
        $teacher->salary = $validated['salary'];
        $teacher->class_id = $validated['class_id'] ?? null;
        $teacher->section_id = $validated['section_id'] ?? null;
        $teacher->subject = $validated['subject'];
        $teacher->status = $request->boolean('status');

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $teacher->photo = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->save();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);

        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $teacher->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }
}
