@extends('layouts.app')
@section('title', 'Edit Posko')
@section('subtitle', 'Perbarui informasi posko ' . $shelter->name)

@section('page-actions')
    <x-ui.back-button :useHistory="true" />
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        @include('admin.shelter._form')
        @include('admin.shelter._sidebar')
    </div>
</div>

@include('admin.shelter._scripts')
@endsection
