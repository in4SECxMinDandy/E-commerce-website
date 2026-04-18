{{--
    File frontend: admin/visit-sessions/index.blade.php
    Chuc nang: Quan ly cac visit session dung cho guest chat qua QR.
    Vai tro giao dien: Admin tao phien moi, xem QR SVG, link chat, thoi diem het han va trang thai tung phien.
    Tuong tac: Form tao session, nut vo hieu hoa, nut xoa, bang danh sach co phan trang; phan QR duoc backend render san vao view.
--}}
@extends('layouts.admin')

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Tạo visit session</h1>
                    <form method="POST" action="{{ route('admin.visit-sessions.store') }}" class="vstack gap-3">
                        @csrf
                        <div>
                            <label class="form-label" for="label">Nhãn session</label>
                            <input class="form-control" id="label" name="label" required>
                        </div>
                        <div>
                            <label class="form-label" for="expires_at">Hết hạn</label>
                            <input class="form-control" id="expires_at" type="datetime-local" name="expires_at">
                        </div>
                        <button class="btn btn-dark" type="submit">Tạo QR chat</button>
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
                                <th>Phiên</th>
                                <th>QR</th>
                                <th>Trạng thái</th>
                                <th>Hết hạn</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($visitSessions as $visitSession)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $visitSession->label }}</div>
                                        <a href="{{ $chatUrls[$visitSession->id] }}" target="_blank" class="small">
                                            {{ $chatUrls[$visitSession->id] }}
                                        </a>
                                    </td>
                                    <td style="width: 160px;">
                                        <div class="bg-white border rounded-3 p-2 d-inline-flex">
                                            {!! $qrSvgs[$visitSession->id] !!}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $visitSession->status->value === 'active' ? 'success' : ($visitSession->status->value === 'expired' ? 'warning' : 'secondary') }}">
                                            {{ strtoupper($visitSession->status->value) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($visitSession->expires_at)->format('d/m/Y H:i') ?: 'Không giới hạn' }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.visit-sessions.disable', $visitSession) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-dark" type="submit">Vô hiệu hóa</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.visit-sessions.destroy', $visitSession) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">Chưa có visit session nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $visitSessions->links() }}</div>
        </div>
    </div>
@endsection

