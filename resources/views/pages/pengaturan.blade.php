@extends('component.template')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="card">
                    @foreach ($pengaturan as $item)
                        <div class="card-header d-flex justify-content-between p-5">
                            <div class="header-title">
                                <h4 class="card-title">Informasi Pengguna</h4>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}">
                                    <span class="nav-icon flex-shrink-0"><i class="bi bi-gear fs-18"></i></span> <span
                                        class="nav-text">Setup</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="new-user-info">
                                <div class="col col-md mb-3">
                                    <div class="row ms-1">
                                        <label for="validationTooltip01" class="form-label">Logo Aplikasi</label>
                                    </div>
                                    <div class="row">
                                        @if ($item->logo)
                                            <img src="{{ asset('storage/' . $item->logo) }}" alt="User Photo"
                                                style="width: 20%">
                                        @else
                                            <div class="text-danger">
                                                Foto tidak tersedia
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <form>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="validationTooltip01" class="form-label">Nama Aplikasi</label>
                                            <input type="text" class="form-control" name="nama"
                                                value="{{ $item->nama }}" readonly>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="lname">Pimpinan</label>
                                            <input type="text" class="form-control" id="lname"
                                                value="{{ $item->pimpinan }}" readonly>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="add1">Alamat:</label>
                                            <input type="text" class="form-control" id="add1"
                                                value="{{ $item->alamat }}" readonly>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label" for="add2">No Telepon:</label>
                                            <input type="text" class="form-control" id="add2"
                                                value="{{ $item->nohp }}" readonly>
                                        </div>
                                        <hr>
                                    </div>
                                </form>

                                {{-- modalEdit --}}
                                <div class="modal modal-lg fade" id="editModal{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title text-white">Edit Data Simpanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="/pengaturan/edit/{{ $item->id }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="validationTooltip01" class="form-label">Nama
                                                                Aplikasi</label>
                                                            <input type="text" class="form-control" name="nama"
                                                                value="{{ $item->nama }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="form-label" for="lname">Pimpinan</label>
                                                            <input type="text" class="form-control" id="lname"
                                                                name="pimpinan" value="{{ $item->pimpinan }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="form-label" for="add1">Alamat:</label>
                                                            <input type="text" class="form-control" id="add1"
                                                                name="alamat" value="{{ $item->alamat }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="form-label" for="add2">No Telepon:</label>
                                                            <input type="text" class="form-control" id="add2"
                                                                name="nohp" value="{{ $item->nohp }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="form-label" for="add2">Logo
                                                                Aplikasi:</label>
                                                            <input type="file" class="form-control" name="logo"
                                                                id="logoInput" onchange="previewLogo()">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            @if ($item->logo)
                                                                <img id="logoPreview"
                                                                    src="{{ asset('storage/' . $item->logo) }}"
                                                                    alt="User Photo" style="width: 50%">
                                                            @else
                                                                <div class="text-danger">
                                                                    Foto tidak tersedia
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <hr>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"
                                                        class="btn btn-light-200 text-danger btn-sm px-2"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm  px-2">Simpan
                                                        Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
    <script>
        function previewLogo() {
            var input = document.getElementById('logoInput');
            var preview = document.getElementById('logoPreview');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
