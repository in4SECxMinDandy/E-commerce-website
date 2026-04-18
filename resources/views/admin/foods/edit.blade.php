{{--
    File frontend: admin/foods/edit.blade.php
    Chuc nang: Man hinh chinh sua chi tiet thong tin mon an.
    Vai tro giao dien: Cap nhat cac thuoc tinh nghiep vu cua mon nhu danh muc, gia, ton kho, mo ta, anh moi va trang thai hien thi.
    Tuong tac: Form gui du lieu da ton tai theo method PUT, ho tro upload lai anh va giu gia tri cu qua old().
--}}
@extends('layouts.admin')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h4 mb-4">Sửa món ăn</h1>
            <form method="POST" action="{{ route('admin.foods.update', $food) }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label" for="category_id">Danh mục</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $food->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="name">Tên món</label>
                    <input class="form-control" id="name" name="name" value="{{ old('name', $food->name) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="short_description">Mô tả ngắn</label>
                    <input class="form-control" id="short_description" name="short_description" value="{{ old('short_description', $food->short_description) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="price">Giá</label>
                    <input class="form-control" id="price" type="number" step="0.01" name="price" value="{{ old('price', $food->price) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="stock">Tồn kho</label>
                    <input class="form-control" id="stock" type="number" name="stock" value="{{ old('stock', $food->stock) }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Mô tả chi tiết</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $food->description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" for="image">Ảnh mới</label>
                    <input class="form-control" id="image" type="file" name="image" accept="image/*">
                </div>
                <div class="col-12 d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" id="is_available" type="checkbox" name="is_available" value="1" @checked(old('is_available', $food->is_available))>
                        <label class="form-check-label" for="is_available">Đang bán</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" id="is_featured" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $food->is_featured))>
                        <label class="form-check-label" for="is_featured">Featured</label>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-dark" type="submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

