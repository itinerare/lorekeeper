<tr class="outflow">
    <td>{!! $log->adopt ? $log->adopt->displayName : '(Deleted Character)' !!}</td>
    <td>{!! $log->adoption->displayName !!}</td>
    <td>{!! $log->currency ? $log->currency->display($log->cost) : $log->cost . ' (Deleted Currency)' !!}</td>
    <td>{!! format_date($log->created_at) !!}</td>
</tr>