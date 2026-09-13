<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdvokatPendamping;
use App\Models\LawFirm;
use App\Models\User;
use App\Models\VerifikasiChecklist;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisterFirmController extends Controller
{
    public function create(): View
    {
        return view('auth.register-firm');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nama_firm' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:500'],
            'sk_kemenkumham' => ['nullable', 'string', 'max:100'],
            'setara_kantor_advokat' => ['boolean'],
            'kuota_maks' => ['required', 'integer', 'min:1', 'max:50'],
            'nama_pendamping' => ['required', 'string', 'max:255'],
            'kta_nomor' => ['required', 'string', 'max:50'],
            'pengalaman_tahun' => ['required', 'integer', 'min:5', 'max:50'],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'law_firm',
            ]);

            $firm = LawFirm::create([
                'nama' => $request->nama_firm,
                'alamat' => $request->alamat,
                'sk_kemenkumham' => $request->sk_kemenkumham,
                'setara_kantor_advokat' => $request->boolean('setara_kantor_advokat'),
                'kuota_maks' => $request->kuota_maks,
                'status_verifikasi' => 'menunggu',
            ]);

            $pendamping = AdvokatPendamping::create([
                'law_firm_id' => $firm->id,
                'user_id' => $user->id,
                'nama' => $request->nama_pendamping,
                'kta_nomor' => $request->kta_nomor,
                'kta_aktif' => true,
                'pengalaman_tahun' => $request->pengalaman_tahun,
            ]);

            VerifikasiChecklist::insert([
                ['checkable_type' => LawFirm::class, 'checkable_id' => $firm->id, 'label' => 'Domisili kantor di wilayah DPC Jakbar', 'is_checked' => false, 'created_at' => now(), 'updated_at' => now()],
                ['checkable_type' => LawFirm::class, 'checkable_id' => $firm->id, 'label' => 'KTA pendamping aktif', 'is_checked' => false, 'created_at' => now(), 'updated_at' => now()],
                ['checkable_type' => LawFirm::class, 'checkable_id' => $firm->id, 'label' => 'SK Kemenkumham terverifikasi', 'is_checked' => false, 'created_at' => now(), 'updated_at' => now()],
            ]);

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect()->route('firm.dashboard')->with('status', 'Pendaftaran berhasil. Menunggu verifikasi dari Admin DPC.');
    }
}
