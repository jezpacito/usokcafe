@extends('layouts.master')

@section('title')
Warehouse Stocks List
@endsection

@section('breadcrumb')
@parent
<li class="active">Warehouse Stocks List</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            <div class="box-header with-border">
                <div class="btn-group">
                    {{-- <button onclick="addForm('{{ route('warehouse.store') }}')" class="btn btn-success  btn-flat"><i
                            class="fa fa-plus-circle"></i> Manage Stock
                    </button> --}}

                </div>
                <form action="{{ route('upload.restock') }}" method="post" class="form-setting" data-toggle="validator"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body" style="border: 2px solid #000; padding: 10px; margin: 10px">
                        <div class="form-group row">
                            <label for="path_logo" class="col-lg-2 control-label">Bulk Restock</label>
                            <div class="col-lg-4">
                                <input type="file" name="restock_csv" class="form-control" id="path_logo">
                                <span class="help-block with-errors"></span>
                                <br>
                                <div class="tampil-logo"></div>
                            </div>
                        </div>
                        <div class="box-footer text-left">
                            <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Save
                                Restock</button>
                        </div>
                    </div>
                </form>
                {{-- <button onclick="deleteSelected('{{ route('warehouse.delete_selected') }}')"
                    class="btn btn-danger  btn-flat"><i class="fa fa-trash"></i> Delete</button> --}}
                {{-- <button onclick="cetakBarcode('{{ route('warehouse.cetak_barcode') }}')"
                    class="btn btn-warning  btn-flat"><i class="fa fa-barcode"></i> Print Barcode</button> --}}
            </div>
        </div>
        <div class="box-body table-responsive">
            <form action="" method="post" class="form-produk">
                @csrf
                <table class="table table-stiped table-bordered table-hover">
                    <thead>
                        <th width="5%">
                            <input type="checkbox" name="select_all" id="select_all">
                        </th>
                        <th width="5%">Product Code</th>
                        <th>Product Name</th>
                        <th>Brand Name</th>
                        <th># Of Stocks</th>
                        <th>Created Date</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </form>
        </div>
    </div>
</div>
</div>

@includeIf('warehouse.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('warehouse.data') }}',
            },
            columns: [
                // {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'product_code'},
                {data: 'product_name'},
                {data: 'brand_name'},
                {data: 'stocks'},
                // {data: 'notes'},
                {data: 'created_at'},
                // {data: 'aksi', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Unable to save data');
                        return;
                    });
            }
        });

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Manage Stock');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=nama_produk]').focus();    
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Product');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=nama_produk]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=nama_produk]').val(response.nama_produk);
                $('#modal-form [name=id_kategori]').val(response.id_kategori);
                $('#modal-form [name=merk]').val(response.merk);
                $('#modal-form [name=harga_beli]').val(response.harga_beli);
                $('#modal-form [name=harga_jual]').val(response.harga_jual);
                $('#modal-form [name=diskon]').val(response.diskon);
                $('#modal-form [name=stok]').val(response.stok);
            })
            .fail((errors) => {
                alert('Unable to display data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Are you sure you want to delete selected data?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Unable to delete data');
                    return;
                });
        }
    }

    function deleteSelected(url) {
        if ($('input:checked').length > 1) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, $('.form-produk').serialize())
                    .done((response) => {
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Unable to delete data');
                        return;
                    });
            }
        } else {
            alert('Select the data to delete');
            return;
        }
    }

    function cetakBarcode(url) {
        if ($('input:checked').length < 1) {
            alert('Select the data to print');
            return;
        } else if ($('input:checked').length < 3) {
            alert('Select at least 3 data to print');
            return;
        } else {
            $('.form-produk')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
</script>
@endpush