<!-- Modal from message delete confirmation-->
<div class="modal fade modal-remove" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" id="form-delete">
            <input type="hidden" name="_METHOD" value="DELETE"/>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Delete a record</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        Do you want really remove this record?
                    </div>
                </div>
                <div class="modal-footer button-group">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger">Remove</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Change Password -->
<div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="changePasswordLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="modifyPassword" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Alterar Senha</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-md-4">
                            Password
                        </label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">
                            New Password
                        </label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="new_password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>