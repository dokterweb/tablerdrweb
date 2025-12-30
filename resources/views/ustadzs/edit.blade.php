@extends('layouts.app')

@section('content')
  <div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <form action="{{route('ustadzs.update', $ustadz->id)}}" method="POST" enctype="multipart/form-data" class="card">
              @csrf
              @method('PUT')
              <div class="card-header bg-red-lt">
                <h3 class="card-title">Edit Data</h3>
              </div>
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label>Nama Ustadz / Ustadzah</label>
                              <input type="text" name="name" class="form-control" value="{{$ustadz->user->name}}">
                              @error('name')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label>Kelompok</label>
                              <select class="form-select" name="kelompok_id">
                                  <option value="">Pilih kelompok</option>
                                  @foreach ($kelompoks as $p)
                                  <option value="{{ $p->id }}" {{ $ustadz->kelompok_id == $p->id ? 'selected' : '' }}>{{ $p->nama_kelompok }}</option>
                                  @endforeach
                              </select>
                              @error('kelompok_id')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-4 mb-3">
                          <div class="form-group">
                              <label>Kelamin</label>
                              <select name="kelamin" class="form-select">
                                  <option value="laki-laki" {{ $ustadz->kelamin == 'laki-laki' ? 'selected' : '' }}>laki-laki</option>
                                  <option value="perempuan" {{ $ustadz->kelamin == 'perempuan' ? 'selected' : '' }}>perempuan</option>
                              </select>
                              @error('kelamin')
                                  <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                      <div class="col-md-3 mb-3">
                          <div class="form-group">
                              <label >Email</label>
                              <input type="text" name="email" class="form-control" value="{{$ustadz->user->email}}">
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
                          @if ($ustadz->user->avatar && file_exists(public_path('storage/'.$ustadz->user->avatar)))
                              <img src="{{ asset('storage/' . $ustadz->user->avatar) }}" alt="Avatar" class="img-thumbnail" style="width: 100px; height: 100px;">
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
                <h3 class="card-title">Data Ustadz</h3>
              </div>
              <div class="table-responsive p-3">
                <table id="mytable" class="table table-vcenter card-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama Ustadz</th>
                      <th>Kelompok</th>
                      <th>Kelamin</th>
                      <th>Email</th>
                      {{-- <th class="w-1">Action</th> --}}
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($ustadzview as $p)
                            <tr>
                              <td>{{$loop->iteration}}</td>
                              <td>{{$p->user->name}} </td>
                              <td>{{$p->kelompok->nama_kelompok}} </td>
                              <td>{{$p->kelamin}} </td>
                              <td>{{$p->user->email}} </td>
                                
                            </tr>
                        @empty
                        <tr>
                            <td colspan="3">No Data</td>
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