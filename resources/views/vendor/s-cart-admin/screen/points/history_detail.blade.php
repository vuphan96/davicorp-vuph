@forelse($data as $datum)
    {{--    {{dd($datum->history()->get())}}--}}
    <h5 style="display: inline">Tháng {{ $datum->month ?? '' }}/{{ $datum->year ?? '' }}</h5><span class="float-right">Tổng điểm: <span id="update_point">{{ $datum->point }}</span></span>
    <table class="table">
        <thead>
        <tr>
            <th scope="col">Mã đơn hàng</th>
            <th scope="col">Ngày giao hàng</th>
            <th scope="col" style="text-align: right">Tổng tiền</th>
            <th scope="col" style="text-align: right">Điểm thưởng</th>
            <th scope="col" style="text-align: right">Điểm thưởng thực tế</th>
        </tr>
        </thead>
        <tbody>
        @forelse($datum->history as $item)
            <tr>
                <th scope="row">{{ $item->order->id_name ?? 'Đơn hàng bị xóa' }}</th>
                <td> {{ \Carbon\Carbon::parse($item->order->delivery_time ?? '')->format('d/m/Y') ?? '' }}</td>
                <td style="text-align: right"> {{ isset($item->order->total) ? sc_currency_render($item->order->total ?? 0, 'VND') : ''  }}</td>
                <td style="text-align: right"> {{ $item->change_point ?? 0 }}</td>
                <td style="text-align: right">
                    <a class="updateActualPoint" id="actual_point_{{ $item->id }}" data-name="actual_point" data-type="number" data-min="0"
                                                 data-pk="{{ $item->id }}"
                                                 data-url="{{ route("admin_point_view.update_actual_point") }}"
                                                 data-value="{{ $item->actual_point ?? 0 }}"
                                                 data-emptytext="0"
                                                 data-title="Điểm thực tế">{{ $item->actual_point ?? 0 }}
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Không có dữ liệu</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@empty
    Không có dữ liệu
@endforelse
