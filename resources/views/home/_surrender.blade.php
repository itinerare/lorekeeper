<tr>
    <td class="text-break"><a href="{{ $surrender->url }}">{{ $surrender->url }}</a></td>
    <td>{!! format_date($surrender->created_at) !!}</td>
    <td>
        <span class="badge badge-{{ $surrender->status == 'Pending' ? 'secondary' : ($surrender->status == 'Approved' ? 'success' : 'danger') }}">{{ $surrender->status }}</span>
    </td>
</tr>