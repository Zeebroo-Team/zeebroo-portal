@if($errors->any())
    <div class="rental-alert rental-alert--err {{ $rentalFormErrorBannerClass ?? '' }}" role="alert">
        <i class="fa fa-circle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form id="rental-form" method="post" action="{{ route('account.rentals.store') }}" class="rental-fields">
    @csrf
    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-building"></i> Property</div>
        <div class="rental-fields-grid">
            <div class="rental-field">
                <label for="rental-property-type">Property type</label>
                <input id="rental-property-type" type="text" name="property_type" value="{{ old('property_type') }}" required maxlength="255" placeholder="e.g. Office, Shop, Warehouse">
                @error('property_type')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="rental-purpose">Purpose</label>
                <textarea id="rental-purpose" name="purpose" rows="2" maxlength="2000" placeholder="What the space is used for">{{ old('purpose') }}</textarea>
                @error('purpose')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-key-money">Key money <span class="rental-hint">optional</span></label>
                <input id="rental-key-money" type="number" name="key_money" value="{{ old('key_money') }}" min="0" step="0.01" inputmode="decimal" placeholder="0.00">
                @error('key_money')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-agreement-year">Agreement valid until (year)</label>
                <input id="rental-agreement-year" type="number" name="agreement_valid_until_year" value="{{ old('agreement_valid_until_year', date('Y') + 1) }}" required min="2000" max="2100" step="1" inputmode="numeric">
                @error('agreement_valid_until_year')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    @if($business)
        @include('account::partials.warehouse-branch-select', [
            'presetBranchId' => old('branch_id'),
            'fixedBusinessId' => $business->id,
            'warehouseSelectClass' => 'rental-select',
        ])
        @error('branch_id')<span class="rental-field-err" style="display:block;margin-top:6px;">{{ $message }}</span>@enderror
    @endif

    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-wallet"></i> Payment</div>
        <div class="rental-fields-grid">
            <div class="rental-field rental-field--full">
                <label for="rental-deduct">Deduct from account</label>
                <select id="rental-deduct" name="deduct_account_id">
                    <option value="">None</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" @selected((string) old('deduct_account_id') === (string) $acc->id)>{{ $acc->deductOptionLabel() }}</option>
                    @endforeach
                </select>
                @error('deduct_account_id')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-recurring-cost">Recurring cost</label>
                <input id="rental-recurring-cost" type="number" name="recurring_cost" value="{{ old('recurring_cost') }}" required min="0" step="0.01" inputmode="decimal" placeholder="0.00">
                @error('recurring_cost')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-recurring-type">Rental recurring</label>
                <select id="rental-recurring-type" name="recurring_type" required>
                    @foreach($recurringTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('recurring_type', \Modules\Account\Models\Rental::RECURRING_PER_MONTH) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('recurring_type')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="rental-submit-wrap">
        <button type="submit" class="linkbtn"><i class="fa fa-check"></i> Save rental</button>
        <span class="rental-submit-note">Uses the business selected in the top navigation.</span>
    </div>
</form>
