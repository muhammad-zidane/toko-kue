@if ($paginator->hasPages())
<style>
    .pgn { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; margin-top: 24px; font-family: 'Plus Jakarta Sans', sans-serif; }
    .pgn-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid #D1C0B8; background: #fff; color: #3D2B1F; text-decoration: none; transition: all 0.2s; cursor: pointer; }
    .pgn-btn:hover { border-color: #E4849A; color: #E4849A; }
    .pgn-btn.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
    .pgn-num { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid #D1C0B8; background: #fff; color: #3D2B1F; text-decoration: none; transition: all 0.2s; }
    .pgn-num:hover { border-color: #E4849A; color: #E4849A; }
    .pgn-num.active { background: #E4849A; border-color: #E4849A; color: #fff; }
    .pgn-dots { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; font-size: 13px; color: #9CA3AF; }
    .pgn-info { font-size: 12px; color: #9CA3AF; text-align: center; margin-top: 8px; }
</style>
<nav role="navigation" aria-label="Pagination">
    <div class="pgn">

        {{-- Tombol Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pgn-btn disabled">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pgn-btn">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                Sebelumnya
            </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pgn-dots">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pgn-num active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pgn-num">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pgn-btn">
                Berikutnya
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </a>
        @else
            <span class="pgn-btn disabled">
                Berikutnya
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </span>
        @endif

    </div>

    {{-- Info halaman --}}
    @if ($paginator->firstItem())
    <p class="pgn-info">Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</p>
    @endif
</nav>
@endif
