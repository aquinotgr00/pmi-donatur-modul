<table border="1" cellpadding="10">
    <thead>
    <tr>
        <th><b>Tanggal Donasi</b></th>
        <th><b>ID-Transaksi</b></th>
        <th><b>Nama</b></th>
        <th><b>Judul Donasi</b></th>
        <th><b>Tipe Donasi</b></th>
        <th><b>Status</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($donations as $donation)
        <tr>
            <td>{{ $donation->created_at->format('Y-m-d') }}</td>
            <td>{{ $donation->invoice_id }}</td>
            <td>{{ $donation->name }}</td>
            <td>{{ (isset($donation->campaign->title))? $donation->campaign->title : '' }}</td>
            <td>{{ (isset($donation->campaign->getType->name))? $donation->campaign->getType->name : '' }}</td>
            <td>{{ config('donation.status')[$donation->status] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
