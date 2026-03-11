<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Item;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentRegisterController extends Controller
{
    public function create()
    {
        $groups = Group::orderBy('group_name')->get();
        $items = Item::orderBy('name')->get();
        $students = Student::orderBy('student_name')->get();

        return view('backend.page.registerStudent.index', compact('groups', 'items', 'students'));
    }



    public function store(Request $request)
    {
        $messages = [
    'student_name.unique' => 'This student name already exists in this group.',
];

$data = $request->validate([
    'student_name' => [
        'required', 'string', 'max:255',
        Rule::unique('students')->where(function ($q) use ($request) {
            return $q->where('group_id', $request->group_id);
        }),
    ],
    'phone_number' => ['nullable', 'string', 'max:50'],
    'group_id'     => ['required', 'exists:groups,group_id'],
], $messages);

        Student::create([
            'student_name' => $data['student_name'],
            'phone_number' => $data['phone_number'] ?? null,
            'group_id'     => $data['group_id'],
            'status'       => 1,
        ]);

        return redirect()->route('student.register')->with('success', 'Registration successful!');
    }
    public function checkPhone(Request $request)
{
    $student = Student::where('phone_number', $request->phone_number)->first();

    if ($student) {
        return response()->json([
            'exists' => true,
            'student_name' => $student->student_name,
            'gender' => $student->gender,
            'group_id' => $student->group_id,
        ]);
    }

    return response()->json([
        'exists' => false,
    ]);
}
public function checkStudentName(Request $request)
{
    $student = Student::with('group')
        ->where('student_name', $request->student_name)
        ->first();

    if ($student) {
        return response()->json([
    'exists' => true,
    'student_name' => $student->student_name,
    'gender' => $student->gender,
    'phone_number' => $student->phone_number,
    'group_name' => $student->group->group_name ?? '',
]);
    }

    return response()->json([
        'exists' => false,
    ]);
}
}
