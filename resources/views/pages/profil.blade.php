@extends('layouts.app')
@section('content')
    <main class="main-wrapper">
        <div class="container-fluid">
            <div class="inner-contents">
                <div class="card">
                    <div class="card-header d-flex justify-content-between p-5">
                        <div class="header-title">
                            <h4 class="card-title">Informasi Pengguna</h4>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editprofile{{ $user->id }}">
                                <span class="nav-icon flex-shrink-0"><i class="bi bi-gear fs-18"></i></span> <span
                                    class="nav-text">Setup</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="col col-md mb-3">
                                <div class="row ms-1">
                                    <label for="validationTooltip01" class="form-label">Foto</label>
                                </div>
                                <div class="row">
                                    @if ($user->foto)
                                        <img src="{{ asset('storage/' . $user->foto) }}" alt="User Photo"
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
                                        <label for="validationTooltip01" class="form-label">Nama</label>
                                        <input type="text" class="form-control" name="nama"
                                            value="{{ $user->name }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="lname">Email:</label>
                                        <input type="text" class="form-control" id="lname"
                                            value="{{ $user->email }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="add1">Alamat:</label>
                                        <input type="text" class="form-control" id="add1"
                                            value="{{ $user->alamat }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="add2">No Telepon:</label>
                                        <input type="text" class="form-control" id="add2"
                                            value="{{ $user->nohp }}" readonly>
                                    </div>
                                    <hr>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal modal-lg fade" id="editprofile{{ $user->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white">Setup Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="form-group col-md-6 mb-3">
                                            <label class="form-label">Nama</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $user->name }}" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ $user->email }}" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label class="form-label">Alamat</label>
                                            <input type="text" class="form-control" name="alamat"
                                                value="{{ $user->alamat }}">
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label class="form-label">No Telepon</label>
                                            <input type="text" class="form-control" name="nohp"
                                                value="{{ $user->nohp }}">
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label class="form-label">Password Baru</label>
                                            <input type="password" class="form-control" name="password"
                                                placeholder="(Kosong jika tidak diubah)">
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label class="form-label">Foto</label>
                                            <input type="file" class="form-control" name="foto"
                                                accept=".jpg,.jpeg,.png">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-200 text-danger btn-sm px-2"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm px-2">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
