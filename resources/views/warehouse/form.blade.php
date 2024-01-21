<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="id_produk" class="col-lg-2 col-lg-offset-1 control-label">Product</label>
                        <div class="col-lg-6">
                            <select name="id_produk" id="id_produk" class="form-control" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="stock" class="col-lg-2 col-lg-offset-1 control-label">Number of Stock</label>
                        <div class="col-lg-6">
                            <input min="0" type="number" name="stock" id="stock" class="form-control" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                 
                    <div class="form-group row">
                        <label for="notes" class="col-lg-2 col-lg-offset-1 control-label">Description/Notes</label>
                        <div class="col-lg-6">
                        <textarea type="number" name="notes" id="notes" class="form-control" >
                        </textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-success"><i class="fa fa-save"></i> Save</button>
                    <button type="button" class="btn btn-sm btn-flat btn-danger" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>