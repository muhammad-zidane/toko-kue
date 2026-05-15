@extends('admin.layout')
@section('title', 'Moderasi Ulasan')
@section('page-title', 'Moderasi Ulasan')
@section('page-subtitle', 'Tinjau dan moderasi ulasan produk dari pelanggan')

@push('styles')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); flex-wrap: wrap; gap: 12px; }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .header-stats { display: flex; gap: 10px; align-items: center; }
    .stat-chip { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .chip-total { background: var(--cream); color: var(--brown-dark); }
    .chip-pending { background: #FEF3C7; color: #D97706; }
    .chip-approved { background: #DCFCE7; color: #16A34A; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .reviewer-name { font-weight: 700; color: var(--text-dark); }
    .reviewer-date { font-size: 11px; color: var(--gray); margin-top: 2px; }
    .product-name { font-weight: 600; color: var(--text-dark); font-size: 12px; max-width: 150px; }
    .stars { color: #F59E0B; font-size: 13px; letter-spacing: 1px; }
    .review-text { font-size: 12px; color: var(--gray); max-width: 220px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .status-approved { background: #DCFCE7; color: #16A34A; }
    .status-pending { background: #FEF3C7; color: #D97706; }
    .action-group { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
    .btn-approve { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #DCFCE7; color: #16A34A; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-unapprove { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEF3C7; color: #D97706; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-approve:hover, .btn-unapprove:hover { opacity: 0.8; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover { opacity: 0.8; }
    .filter-bar { padding: 16px 20px; border-bottom: 1px solid #EDE0D4; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .filter-select { border: 1.5px solid #EDE0D4; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; background: white; cursor: pointer; }
    .filter-select:focus { border-color: var(--pink); }
    .btn-filter { background: var(--brown-dark); color: white; border: none; border-radius: 8px; padding: 8px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Semua Ulasan</h2>
        <div class="header-stats">
            <span class="stat-chip chip-total">Total: {{ $reviews->total() }}</span>
            <span class="stat-chip chip-pending">Pending: {{ $pendingCount ?? 0 }}</span>
            <span class="stat-chip chip-approved">Disetujui: {{ $approvedCount ?? 0 }}</span>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="filter-bar">
        <select name="status" class="filter-select">
            <option value="">Semua Status</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
        </select>
        <select name="rating" class="filter-select">
            <option value="">Semua Rating</option>
            @for($r = 5; $r >= 1; $r--)
            <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} Bintang</option>
            @endfor
        </select>
        <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
        @if(request()->hasAny(['status', 'rating']))
        <a href="{{ route('admin.reviews.index') }}" style="font-size:12px;color:var(--pink);font-weight:600;text-decoration:none;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th style="text-align:center;">Rating</th>
                    <th>Komentar</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td>
                        <div class="reviewer-name">{{ $review->user->name ?? 'Pengguna' }}</div>
                        <div class="reviewer-date">{{ $review->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="product-name">{{ $review->product->name ?? '—' }}</div>
                    </td>
                    <td style="text-align:center;">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <div style="font-size:11px;color:var(--gray);margin-top:2px;">{{ $review->rating }}/5</div>
                    </td>
                    <td>
                        @if($review->comment)
                            <div class="review-text">"{{ $review->comment }}"</div>
                        @else
                            <span style="font-size:12px;color:var(--gray);">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $review->is_approved ? 'status-approved' : 'status-pending' }}">
                            {{ $review->is_approved ? 'Disetujui' : 'Menunggu' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="{{ $review->is_approved ? 'btn-unapprove' : 'btn-approve' }}">
                                    @if($review->is_approved)
                                        <i class="fas fa-eye-slash"></i> Tolak
                                    @else
                                        <i class="fas fa-check"></i> Setujui
                                    @endif
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Hapus ulasan ini permanen?')">
                                @csrf @method('DELETE')
                                <button class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-star" style="color:var(--pink)"></i></div>
                            <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">Belum Ada Ulasan</h3>
                            <p style="font-size:12px;">Ulasan dari pelanggan akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
    <div style="padding:16px 20px;display:flex;justify-content:flex-end;">
        {{ $reviews->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

