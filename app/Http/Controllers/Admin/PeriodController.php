<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodController extends Controller
{
    public function index(Request $request)
    {
        $periods = Period::query()->latest('id')->get();
        $selectedId = (int) $request->query('period_id', 0);
        $createMode = $request->boolean('create');

        $activePeriod = Period::query()->active()->latest('id')->first();
        $period = $createMode
            ? new Period([
                'wave' => 1,
                'is_active' => $periods->isEmpty(),
                'payment_proof_label' => 'Bukti Pembayaran Uang Pendaftaran (Rp. 150.000)',
                'payment_proof_note' => 'No Rek. (BSI 7145-1777-28) Kode Bank 451 An. Al Marwa SPP',
                'payment_agreement_note' => 'Saya bersedia memenuhi kewajiban pembayaran biaya pendidikan sesuai waktu yang ditentukan, termasuk ketentuan tanda jadi, infak, dan aturan pengembalian dana. Dengan mencentang, berarti saya telah membaca dan menyetujui seluruh ketentuan pembayaran lainnya yang ditetapkan ma\'had.',
            ])
            : ($periods->firstWhere('id', $selectedId)
                ?? $activePeriod
                ?? $periods->first()
                ?? new Period([
                    'wave' => 1,
                    'is_active' => true,
                    'payment_proof_label' => 'Bukti Pembayaran Uang Pendaftaran (Rp. 150.000)',
                    'payment_proof_note' => 'No Rek. (BSI 7145-1777-28) Kode Bank 451 An. Al Marwa SPP',
                    'payment_agreement_note' => 'Saya bersedia memenuhi kewajiban pembayaran biaya pendidikan sesuai waktu yang ditentukan, termasuk ketentuan tanda jadi, infak, dan aturan pengembalian dana. Dengan mencentang, berarti saya telah membaca dan menyetujui seluruh ketentuan pembayaran lainnya yang ditetapkan ma\'had.',
                ]));

        return view('admin.periods.index', [
            'periods' => $periods,
            'period' => $period,
            'createMode' => $createMode,
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'period_id' => ['nullable', 'integer', 'exists:periods,id'],
            'name' => ['required', 'string', 'max:255'],
            'wave' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'registration_open_date' => ['nullable', 'date'],
            'registration_close_date' => ['nullable', 'date', 'after_or_equal:registration_open_date'],
            'exam_date' => ['nullable', 'date'],
            'announce_date' => ['nullable', 'date'],
            'down_payment_deadline' => ['nullable', 'date'],
            'wa_group_ikhwan' => ['nullable', 'url', 'max:255'],
            'wa_group_akhwat' => ['nullable', 'url', 'max:255'],
            'admin_contact_1' => ['nullable', 'string', 'max:255'],
            'admin_contact_2' => ['nullable', 'string', 'max:255'],
            'information_note' => ['nullable', 'string', 'max:2000'],
            'payment_proof_label' => ['nullable', 'string', 'max:255'],
            'payment_proof_note' => ['nullable', 'string', 'max:2000'],
            'payment_agreement_note' => ['nullable', 'string', 'max:4000'],
        ], [
            'registration_close_date.after_or_equal' => 'Tanggal tutup pendaftaran harus sama atau setelah tanggal buka.',
        ]);

        $period = null;
        if (!empty($validated['period_id'])) {
            $period = Period::query()->findOrFail($validated['period_id']);
        } else {
            $period = new Period();
        }

        $period->fill([
            'name' => $validated['name'],
            'wave' => $validated['wave'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'registration_open_date' => $validated['registration_open_date'] ?? null,
            'registration_close_date' => $validated['registration_close_date'] ?? null,
            'exam_date' => $validated['exam_date'] ?? null,
            'announce_date' => $validated['announce_date'] ?? null,
            'down_payment_deadline' => $validated['down_payment_deadline'] ?? null,
            'wa_group_ikhwan' => $validated['wa_group_ikhwan'] ?? null,
            'wa_group_akhwat' => $validated['wa_group_akhwat'] ?? null,
            'admin_contact_1' => $validated['admin_contact_1'] ?? null,
            'admin_contact_2' => $validated['admin_contact_2'] ?? null,
            'information_note' => $validated['information_note'] ?? null,
            'payment_proof_label' => $validated['payment_proof_label'] ?? null,
            'payment_proof_note' => $validated['payment_proof_note'] ?? null,
            'payment_agreement_note' => $validated['payment_agreement_note'] ?? null,
        ]);
        $period->save();

        if ($period->is_active) {
            Period::query()
                ->where('id', '!=', $period->id)
                ->update(['is_active' => false]);
        }

        return redirect()
            ->route('admin.periods.index', ['period_id' => $period->id])
            ->with('success', 'Pengaturan periode berhasil disimpan.');
    }

    public function activate(Period $period)
    {
        DB::transaction(function () use ($period) {
            Period::query()->update(['is_active' => false]);
            $period->update(['is_active' => true]);
        });

        return redirect()
            ->route('admin.periods.index', ['period_id' => $period->id])
            ->with('success', 'Periode berhasil diaktifkan.');
    }

    public function destroy(Period $period)
    {
        $period->delete();

        return redirect()
            ->route('admin.periods.index')
            ->with('success', 'Periode berhasil dihapus.');
    }
}
