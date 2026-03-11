<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Group;
use App\Models\Item;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Auto update BORROWED -> OVERDUE (not returned after 3 days)
        Borrow::query()
            ->where('status', 'BORROWED')
            ->whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(3))
            ->update(['status' => 'OVERDUE']);

        $students = Student::orderBy('student_name')->get();
        $items = Item::orderBy('name')->get();
        $groups = Group::orderBy('group_name')->get();

        // ✅ base query
        $query = Borrow::with(['student.group', 'item']);

        // ✅ search by student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // ✅ filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ✅ filter item (borrows table uses item_id)
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        // ✅ filter group (via student)
        if ($request->filled('group_id')) {
            $groupId = $request->group_id;
            $query->whereHas('student', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        }

        // ✅ active list for return modal: only BORROWED
        $activeBorrows = Borrow::with(['student', 'item'])
    ->whereIn('status', ['BORROWED', 'OVERDUE'])
    ->orderByDesc('borrow_date')
    ->get();

        // ✅ paginate + keep query string
        $borrows = $query->orderByDesc('id')->paginate(5)->withQueryString();

        // ✅ Stats (global, not filtered)
        $stats = [
            'total_records' => Borrow::count(),
            'active_records' => Borrow::where('status', 'BORROWED')->count(),
            'overdue_records' => Borrow::where('status', 'OVERDUE')->count(),
            'returned_records' => Borrow::where('status', 'RETURNED')->count(),
            'total_qty' => Borrow::sum('qty'),
            'borrowed_qty' => Borrow::where('status', 'BORROWED')->sum('qty'),
            'returned_qty' => Borrow::where('status', 'RETURNED')->sum('qty'),
        ];

        return view('backend.page.borrows.index', compact(
            'students',
            'items',
            'groups',
            'activeBorrows',
            'borrows',
            'stats'
        ));
    }

    // 1) Borrow Item
    public function storeBorrow(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,student_id'],
            'item_id' => ['required', 'exists:items,Itemid'],
            'qty' => ['required', 'integer', 'min:1'],
            // ✅ FIX: due date cannot be in the past
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $item = Item::where('Itemid', $data['item_id'])->firstOrFail();

        // ✅ check stock
        if (($item->qty ?? 0) < $data['qty']) {
            return back()
                ->withErrors(['qty' => "Not enough stock. Available: {$item->qty}"])
                ->withInput();
        }

        Borrow::create([
            'student_id' => $data['student_id'],
            'item_id' => $data['item_id'],
            'qty' => $data['qty'],
            'borrow_date' => now('Asia/Jakarta'),
            'due_date' => $data['due_date'] ?? null,
            'status' => 'BORROWED',
            'notes' => $data['notes'] ?? null,
        ]);

        // ✅ decrease item qty
        $item->decrement('qty', $data['qty']);

        return redirect()->route('borrows.index')->with('success', 'Borrow saved.');
    }

    // 2) Return Item
    public function storeReturn(Request $request)
{
    $data = $request->validate([
        'borrow_id' => [
            'required',
            'exists:borrows,id',
            Rule::exists('borrows', 'id')
                ->where(fn($q) => $q->whereIn('status', ['BORROWED', 'OVERDUE'])),
        ],
        'return_date'  => ['required', 'date_format:Y-m-d\TH:i'],
        'condition'    => ['required', 'string', 'max:50'],
        'return_notes' => ['nullable', 'string', 'max:1000'],
    ]);

    $borrow = Borrow::where('id', $data['borrow_id'])
        ->whereIn('status', ['BORROWED', 'OVERDUE'])
        ->firstOrFail();

    // add item qty back
    Item::where('Itemid', $borrow->item_id)->increment('qty', $borrow->qty);

    $returnDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $data['return_date'], 'Asia/Jakarta');

    $borrow->update([
        'return_date'   => $returnDate,
        'condition'     => $data['condition'],
        'return_notes'  => $data['return_notes'] ?? null,
        'status'        => 'RETURNED',
    ]);

    return redirect()->route('borrows.index')->with('success', 'Return saved.');
}

    public function undoReturn(Request $request, Borrow $borrow)
    {
        if ($borrow->status !== 'RETURNED' ) {
            return back()->withErrors(['status' => 'Only RETURNED records can be undone.']);
        }

        $item = Item::where('Itemid', $borrow->item_id)->firstOrFail();

        if (($item->qty ?? 0) < ($borrow->qty ?? 1)) {
            return back()->withErrors([
                'qty' => "Cannot undo return. Item stock is too low now (Available: {$item->qty}).",
            ]);
        }

        $item->decrement('qty', $borrow->qty ?? 1);

        $borrow->update([
            'status' => 'BORROWED',
            'return_date' => null,
            'condition' => null,
            'return_notes' => null,
        ]);

        return redirect()->route('borrows.index')->with('success', 'Return undone. Status is BORROWED again.');
    }

    public function destroy(Borrow $borrow)
    {
        if ($borrow->status === 'BORROWED') {
            Item::where('Itemid', $borrow->item_id)->increment('qty', $borrow->qty ?? 1);
        }

        $borrow->delete();

        return redirect()->route('borrows.index')->with('success', 'Borrow record deleted.');
    }

    // ✅ Returned Late: returned after due_date + 3 days
    public function lateReturns(Request $request)
    {
        $q = $request->get('q');

        $query = Borrow::query()
            ->whereNotNull('return_date')
            // ✅ B: late if returned after borrow_date + 3 days
            ->whereRaw('return_date > DATE_ADD(borrow_date, INTERVAL 3 DAY)')
            ->with(['student', 'item']);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->whereHas('student', function ($s) use ($q) {
                    $s->where('student_name', 'like', "%{$q}%")
                        ->orWhere('phone_number', 'like', "%{$q}%");
                })
                    ->orWhereHas('item', function ($i) use ($q) {
                        $i->where('name', 'like', "%{$q}%"); // ✅ items.name
                    });
            });
        }

        $lateReturns = $query->orderByDesc('return_date')
            ->paginate(10)
            ->appends($request->query());

        return view('backend.page.borrows.late_returns', compact('lateReturns'));
    }

    // ✅ Overdue: not returned after 3 days
    public function overdueBorrows(Request $request)
    {
        $q = $request->get('q');

        $query = Borrow::query()
            ->whereNull('return_date')
            ->where('borrow_date', '<', now()->subDays(3))
            ->whereIn('status', ['BORROWED', 'OVERDUE'])
            ->with(['student', 'item']);

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->whereHas('student', function ($s) use ($q) {
                    $s->where('student_name', 'like', "%{$q}%")
                        ->orWhere('phone_number', 'like', "%{$q}%");
                })
                    ->orWhereHas('item', function ($i) use ($q) {
                        $i->where('name', 'like', "%{$q}%"); // ✅ FIX: items.name
                    });
            });
        }

        $overdues = $query->orderBy('borrow_date', 'asc')
            ->paginate(10)
            ->appends($request->query());

        return view('backend.page.borrows.overdue', compact('overdues'));
    }
}
