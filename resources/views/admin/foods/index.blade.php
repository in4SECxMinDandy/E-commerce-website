{{--
    File frontend: admin/foods/index.blade.php
    Chuc nang: Trang CRUD mon an gom form tao mon moi va bang danh sach mon hien co.
    Vai tro giao dien: Cho phep admin gan danh muc, nhap gia, ton kho, mo ta ngan, anh dai dien va cac co trang thai ban/featured.
    Tuong tac: Form tao gui multipart/form-data de upload anh; bang danh sach co nut sua, xoa va phan trang.
--}}
@extends('layouts.admin')

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Thêm món ăn</h1>
                    <form method="POST" action="{{ route('admin.foods.store') }}" enctype="multipart/form-data" class="vstack gap-3">
                        @csrf
                        <div>
                            <label class="form-label" for="category_id">Danh mục</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="name">Tên món</label>
                            <input class="form-control" id="name" name="name" required>
                        </div>
                        <div>
                            <label class="form-label" for="short_description">Mô tả ngắn</label>
                            <input class="form-control" id="short_description" name="short_description">
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label" for="price">Giá</label>
                                <input class="form-control" id="price" type="number" step="0.01" name="price" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="stock">Tồn kho</label>
                                <input class="form-control" id="stock" type="number" name="stock" required>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="image">Ảnh món ăn</label>
                            <input class="form-control" id="image" type="file" name="image" accept="image/*">
                        </div>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" id="is_available" type="checkbox" name="is_available" value="1" checked>
                                <label class="form-check-label" for="is_available">Đang bán</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" id="is_featured" type="checkbox" name="is_featured" value="1">
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
                        <button class="btn btn-dark" type="submit">Tạo món</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Món</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($foods as $food)
                                <tr>
                                    <td>{{ $food->name }}</td>
                                    <td>{{ $food->category?->name }}</td>
                                    <td>{{ number_format((float) $food->price, 0, ',', '.') }} đ</td>
                                    <td>{{ $food->stock }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.foods.edit', $food) }}">Sửa</a>
                                        <form method="POST" action="{{ route('admin.foods.destroy', $food) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $foods->links() }}</div>
        </div>
    </div>
@endsection

