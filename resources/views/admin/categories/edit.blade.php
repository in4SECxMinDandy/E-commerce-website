{{--
    File frontend: admin/categories/edit.blade.php
    Chuc nang: Man hinh chinh sua thong tin mot danh muc da ton tai.
    Vai tro giao dien: Cho phep admin cap nhat ten, mo ta, thu tu sap xep va trang thai hien thi cua danh muc.
    Tuong tac: Form gui PUT/PATCH den route cap nhat va do lai gia tri cu thong qua old() de tranh mat du lieu khi validate loi.
--}}
@extends('layouts.admin')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h4 mb-4">Sửa danh mục</h1>
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label" for="name">Tên danh mục</label>
                    <input class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="sort_order">Thứ tự</label>
                    <input class="form-control" id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>
                <div class="col-12 form-check ms-1">
                    <input class="form-check-input" id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                    <label class="form-check-label" for="is_active">Đang hoạt động</label>
                </div>
                <div class="col-12">
                    <button class="btn btn-dark" type="submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

