@if($paginator->hasPages())
<nav class="pagination" aria-label="Navigasi halaman transaksi">
    <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
    <div>
        @if($paginator->onFirstPage())<span aria-disabled="true">Sebelumnya</span>@else<a rel="prev" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>@endif
        @if($paginator->hasMorePages())<a rel="next" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>@else<span aria-disabled="true">Berikutnya</span>@endif
    </div>
</nav>
@endif
