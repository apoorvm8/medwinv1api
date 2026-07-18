<div class="modal fade login-modal" id="customerLoginModal" tabindex="-1" role="dialog"
    aria-labelledby="customerLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-accent" aria-hidden="true"></div>
            <div class="modal-header border-0 align-items-start">
                <div>
                    <span class="section-eyebrow mb-2">Existing customers</span>
                    <h2 class="modal-title" id="customerLoginModalLabel">Customer login</h2>
                    <p class="modal-subtitle mb-0">Sign in with your 4-digit Medwin Customer ID.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="customerLoginForm" novalidate>
                @csrf
                <div class="modal-body pt-2">
                    <div class="form-group">
                        <label for="customer_id">Customer ID</label>
                        <div class="input-icon-wrap">
                            <i class="far fa-user" aria-hidden="true"></i>
                            <input type="text" id="customer_id" name="customer_id"
                                class="form-control clearFields numeric" inputmode="numeric" maxlength="4"
                                autocomplete="username" autofocus placeholder="Enter 4-digit Customer ID">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="password">Password</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-lock" aria-hidden="true"></i>
                            <input type="password" id="password" name="password"
                                class="form-control clearFields" autocomplete="current-password"
                                placeholder="Enter your password">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="customerLoginBtn">
                        <span>Sign in</span>
                        <span class="align-items-center" style="display: none;">
                            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                            Signing in...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
