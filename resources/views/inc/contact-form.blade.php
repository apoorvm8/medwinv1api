@php
    $selectedSoftware = old('type_of_soft', request()->query('software', request()->query('type_of_soft', '')));
@endphp

@include('inc.messages')

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Please check the details below.</strong>
        <ul class="mb-0 mt-2 pl-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('customer.msg.submit') }}" method="POST" id="contactForm" class="enquiry-form">
    @csrf

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="name">Name <span aria-hidden="true">*</span></label>
            <input required value="{{ old('name') }}" name="name" id="name" type="text"
                class="form-control" autocomplete="name" placeholder="Your full name">
        </div>

        <div class="form-group col-md-6">
            <label for="mobile_no">Mobile number <span aria-hidden="true">*</span></label>
            <input required value="{{ old('mobile_no') }}" name="mobile_no" id="mobile_no" type="tel"
                inputmode="numeric" pattern="[0-9]{10}" maxlength="10" class="form-control numeric"
                autocomplete="tel" placeholder="10-digit mobile number">
        </div>
    </div>

    <div class="form-group">
        <label for="email">Email address <span class="optional-label">Optional</span></label>
        <input value="{{ old('email') }}" id="email" name="email" type="email"
            class="form-control" autocomplete="email" placeholder="you@business.com">
    </div>

    <div class="form-group">
        <label for="type_of_soft">Software required <span aria-hidden="true">*</span></label>
        <select required class="form-control custom-select" name="type_of_soft" id="type_of_soft">
            <option value="">Select a solution</option>
            <option value="retailPharma" {{ $selectedSoftware === 'retailPharma' ? 'selected' : '' }}>Retail Pharmacy</option>
            <option value="wholeSalePharma" {{ $selectedSoftware === 'wholeSalePharma' ? 'selected' : '' }}>Wholesale Pharmacy</option>
            <option value="retailSupermarket" {{ $selectedSoftware === 'retailSupermarket' ? 'selected' : '' }}>Retail Departmental Store</option>
            <option value="retailBookstore" {{ $selectedSoftware === 'retailBookstore' ? 'selected' : '' }}>Retail Bookstore</option>
            <option value="retailFootwear" {{ $selectedSoftware === 'retailFootwear' ? 'selected' : '' }}>Retail Footwear</option>
            <option value="wholeSaleFootwear" {{ $selectedSoftware === 'wholeSaleFootwear' ? 'selected' : '' }}>Wholesale Footwear</option>
            <option value="retailGarment" {{ $selectedSoftware === 'retailGarment' ? 'selected' : '' }}>Retail Garment</option>
            <option value="other" {{ $selectedSoftware === 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>

    <button id="submit" type="submit" class="btn btn-primary btn-lg btn-block mt-4">
        <span class="submit-label">Request a demo</span>
        <i class="fas fa-arrow-right ml-2" aria-hidden="true"></i>
    </button>

    <p class="form-note mb-0 mt-3">
        By submitting this form, you agree to be contacted about Medwin software.
    </p>
</form>
