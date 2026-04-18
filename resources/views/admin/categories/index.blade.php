{{--
    File frontend: admin/categories/index.blade.php
    Chuc nang: Trang CRUD danh muc gom hai phan tren cung mot man hinh: form tao moi ben trai va bang danh sach ben phai.
    Vai tro giao dien: Giup admin quan ly nhom mon an, thiet lap mo ta, thu tu hien thi va trang thai hoat dong cua tung danh muc.
    Tuong tac: Gui POST de tao, theo link sua, gui DELETE de xoa, va phan trang danh sach danh muc.
--}}
@extends('layouts.admin')

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Thêm danh mục</h1>
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="vstack gap-3">
                        @csrf
                        <div>
                            <label class="form-label" for="name">Tên danh mục</label>
                            <input class="form-control" id="name" name="name" required>
                        </div>
                        <div>
                            <label class="form-label" for="description">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div>
                            <label class="form-label" for="sort_order">Thứ tự</label>
                            <input class="form-control" id="sort_order" type="number" name="sort_order" value="0">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" id="is_active" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Đang hoạt động</label>
                        </div>
                        <button class="btn btn-dark" type="submit">Tạo danh mục</button>
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
                                <th>Tên</th>
                                <th>Slug</th>
                                <th>Thứ tự</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>{{ $category->is_active ? 'Đang hiển thị' : 'Đã ẩn' }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.categories.edit', $category) }}">Sửa</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline">
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
            <div class="mt-4">{{ $categories->links() }}</div>
        </div>
    </div>
@endsection

