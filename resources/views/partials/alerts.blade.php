{{--
    File frontend: partials/alerts.blade.php
    Chuc nang: Partial hien thong bao thanh cong va danh sach loi validate.
    Vai tro giao dien: Dat o layout de moi trang co the thong nhat cach hien feedback sau thao tac form.
    Tuong tac: Doc session('status') va $errors de render alert Bootstrap tu dong.
--}}
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-2">Có lỗi xảy ra:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

