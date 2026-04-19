<?php
// app/Http/Controllers/Admin/PengaturanController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingAplikasi;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        /** @var \Illuminate\Support\Collection $settings */
        $settings = SettingAplikasi::orderBy('grup')->orderBy('id')->get()->groupBy('grup');
        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            SettingAplikasi::where('key', $key)->update(['value' => $value]);
        }

        AktivitasLog::catat('update_pengaturan', 'Update pengaturan aplikasi');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
