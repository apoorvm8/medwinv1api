<div class="modal fade" id="customerLoginModal" tabindex="-1" role="dialog" aria-labelledby="customerLoginModalLabel"
    aria-hidden="true">
    <div class="modal-dialog px-2" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="customerLoginModalLabel">Customer Login</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='customerLoginForm' class="bg-light">
                <div class="modal-body bg-light ml-md-5">
                    <div class="form-group row mb-1">
                        <label for="customer_id" class="col-sm-4 col-form-label font-weight-bold">Customer ID:</label>
                        <div class="col-sm-8">
                          <input type="text" name="customer_id" class="form-control clearFields numeric" autofocus placeholder="Enter Customer ID">
                        </div>
                    </div>
                </div>
                <div class="modal-body bg-light ml-md-5">
                    <div class="form-group row mb-2">
                        <label for="password" class="col-sm-4 col-form-label font-weight-bold">Password:</label>
                        <div class="col-sm-8">
                          <input type="password" name="password" class="form-control clearFields" placeholder="Enter Password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary" id="customerLoginBtn">
                        <span>Log In</span>
                        <span style="display:none;"><span class="spinner-border spinner-border-sm" role="status"
                                aria-hidden="true"></span>
                            Logging In...</span>
                    </button>
                    <button role="button" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>