@props(['transactions', 'editable' => false])
@if($transactions->isEmpty())
    <div class="empty-state">
        <span class="eyebrow">BUKU KAS ANDA</span>
        <h3>{{ request()->filled('q') || request()->filled('type') || request()->filled('month') ? 'Tidak ada transaksi yang sesuai' : 'Belum ada transaksi' }}</h3>
        <p>Mulai catat pemasukan atau pengeluaran Anda. Semua catatan akan tersimpan di sini.</p>
        <a class="button" href="{{ route('transactions.create') }}">+ Tambah Transaksi</a>
    </div>
@else
    <div class="table-scroll" role="region" aria-label="Daftar transaksi" tabindex="0">
        <table>
            <thead><tr><th scope="col">Tanggal</th><th scope="col">Keterangan</th><th scope="col">Kategori</th><th scope="col">Jenis Transaksi</th><th scope="col" class="number">Nominal</th>@if($editable)<th scope="col" class="number">Aksi</th>@endif</tr></thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td class="date">{{ $transaction->transaction_date->translatedFormat('d M Y') }}</td>
                        <td class="description">{{ $transaction->description }}</td>
                        <td>{{ $transaction->category }}</td>
                        <td><span class="type {{ $transaction->type }}">{{ $transaction->typeLabel() }}</span></td>
                        <td class="number money {{ $transaction->type }}">{{ $transaction->type === 'income' ? '+' : '−' }} <x-rupiah :amount="$transaction->amount" /></td>
                        @if($editable)
                            <td><div class="row-actions">
                                <a href="{{ route('transactions.edit', $transaction) }}" aria-label="Edit transaksi {{ $transaction->description }}">Edit</a>
                                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="delete-link" aria-label="Hapus transaksi {{ $transaction->description }}">Hapus</button>
                                </form>
                            </div></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
