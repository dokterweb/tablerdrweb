@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
            <div class="col-lg-12">
                <form action="{{ route('santris.update', $santri->id) }}" method="POST" enctype="multipart/form-data" class="card">
                @csrf
                @method('PUT')
                <div class="card-header bg-red-lt">
                    <h3 class="card-title">Edit Data</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label>Nama Santri</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $santri->user->name) }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label>Kelas</label>
                                <select class="form-select" name="kelas_id">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelasnyas as $p)
                                    <option value="{{ $p->id }}" {{ $santri->kelas_id == $p->id ? 'selected' : '' }}>{{ $p->nama_kelas }}</option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label>Kelompok</label>
                                <select class="form-select" name="kelompok_id">
                                    <option value="">Pilih kelompok</option>
                                    @foreach ($kelompoks as $p)
                                    <option value="{{ $p->id }}" {{ $santri->kelompok_id == $p->id ? 'selected' : '' }}>{{ $p->nama_kelompok }}</option>
                                    @endforeach
                                </select>
                                @error('kelompok_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label>Kelamin</label>
                                <select name="kelamin" class="form-select">
                                    <option value="laki-laki" {{ $santri->kelamin == 'laki-laki' ? 'selected' : '' }}>laki-laki</option>
                                    <option value="perempuan" {{ $santri->kelamin == 'perempuan' ? 'selected' : '' }}>perempuan</option>
                                </select>
                                @error('kelamin')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label >Email</label>
                                <input type="text" name="email" class="form-control" value="{{ old('email', $santri->user->email) }}" required>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label >Password</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                            <label for="avatar">Avatar</label>
                            <input type="file" class="form-control" name="avatar" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                @if ($santri->user->avatar && file_exists(public_path('storage/'.$santri->user->avatar)))
                                    <img src="{{ asset('storage/' . $santri->user->avatar) }}" alt="Avatar" class="img-thumbnail" style="width: 100px; height: 100px;">
                                @else
                                    <img src="{{ asset('storage/avatar/default-avatar.png') }}" alt="Default Avatar" class="img-thumbnail" style="width: 100px; height: 100px;">
                                @endif
                            </div>
                          </div>
                        </div>
                    </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-cyan-lt">
                        <h3 class="card-title">Data Santri</h3>
                    </div>
                    <div class="table-responsive p-3">
                        <table id="mytable" class="table table-vcenter card-table">
                        <thead>
                            <tr>
                            <th>#</th>
                            <th>Nama Santri</th>
                            <th>Kelas</th>
                            <th>Kelompok</th>
                            <th>Kelamin</th>
                            <th>Email</th>
                            <th class="w-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($santriView as $p)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$p->user->name}} </td>
                                    <td>{{$p->kelasnya->nama_kelas}} </td>
                                    <td>{{$p->kelompok->nama_kelompok}} </td>
                                    <td>{{$p->kelamin}} </td>
                                    <td>{{$p->user->email}} </td>
                                    <td class="d-flex align-items-center" style="gap: 5px;">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" data-bs-target="#modalnya" 
                                        onclick="showSantriDetails({{ $p->id }})">
                                            Show
                                        </button>
                                        <form method="POST" action="{{ route('santris.destroy', $p->id) }}" style="display: inline;" id="delete-form-{{ $p->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteConfirmation({{ $p->id }})">Del</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="6">No Data</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modalnya" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="santri-details">
            
            <!-- Konten Ustadz akan dimuat melalui JavaScript -->
            <div class="row">
                <div class="col-md-4">
                    <img id="santri-avatar" src="" alt="Avatar" class="img-fluid rounded-circle">
                </div>
                <div class="col-md-8">
                    <table class="table table-vcenter card-table">
                        <tr>
                            <td>Nama Siswa</td>
                            <td>:</td>
                            <td><span id="santri-name"></span></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>:</td>
                            <td><span id="santri-kelasnya"></span></td>
                        </tr>
                        <tr>
                            <td>Kelompok</td>
                            <td>:</td>
                            <td><span id="santri-kelompok"></span></td>
                        </tr>
                        <tr>
                            <td>Kelamin</td>
                            <td>:</td>
                            <td><span id="santri-kelamin"></span></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><span id="santri-email"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
          <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>


@endsection
@section('scripts')

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#mytable').DataTable({
            "processing": true,   // Menampilkan loading saat memproses data
            "serverSide": false,  // Tentukan apakah menggunakan server-side processing
            "paging": true,       // Menampilkan pagination
            "lengthChange": false // Menonaktifkan pengaturan jumlah baris per halaman
        });
    });

    function showSantriDetails(id) {
        // Melakukan request AJAX untuk mendapatkan detail santri berdasarkan ID
        $.ajax({
            url: '/santris/' + id, // Rute untuk mendapatkan detail santri berdasarkan ID
            type: 'GET',
            success: function(response) {
                // Mengisi modal dengan data santri
                $('#santri-name').text(response.name);
                $('#santri-kelasnya').text(response.kelasnya);
                $('#santri-kelompok').text(response.kelompok);
                $('#santri-kelamin').text(response.kelamin);
                $('#santri-email').text(response.email);
                
                // Menampilkan avatar jika ada
                if (response.avatar) {
                    $('#santri-avatar').attr('src', response.avatar); // Mengatur URL avatar
                } else {
                    $('#santri-avatar').attr('src', '/storage/avatar/default-avatar.png'); // Default avatar jika tidak ada
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    }

    function deleteConfirmation(id) {
        // SweetAlert2 konfirmasi
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirimkan form untuk menghapus data jika dikonfirmasi
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection