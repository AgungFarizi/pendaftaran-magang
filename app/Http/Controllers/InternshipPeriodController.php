<?php

namespace App\Http\Controllers;

use App\Models\InternshipPeriod;
use Illuminate\Http\Request;

class InternshipPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = InternshipPeriod::withCount('proposals');

        // Filter aktif / nonaktif
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $periods = $query->latest()->paginate(10);

        return view('operator.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('operator.periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_registration' => 'required|date',
            'end_registration' => 'required|date|after:start_registration',
            'start_internship' => 'required|date|after_or_equal:end_registration',
            'end_internship' => 'required|date|after:start_internship',
            'quota' => 'required|integer|min:1',
            'departments' => 'nullable|array',
        ]);

        InternshipPeriod::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_registration' => $request->start_registration,
            'end_registration' => $request->end_registration,
            'start_internship' => $request->start_internship,
            'end_internship' => $request->end_internship,
            'quota' => $request->quota,
            'departments' => $request->departments,
            'is_active' => true, // default aktif saat dibuat
        ]);

        return redirect()->route('operator.periods.index')
            ->with('success', 'Periode magang berhasil dibuat!');
    }

    public function show(InternshipPeriod $period)
    {
        $period->load(['proposals.user']);
        return view('operator.periods.show', compact('period'));
    }

    public function edit(InternshipPeriod $period)
    {
        return view('operator.periods.edit', compact('period'));
    }

    public function update(Request $request, InternshipPeriod $period)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_registration' => 'required|date',
            'end_registration' => 'required|date|after:start_registration',
            'start_internship' => 'required|date|after_or_equal:end_registration',
            'end_internship' => 'required|date|after:start_internship',
            'quota' => 'required|integer|min:1',
            'departments' => 'nullable|array',
            'is_active' => 'required|boolean',
        ]);

        $period->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_registration' => $request->start_registration,
            'end_registration' => $request->end_registration,
            'start_internship' => $request->start_internship,
            'end_internship' => $request->end_internship,
            'quota' => $request->quota,
            'departments' => $request->departments,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('operator.periods.index')
            ->with('success', 'Periode magang berhasil diperbarui!');
    }

    public function destroy(InternshipPeriod $period)
    {
        if ($period->proposals()->count() > 0) {
            return redirect()->route('operator.periods.index')
                ->with('error', 'Tidak dapat menghapus periode yang memiliki proposal.');
        }

        $period->delete();

        return redirect()->route('operator.periods.index')
            ->with('success', 'Periode magang berhasil dihapus!');
    }

    public function toggleStatus(InternshipPeriod $period)
    {
        $period->update([
            'is_active' => !$period->is_active
        ]);

        return redirect()->route('operator.periods.index')
            ->with('success', 'Status periode berhasil diubah!');
    }

    // Public landing page
    public function publicIndex()
    {
        $periods = InternshipPeriod::where('is_active', true)
            ->where('end_registration', '>=', now())
            ->get();

        return view('public.periods', compact('periods'));
    }
}