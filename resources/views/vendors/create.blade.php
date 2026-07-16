@extends('layouts.app')

@section('title', 'Create Vendor | Unibs Tools')

@section('content')

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">

                <div class="card shadow-sm border-0 bg-white">

                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-dark">
                            <i class="fa fa-handshake me-2 text-primary"></i>
                            Create Vendor
                        </h5>
                    </div>

                    <div class="card-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm border-0 mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('vendors.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="vendor_name" class="form-label fw-bold text-dark">Vendor Name <span class="text-danger">*</span></label>
                                <input type="text" name="vendor_name" id="vendor_name" class="form-control shadow-sm" placeholder="Enter Vendor Name" required>
                            </div>

                            <div class="mb-3">
                                <label for="contact_person" class="form-label fw-bold text-dark">Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control shadow-sm" placeholder="Enter Contact Person">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold text-dark">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control shadow-sm" placeholder="Enter Email Address">
                            </div>

                            <div class="mb-3">
                                <label for="mobile_no" class="form-label fw-bold text-dark">Mobile No</label>
                                <input type="text" name="mobile_no" id="mobile_no" class="form-control shadow-sm" placeholder="Enter Mobile Number">
                            </div>

                            <div class="mb-3">
                                <label for="gst" class="form-label fw-bold text-dark">GST Number</label>
                                <input type="text" name="gst" id="gst" class="form-control shadow-sm" placeholder="Enter GST Number">
                            </div>

                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <a href="{{ route('vendors.index') }}" class="btn btn-light px-4">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="fa fa-save me-1"></i>
                                    Create
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection
