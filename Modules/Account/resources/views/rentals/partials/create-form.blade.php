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

    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-address-card"></i> Landlord / owner <span class="rental-hint" style="text-transform:none;">saved to address book</span></div>
        <p class="rental-owner-lead">Enter at least <strong style="color:var(--text);">email</strong> or <strong style="color:var(--text);">phone</strong> once per landlord; we merge duplicates automatically.</p>
        <div class="rental-fields-grid">
            <div class="rental-field rental-field--full">
                <label for="rental-owner-name">Owner name</label>
                <input id="rental-owner-name" type="text" name="owner_name" value="{{ old('owner_name') }}" required maxlength="255" placeholder="Landlord or company contact">
                @error('owner_name')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-owner-email">Owner email</label>
                <input id="rental-owner-email" type="email" name="owner_email" value="{{ old('owner_email') }}" maxlength="255" autocomplete="email" placeholder="contact@example.com">
                @error('owner_email')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-owner-phone">Owner phone</label>
                <input id="rental-owner-phone" type="text" name="owner_phone" value="{{ old('owner_phone') }}" maxlength="40" inputmode="tel" autocomplete="tel">
                @error('owner_phone')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="rental-owner-address">Owner address / location <span class="rental-hint">optional</span></label>
                <textarea id="rental-owner-address" name="owner_address" rows="2" maxlength="2000" placeholder="Mailing address or locality">{{ old('owner_address') }}</textarea>
                @error('owner_address')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="rental-owner-bank">Owner bank account details</label>
                <textarea id="rental-owner-bank" name="owner_bank_details" rows="3" maxlength="5000" placeholder="Bank name, account name, IBAN/account number">{{ old('owner_bank_details') }}</textarea>
                @error('owner_bank_details')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="rental-owner-notes">Owner notes <span class="rental-hint">optional · stored on contact card</span></label>
                <textarea id="rental-owner-notes" name="owner_notes" rows="2" maxlength="2000" placeholder="Internal notes about this person">{{ old('owner_notes') }}</textarea>
                @error('owner_notes')<span class="rental-field-err">{{ $message }}</span>@enderror
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
                <select id="rental-deduct" name="deduct_account_id" class="rental-select">
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
                <select id="rental-recurring-type" name="recurring_type" class="rental-select" required>
                    @foreach($recurringTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('recurring_type', \Modules\Account\Models\Rental::RECURRING_PER_MONTH) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('recurring_type')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="rental-remind-before">Remind before <span class="rental-hint">days before each recurring rent (delivery TBD)</span></label>
                <input id="rental-remind-before" type="number" name="remind_before_days" value="{{ old('remind_before_days') }}" min="0" max="366" step="1" inputmode="numeric" placeholder="e.g. 7 — leave blank for none">
                @error('remind_before_days')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-due-date">Due date <span class="rental-hint">optional · next/current period</span></label>
                <input id="rental-due-date" type="date" name="due_date" value="{{ old('due_date') }}">
                @error('due_date')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="rental-first-installment">First installment due <span class="rental-hint">optional</span></label>
                <input id="rental-first-installment" type="date" name="first_installment_due_date" value="{{ old('first_installment_due_date') }}">
                @error('first_installment_due_date')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-note-sticky"></i> Rental notes</div>
        <div class="rental-field rental-field--full">
            <label for="rental-notes">Lease notes</label>
            <textarea id="rental-notes" name="notes" rows="3" maxlength="5000" placeholder="Anything specific to this rental agreement">{{ old('notes') }}</textarea>
            @error('notes')<span class="rental-field-err">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="rental-submit-wrap">
        <button type="submit" class="linkbtn"><i class="fa fa-check"></i> Save rental</button>
        <span class="rental-submit-note">Uses the business selected in the top navigation.</span>
    </div>
</form>
