@extends('component.template')
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
                            <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="modal"
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
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="cname">Iuran Wajib:</label>
                                        <input type="text" class="form-control" id="cname"
                                            value="{{ $user->iuran_wajib }}" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label" for="uname">Iuran Pokok:</label>
                                        <input type="text" class="form-control" id="uname"
                                            value="{{ $user->iuran_pokok }}" readonly>
                                    </div>
                                    <hr>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
