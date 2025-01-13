<?php

namespace App\Http\Controllers;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {

        $query = Karyawan::query();
        $query->select('karyawan.*', 'nama_dept');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->orderBy('nama_lengkap');
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }
        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        $karyawan = $query->paginate(10);

        $departemen = DB::table('departemen')->get();
        return view('karyawan.index', compact('karyawan', 'departemen'));
    }

    public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'nik' => 'required|unique:karyawan,nik',
        'nama_lengkap' => 'required|string',
        'jabatan' => 'required|string',
        'no_hp' => 'required|string',
        'kode_dept' => 'required|string',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
    ]);

    // Get the data from the request
    $nik = $request->nik;
    $nama_lengkap = $request->nama_lengkap;
    $jabatan = $request->jabatan;
    $no_hp = $request->no_hp;
    $kode_dept = $request->kode_dept;
    $password = Hash::make('1234');
    
    // Handle file upload (foto)
    if ($request->hasFile('foto')) {
        $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
    } else {
        $foto = null;
    }

    try {
        // Prepare the data for insertion
        $data = [
            'nik' => $nik,
            'nama_lengkap' => $nama_lengkap,
            'jabatan' => $jabatan,
            'no_hp' => $no_hp,
            'kode_dept' => $kode_dept,
            'foto' => $foto,
            'password' => $password
        ];

        // Insert data into the database
        $simpan = DB::table('karyawan')->insert($data);

        // If insertion is successful, handle the file upload
        if ($simpan) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                // Store the file in the defined folder
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            // Log the failure
            \Log::error('Failed to insert data into karyawan table');
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    } catch (\Exception $e) {
        // Log the detailed exception message
        \Log::error("Data Gagal Disimpan. Error: " . $e->getMessage());
        
        // Return the error message to the user
        return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
    }
}


    public function edit(Request $request)
    {
        $nik = $request->nik;
        $departemen = DB::table('departemen')->get();
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('karyawan.edit', compact('departemen', 'karyawan'));
    }
    
    public function update($nik, Request $request)
    {
        $nik = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $password = Hash::make('1234');
        $old_foto = $request->old_foto;

        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data = [
                    'nama_lengkap' => $nama_lengkap,
                    'jabatan' => $jabatan,
                    'no_hp' => $no_hp, 
                    'kode_dept' => $kode_dept,
                    'foto' => $foto,
                    'password' => $password
            ];
            $update = DB::table('karyawan')->where('nik',$nik)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $folderPathold = "public/uploads/karyawan/" . $old_foto;
                    Storage::delete($folderPathold);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' =>'Data Berhasil Diupdate']);
            }
        } catch (\Exception $e) {
            //dd($e->message);
            return Redirect::back()->with(['warning' =>'Data Gagal Diupdate']);
        }
    }

    public function delete($nik){
        $delete = DB::table('karyawan')->where('nik', $nik)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['success' => 'Data Gagal Dihapus']);
        }
    }
}
