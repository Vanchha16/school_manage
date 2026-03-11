<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Item;
use App\Models\Student;
use App\Models\StudentSubmission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $submissions = StudentSubmission::query()
            ->with('group')
            ->when($q, function ($query) use ($q) {
                $query->where('student_name', 'like', "%{$q}%")
                    ->orWhere('phone_number', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // ✅ attach match info: existing student + reason (phone/name)
        $submissions->getCollection()->transform(function ($sub) {

            $existing = null;
            $reason = null;

            // 1) match by phone first (best unique)
            if (! empty($sub->phone_number)) {
                $existing = Student::where('phone_number', $sub->phone_number)->first();
                if ($existing) {
                    $reason = 'phone';
                }
            }

            // 2) fallback match by name only if no phone match
            if (! $existing) {
                $existing = Student::where('student_name', $sub->student_name)->first();
                if ($existing) {
                    $reason = 'name';
                }
            }

            $sub->existing_student = $existing; // Student or null
            $sub->existing_reason = $reason;   // 'phone' | 'name' | null

            return $sub;
        });

        return view('backend.page.submissions.index', compact('submissions'));
    }

    // Register form will POST here (store into submissions only)
    public function store(Request $request)
    {
        $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:Male,Female'],
            'phone_number' => ['required', 'string', 'max:30'],
            'group_id' => ['required', 'exists:groups,group_id'],
            'item_id' => ['required', 'exists:items,Itemid'],
            'qty' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $existingStudentByName = Student::where('student_name', $request->student_name)->first();
        $existingStudentByPhone = Student::where('phone_number', $request->phone_number)->first();

        if ($existingStudentByPhone) {
            if (! $existingStudentByName || $existingStudentByPhone->student_id !== $existingStudentByName->student_id) {
                return back()
                    ->withErrors(['phone_number' => 'This phone number is already used by another student.'])
                    ->withInput();
            }
        }

        $item = Item::where('Itemid', $request->item_id)->firstOrFail();

        if ($item->qty < $request->qty) {
            return back()
                ->withErrors(['qty' => 'Not enough stock available.'])
                ->withInput();
        }

        $duplicateSubmission = StudentSubmission::where('student_name', $request->student_name)
            ->where('phone_number', $request->phone_number)
            ->where('group_id', $request->group_id)
            ->where('item_id', $request->item_id)
            ->where('qty', $request->qty)
            ->where('is_borrow_approved', false)
            ->first();

        if ($duplicateSubmission) {
            return back()
                ->withErrors(['student_name' => 'This form was already submitted and is still pending approval.'])
                ->withInput();
        }

        StudentSubmission::create([
            'student_name' => $request->student_name,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'group_id' => $request->group_id,
            'item_id' => $request->item_id,
            'qty' => $request->qty,
            'status' => 'BORROWED',
            'note' => $request->notes,
            'student_id' => $existingStudentByName?->student_id,
            'is_student_existing' => $existingStudentByName ? true : false,
            'is_student_added' => $existingStudentByName ? true : false,
            'is_borrow_approved' => false,
        ]);

        return back()->with('success', 'Submitted successfully. Please wait for approval.');
    }

    // Approve = insert into students table (only if not exist)
    public function approve(StudentSubmission $submission)
    {
        $existing = null;
        $reason = null;

        // 1) if phone exists, use phone as unique key
        $phoneExists = Student::where('phone_number', $submission->phone_number)->exists();

        if ($phoneExists) {
            return back()->withErrors([
                'error' => 'This phone number is already used in student database.',
            ]);
        }

        // 2) fallback: if no phone, check name
        if (! $existing) {
            $existing = Student::where('student_name', $submission->student_name)->first();
            if ($existing) {
                $reason = 'name';
            }
        }

        // ✅ If already exists, show friendly message
        if ($existing) {
            if ($reason === 'phone') {
                return back()->withErrors([
                    'student' => "Phone already used by {$existing->student_name} (ID: {$existing->student_id}).",
                ]);
            }

            return back()->withErrors([
                'student' => "Student name already exists: {$existing->student_name} (ID: {$existing->student_id}).",
            ]);
        }

        // ✅ Add to students table
        $student = Student::create([
            'student_name' => $submission->student_name,
            'phone_number' => $submission->phone_number,
            'group_id' => $submission->group_id,
            'gender' => $submission->gender,
            'status' => 1,
        ]);

        // delete submission after approve
        $submission->delete();

        return redirect()->route('students.index')->with('success', 'Student added to database.');
    }

    // Cancel submission
    public function destroy(StudentSubmission $submission)
    {
        $submission->delete();

        return back()->with('success', 'Submission canceled.');
    }

    public function goManage(StudentSubmission $submission)
    {
        // find the student that already exists (phone first, else name)
        $student = null;

        if (! empty($submission->phone_number)) {
            $student = Student::where('phone_number', $submission->phone_number)->first();
        }

        if (! $student) {
            $student = Student::where('student_name', $submission->student_name)->first();
        }

        if (! $student) {
            return back()->withErrors(['student' => 'Student not found in database.']);
        }

        // ✅ delete submission
        $submission->delete();

        // ✅ redirect to borrows and open borrow modal
        return redirect()->to(
            url(
                'admin/borrows?openBorrow=1'
                    .'&student_id='.$student->student_id
                    .'&student_name='.urlencode($student->student_name)
            )
        )->with('success', 'Submission cleared. You can borrow item now.');
    }

    public function addStudent($id)
    {
        $submission = StudentSubmission::findOrFail($id);

        if ($submission->student_id) {
            return back()->with('success', 'Student already exists.');
        }

        $phoneExists = Student::where('phone_number', $submission->phone_number)->first();

        if ($phoneExists) {
            return back()->withErrors([
                'error' => 'This phone number is already used in student database.',
            ]);
        }

        $student = Student::create([
            'student_name' => $submission->student_name,
            'gender' => $submission->gender,
            'phone_number' => $submission->phone_number,
            'group_id' => $submission->group_id,
            'status' => 1,
        ]);

        $submission->update([
            'student_id' => $student->student_id,
            'is_student_added' => true,
        ]);

        return back()->with('success', 'Student added successfully. Now you can approve borrow.');
    }

    public function approveBorrow($id)
    {
        $submission = StudentSubmission::findOrFail($id);

        if (! $submission->student_id) {
            return back()->withErrors(['error' => 'Please add the student first.']);
        }

        if ($submission->is_borrow_approved) {
            return back()->with('success', 'Borrow already approved.');
        }

        $item = Item::where('Itemid', $submission->item_id)->firstOrFail();

        if ($item->qty < $submission->qty) {
            return back()->withErrors(['error' => 'Not enough stock to approve this borrow.']);
        }

        Borrow::create([
            'student_id' => $submission->student_id,
            'item_id' => $submission->item_id,
            'borrow_date' => now()->format('Y-m-d H:i:s'),
            'qty' => $submission->qty,
            'status' => 'BORROWED',
            'notes' => $submission->note,
        ]);

        $item->decrement('qty', $submission->qty);

        $submission->update([
            'is_borrow_approved' => true,
        ]);

        return back()->with('success', 'Borrow approved successfully.');
    }

    public function remove($id)
    {
        $submission = StudentSubmission::findOrFail($id);
        $submission->delete();

        return back()->with('success', 'Submission removed successfully.');
    }

    public function cancelAll()
    {
        StudentSubmission::query()->delete();

        return back()->with('success', 'All submissions cancelled successfully.');
    }
}
