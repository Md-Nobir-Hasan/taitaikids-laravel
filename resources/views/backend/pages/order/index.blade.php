@extends('backend.layouts.app')

@section('title', 'Order Management')

@push('third_party_stylesheets')
    <link href="{{ asset('assets/backend/js/DataTable/datatables.min.css') }}" rel="stylesheet">
@endpush

@push('page_css')
    <style>
        .btn-box {
            display: flex;
            justify-content: center;
        }

        .dialogify-bottom-select {
            margin-bottom: 33px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <span class="float-left">
                            <h4>View Order</h4>
                        </span>
                        <span class="float-right">
                            <a href="{{ route('order.trash') }}" class="btn btn-danger">Trash</a>
                        </span>
                    </div>
                    <div class="card-body">
                        @include('backend.partial.flush-message')
                        <div class="table-responsive">
                            <table id="table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>S.N.</th>
                                        <th>Order No.</th>
                                        <th>Name</th>
                                        <th>phone</th>
                                        <th>Address</th>
                                        <th>Proudct Title</th>
                                        <th>Proudct Price</th>
                                        <th>Quantity</th>
                                        {{-- <th>Shipping</th> --}}
                                        {{-- <th>Total Amount</th> --}}
                                        {{-- <th>Payment Method</th> --}}
                                        <th>Ordered Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $key => $order)
                                   
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ $order->address }}</td>
                                            <td>{{ $order->product->title }}</td>
                                            <td>{{ $order->product->price }}</td>
                                            <td>{{ $order->quantity }}</td>

                                            {{-- <td>{{ $order->shipping->type  .'('.$order->shipping->price.')৳' }}</td> --}}
                                            {{-- <td>{{ ($order->quantity * $order->product->price)+ $order->shipping->price}}</td>
                                            <td>{{ $order->quantity->payment_method ?? 'Cash on Delivery'}}
                                            </td> --}}
                                            {{-- <td>{{ $order->payment_number }}</td> --}}
                                            {{-- <td>{{ $order->pamyment_method}}</td> --}}
                                            <td>{{ $order->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a class="btn">
                                                    @if ($order->order_status == 'new')
                                                        <span class="badge badge-primary order_status"
                                                            onclick="orderStatus({{ $order->id }},{{ $key }})"
                                                            id="order_status{{ $key }}">{{ $order->order_status }}</span>
                                                    @elseif($order->order_status == 'process')
                                                        <span class="badge badge-warning order_status"
                                                            onclick="orderStatus({{ $order->id }},{{ $key }})"
                                                            id="order_status{{ $key }}">{{ $order->order_status }}</span>
                                                    @elseif($order->order_status == 'delivered')
                                                        <span class="badge badge-success order_status"
                                                            onclick="orderStatus({{ $order->id }},{{ $key }})"
                                                            id="order_status{{ $key }}">{{ $order->order_status }}</span>
                                                    @else
                                                        <span class="badge badge-danger order_status"
                                                            onclick="orderStatus({{ $order->id }},{{ $key }})"
                                                            id="order_status{{ $key }}">{{ $order->order_status }}</span>
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="text-middle py-0 align-middle">
                                                <div class="btn-group">
                                                    {{-- <a href="{{ route('order.edit', $order->id) }}"
                                                        class="btn btn-dark btnEdit" title="Edit"><i
                                                            class="fas fa-edit"></i></a> --}}
                                                    <a href="{{ route('order.delete', $order->id) }}"
                                                        class="btn btn-danger btnDelete" title="Move to trash"><i
                                                            class="fas fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@push('third_party_scripts')
    <script src="{{ asset('assets/backend/js/DataTable/datatables.min.js') }}"></script>
    <script src="https://www.jqueryscript.net/demo/Dialog-Modal-Dialogify/dist/dialogify.min.js"></script>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        title: 'District Management',
                        download: 'open',
                        orientation: 'potrait',
                        pagesize: 'LETTER',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                        }
                    }, 'pageLength'
                ]
            });
        });

        // Dialogify
        function orderStatus(order_id, key) {
            var options = {
                ajaxPrefix: ''
            };
            new Dialogify('{{ url('order-status/ajax') }}', options)
                .title("Ordere Status")
                .buttons([{
                        text: "Cancle",
                        type: Dialogify.BUTTON_DANGER,
                        click: function(e) {
                            this.close();
                        }
                    },
                    {
                        text: 'Status update',
                        type: Dialogify.BUTTON_PRIMARY,
                        click: function(e) {
                            var name = $('#order_status_name').val();

                            $.ajax({
                                cache: false,
                                url: "{{ route('order-status.order-status-assign') }}",
                                method: "GET",
                                data: {
                                    name: name,
                                    order_id: order_id
                                },
                                success: function(data) {
                                    if (data != 0) {
                                        alert('Order Status successfully updated')
                                        // console.log($('#order_status').html());
                                        $('#order_status' + key).html(data);

                                    } else {
                                        alert("Order Status can't update")

                                    }
                                }
                            });

                        }
                        // }
                    }
                ]).showModal();
            //     });
            // });
        }
    </script>
@endpush
{{-- var form_data = new FormData();
form_data.append('name', $('#name').val());
form_data.append('address', $('#address')
.val());
form_data.append('discount', discount_v);
form_data.append('id', data[0].cake_id);
$.ajax({
method: "POST",
url: '{{ url('order.store') }}',
data: form_data,
// dataType:'json',
contentType: false,
cache: false,
processData: false,
success: function(value) {
// alert(value);
// $.ajax({
// cache: false,
// url: "{{ url('order.store') }}",
// method: "POST",
// success: function(
// data) {
// $("#show_data")
// .html(
// data
// );
// }
// });

}
}); --}}
