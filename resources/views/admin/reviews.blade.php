@extends('admin.layout')
@section('title', 'Moderasi Ulasan')
@section('page-title', 'Moderasi Ulasan')
@section('page-subtitle', 'Tinjau dan moderasi ulasan produk dari pelanggan')

@section('content')
<div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10 flex-wrap gap-2">
        <h2 class="font-heading text-lg font-bold text-brown-dark">Semua Ulasan</h2>
        <div class="flex items-center gap-2">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cream border border-cream-border text-brown-dark">Total: {{ $reviews->total() }}</span>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 border border-amber-200 text-amber-700">Pending: {{ $pendingCount ?? 0 }}</span>
            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 border border-green-200 text-green-700">Disetujui: {{ $approvedCount ?? 0 }}</span>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex items-center gap-3 p-4 border-b border-cream-border bg-cream/20 flex-wrap">
        <select name="status" class="border border-cream-border rounded-xl px-3 py-2 text-xs text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
            <option value="">Semua Status</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
        </select>
        <select name="rating" class="border border-cream-border rounded-xl px-3 py-2 text-xs text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
            <option value="">Semua Rating</option>
            @for($r = 5; $r >= 1; $r--)
            <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ $r }} Bintang</option>
            @endfor
        </select>
        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white font-bold text-xs rounded-full shadow-gold transition-all duration-200 hover:bg-primary-hover hover:-translate-y-0.5 border-0 cursor-pointer"><i class="fas fa-filter mr-1"></i> Filter</button>
        @if(request()->hasAny(['status', 'rating']))
        <a href="{{ route('admin.reviews.index') }}" class="text-xs text-primary font-semibold hover:text-primary-hover transition-colors ml-2">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Pelanggan</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Produk</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-center">Rating</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Komentar</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-center">Status</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr class="hover:bg-cream-warm/20 transition-colors">
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="font-bold text-brown-dark text-sm">{{ $review->user->name ?? 'Pengguna' }}</div>
                        <div class="text-[10px] text-text-muted mt-0.5">{{ $review->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="font-medium text-brown-dark text-sm">{{ $review->product->name ?? '—' }}</div>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-center">
                        <div class="flex items-center justify-center gap-0.5 text-amber-400 text-xs">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <div class="text-[10px] text-text-muted mt-1">{{ $review->rating }}/5</div>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        @if($review->comment)
                            <div class="text-xs text-text-secondary leading-relaxed max-w-sm italic">"{{ $review->comment }}"</div>
                        @else
                            <span class="text-xs text-text-muted">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-center">
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $review->is_approved ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $review->is_approved ? 'Disetujui' : 'Menunggu' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="flex gap-2 justify-end items-center">
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border font-semibold transition-all cursor-pointer @if($review->is_approved) border-red-300 text-red-600 hover:bg-red-600 hover:text-white @else border-primary text-primary hover:bg-primary hover:text-white @endif">
                                    @if($review->is_approved)
                                        <i class="fas fa-eye-slash"></i> Tolak
                                    @else
                                        <i class="fas fa-check"></i> Setujui
                                    @endif
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Hapus ulasan ini permanen?')" class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 border-b border-cream-border/50 text-center">
                        <div class="py-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-star"></i></div>
                            <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Ulasan</h3>
                            <p class="text-xs text-text-secondary">Ulasan dari pelanggan akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reviews->hasPages())
    <div class="p-4 flex justify-end bg-white border-t border-cream-border">
        {{ $reviews->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

