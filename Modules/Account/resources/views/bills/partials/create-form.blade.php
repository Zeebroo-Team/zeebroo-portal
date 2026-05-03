@php
    $editing = $editingBill ?? null;
    $departmentsForBill = $departmentsForBill ?? collect();
    $rentalsForBillLink = $rentalsForBillLink ?? collect();
    $rpRelatedShow = (bool) old('rental_property_related', $editing?->rental_property_related ?? false);
    $pmOld = old('payment_mode', $editing?->payment_mode ?? \Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING);
    $catOld = old('bill_category', $editing?->bill_category ?? \Modules\Account\Models\Bill::CATEGORY_OTHER);
    $varyUsageChecked = filter_var(old('amount_varies_by_usage', $editing?->amount_varies_by_usage ?? false), FILTER_VALIDATE_BOOLEAN);
    $allowSplitChecked = filter_var(old('allow_split_payment', $editing?->allow_split_payment ?? true), FILTER_VALIDATE_BOOLEAN);
@endphp
@include('account::bills.partials.bill-rental-field-styles')
@if($errors->any())
    <div class="rental-alert rental-alert--err {{ $billFormErrorBannerClass ?? '' }}" role="alert">
        <i class="fa fa-circle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form id="{{ $billFormId ?? 'bill-form' }}" method="post" action="{{ $billFormAction ?? route('account.bills.store') }}" class="rental-fields">
    @csrf
    @isset($billFormMethod)
        @method($billFormMethod)
    @endisset
    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-file-invoice-dollar"></i> Bill</div>
        <div class="rental-fields-grid">
            <div class="rental-field rental-field--full">
                <label for="bill-name">Name</label>
                <input id="bill-name" type="text" name="name" value="{{ old('name', $editing?->name) }}" required maxlength="255" placeholder="Short label on your dashboard">
                @error('name')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="bill-category">Bill type</label>
                <select id="bill-category" name="bill_category" class="rental-select" required>
                    @foreach($billCategories as $value => $label)
                        <option value="{{ $value }}" @selected((string) $catOld === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('bill_category')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full" id="bill-category-other-wrap" style="{{ (string) $catOld === \Modules\Account\Models\Bill::CATEGORY_OTHER ? '' : 'display:none;' }}">
                <label for="bill-category-other">Specify type <span class="rental-hint">when Other</span></label>
                <input id="bill-category-other" type="text" name="bill_category_other" value="{{ old('bill_category_other', $editing?->bill_category_other) }}" maxlength="255" placeholder="e.g. Security, Software subscription">
                @error('bill_category_other')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="bill-description">Description <span class="rental-hint">optional</span></label>
                <textarea id="bill-description" name="description" rows="2" maxlength="2000" placeholder="Vendor, account number, or service notes">{{ old('description', $editing?->description) }}</textarea>
                @error('description')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full bill-pay-pattern">
                <span class="bill-pay-pattern__legend" id="bill-payment-pattern-heading">Payment pattern</span>
                <p class="bill-pay-pattern__hint">Recurring bills repeat on a schedule you control. One-time is a single payment with a due or anchor date.</p>
                <div class="bill-pay-pattern__choices" role="radiogroup" aria-labelledby="bill-payment-pattern-heading">
                    <label class="bill-pay-pattern__option">
                        <span class="bill-pay-pattern__option-inner">
                            <span class="bill-pay-pattern__ico" aria-hidden="true"><i class="fa fa-rotate"></i></span>
                            <span class="bill-pay-pattern__body">
                                <span class="bill-pay-pattern__title">{{ $paymentModes[\Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING] ?? 'Recurring' }}</span>
                                <span class="bill-pay-pattern__desc">Cycles until the schedule end year · set cadence (month / year / day)</span>
                            </span>
                            <span class="bill-pay-pattern__radio">
                                <input type="radio" name="payment_mode" value="{{ \Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING }}" @checked((string) $pmOld === \Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING)>
                            </span>
                        </span>
                    </label>
                    <label class="bill-pay-pattern__option">
                        <span class="bill-pay-pattern__option-inner">
                            <span class="bill-pay-pattern__ico" aria-hidden="true"><i class="fa fa-money-bill-wave"></i></span>
                            <span class="bill-pay-pattern__body">
                                <span class="bill-pay-pattern__title">{{ $paymentModes[\Modules\Account\Models\Bill::PAYMENT_MODE_ONE_TIME] ?? 'One-time' }}</span>
                                <span class="bill-pay-pattern__desc">One payment total · requires a due date or first installment date</span>
                            </span>
                            <span class="bill-pay-pattern__radio">
                                <input type="radio" name="payment_mode" value="{{ \Modules\Account\Models\Bill::PAYMENT_MODE_ONE_TIME }}" @checked((string) $pmOld === \Modules\Account\Models\Bill::PAYMENT_MODE_ONE_TIME)>
                            </span>
                        </span>
                    </label>
                </div>
                @error('payment_mode')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field" id="bill-agreement-year-field">
                <label for="bill-agreement-year">Schedule through (year)</label>
                <input id="bill-agreement-year" type="number" name="agreement_valid_until_year" value="{{ old('agreement_valid_until_year', $editing?->agreement_valid_until_year ?? ((int) date('Y') + 1)) }}" min="2000" max="2100" step="1" inputmode="numeric"
                    @if((string) $pmOld !== \Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING) disabled @else required @endif>
                @error('agreement_valid_until_year')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    @if($business && $departmentsForBill->isNotEmpty())
        <div class="rental-form-section">
            <div class="rental-form-section__head"><i class="fa fa-users" aria-hidden="true"></i> Department</div>
            <p class="bill-rental-field__lead">Optional — tag this bill for a specific HR department when you organize teams under <strong>HR Management</strong>.</p>
            <div class="rental-fields-grid">
                <div class="rental-field rental-field--full">
                    <label for="bill-department-id">Assign to department</label>
                    <select id="bill-department-id" name="department_id" class="rental-select">
                        <option value="">No department</option>
                        @foreach($departmentsForBill as $dept)
                            <option value="{{ $dept->id }}" @selected((string) old('department_id', $editing?->department_id) === (string) $dept->id)>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<span class="rental-field-err">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    @endif

    @if($business)
        <div class="rental-form-section bill-location-rental-group">
            <div class="rental-form-section__head"><i class="fa fa-building" aria-hidden="true"></i> Rental property</div>
            <p class="bill-rental-field__lead">Group <strong>where</strong> this bill applies and <strong>which rental</strong> (if any) it belongs to: use the branch when your business has multiple locations, then optionally link to a saved rental for rent or property-tied utilities.</p>

            <div class="bill-location-rental-group__branch">
                @include('account::partials.warehouse-branch-select', [
                    'presetBranchId' => old('branch_id', $editing?->branch_id),
                    'fixedBusinessId' => $business->id,
                    'warehouseSelectClass' => 'rental-select',
                ])
                @error('branch_id')<span class="rental-field-err" style="display:block;margin-top:6px;">{{ $message }}</span>@enderror
            </div>

            <div class="bill-location-rental-group__divider" role="presentation" aria-hidden="true"></div>

            <div class="bill-location-rental-group__rental">
                @if($rentalsForBillLink->isEmpty())
                    <div class="bill-rental-empty-note" role="status">
                        <i class="fa fa-circle-info" aria-hidden="true"></i>
                        <span>To link this bill to a lease or property, add a rental under <strong>Rentals</strong> from the overview first.</span>
                    </div>
                @else
                    <div class="rental-fields-grid">
                        <div class="rental-field--full">
                            <label id="bill-rental-toggle-label" for="bill-rental-related" @class([
                                'bill-rental-toggle',
                                'bill-rental-toggle--on' => $rpRelatedShow,
                            ])>
                                <span class="bill-rental-toggle__icon" aria-hidden="true"><i class="fa fa-link"></i></span>
                                <span class="bill-rental-toggle__body">
                                    <span class="bill-rental-toggle__title">Link to a rental record</span>
                                    <span class="bill-rental-toggle__desc">Turn on and pick a property below for rent or other rental-specific bills. Leave off for expenses that are not tied to a lease.</span>
                                </span>
                                <span class="bill-rental-toggle__control">
                                    <input type="checkbox" name="rental_property_related" id="bill-rental-related" value="1" @checked($rpRelatedShow)>
                                </span>
                            </label>
                            @error('rental_property_related')<span class="rental-field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="rental-field rental-field--full bill-rental-select-wrap" id="bill-rental-link-wrap" @if(!$rpRelatedShow) hidden @endif>
                            <label class="bill-rental-select-label" for="bill-rental-id">Which rental</label>
                            <select id="bill-rental-id" name="rental_id" class="rental-select" @if(!$rpRelatedShow) disabled @endif>
                                <option value="">Select a rental…</option>
                                @foreach($rentalsForBillLink as $r)
                                    <option value="{{ $r->id }}" @selected((string) old('rental_id', $editing?->rental_id) === (string) $r->id)>
                                        {{ $r->property_type }}@if($r->warehouse) · {{ $r->warehouse->name }}@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('rental_id')<span class="rental-field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-wallet"></i> Payment</div>
        <div class="rental-fields-grid">
            <div class="rental-field rental-field--full">
                <label for="bill-deduct">Preferred debit account</label>
                <select id="bill-deduct" name="deduct_account_id" class="rental-select">
                    <option value="">None — pick when recording each payment</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" @selected((string) old('deduct_account_id', $editing?->deduct_account_id) === (string) $acc->id)>{{ $acc->deductOptionLabel() }}</option>
                    @endforeach
                </select>
                @error('deduct_account_id')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="bill-recurring-cost"><span id="bill-amount-label">Amount</span> <span class="rental-hint" id="bill-amount-hint">per billing cycle</span></label>
                <input id="bill-recurring-cost" type="number" name="recurring_cost" value="{{ old('recurring_cost', $editing !== null ? number_format((float) $editing->recurring_cost, 2, '.', '') : '') }}" min="0" step="0.01" inputmode="decimal" placeholder="0.00" @unless($varyUsageChecked) required @endunless>
                @error('recurring_cost')<span class="rental-field-err">{{ $message }}</span>@enderror
                <small id="bill-recurring-cost-help" style="display:block;margin-top:6px;font-size:11px;color:var(--muted);font-weight:500;line-height:1.4;"></small>
            </div>
            <div class="rental-field rental-field--full">
                <input type="hidden" name="amount_varies_by_usage" value="0">
                <label for="bill-amount-varies" style="margin:0;font-weight:600;display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <span style="padding-top:2px;"><input type="checkbox" name="amount_varies_by_usage" id="bill-amount-varies" value="1" @checked($varyUsageChecked)></span>
                    <span style="flex:1;min-width:0;">
                        <span style="display:block;">Amount varies by usage or invoice</span>
                        <span style="display:block;margin-top:3px;font-size:11px;font-weight:500;color:var(--muted);">For water, utilities, or any bill where each period total is confirmed when you receive the invoice. You declare the charge when recording the first payment for that billing date.</span>
                    </span>
                </label>
                @error('amount_varies_by_usage')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <input type="hidden" name="allow_split_payment" value="0">
                <label for="bill-allow-split" style="margin:0;font-weight:600;display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <span style="padding-top:2px;"><input type="checkbox" name="allow_split_payment" id="bill-allow-split" value="1" @checked($allowSplitChecked)></span>
                    <span style="flex:1;min-width:0;">
                        <span style="display:block;">Allow splitting one payment across multiple debit accounts</span>
                        <span style="display:block;margin-top:3px;font-size:11px;font-weight:500;color:var(--muted);">Turn off when you always pay each bill from a single account.</span>
                    </span>
                </label>
                @error('allow_split_payment')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field" id="bill-recurring-type-field">
                <label for="bill-recurring-type">Billing cadence</label>
                <select id="bill-recurring-type" name="recurring_type" class="rental-select"
                    @if((string) $pmOld !== \Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING) disabled @else required @endif>
                    @foreach($recurringTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('recurring_type', $editing?->recurring_type ?? \Modules\Account\Models\Bill::RECURRING_PER_MONTH) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('recurring_type')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field rental-field--full">
                <label for="bill-remind-before">Remind before <span class="rental-hint">days before period end — delivery TBD</span></label>
                <input id="bill-remind-before" type="number" name="remind_before_days" value="{{ old('remind_before_days', $editing?->remind_before_days) }}" min="0" max="366" step="1" inputmode="numeric" placeholder="e.g. 7 — blank for none">
                @error('remind_before_days')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="bill-due-date">Due date <span class="rental-hint" id="bill-due-hint">optional · schedule anchor</span></label>
                <input id="bill-due-date" type="date" name="due_date" value="{{ old('due_date', $editing?->due_date?->format('Y-m-d')) }}">
                @error('due_date')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
            <div class="rental-field">
                <label for="bill-first-installment">First installment due <span class="rental-hint">optional — alternative anchor</span></label>
                <input id="bill-first-installment" type="date" name="first_installment_due_date" value="{{ old('first_installment_due_date', $editing?->first_installment_due_date?->format('Y-m-d')) }}">
                @error('first_installment_due_date')<span class="rental-field-err">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="rental-form-section">
        <div class="rental-form-section__head"><i class="fa fa-note-sticky"></i> Notes</div>
        <div class="rental-field rental-field--full">
            <label for="bill-notes">Internal notes</label>
            <textarea id="bill-notes" name="notes" rows="3" maxlength="5000" placeholder="Reference numbers or reminders">{{ old('notes', $editing?->notes) }}</textarea>
            @error('notes')<span class="rental-field-err">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="rental-submit-wrap">
        <button type="submit" class="linkbtn"><i class="fa fa-check"></i>{{ $billSubmitLabel ?? 'Save bill' }}</button>
        <span class="rental-submit-note">Uses the business selected in the top navigation.</span>
    </div>
</form>

<script>
(function(){
    var cat = document.getElementById('bill-category');
    var otherWrap = document.getElementById('bill-category-other-wrap');
    function syncCategoryOther(){
        if(!cat || !otherWrap) return;
        otherWrap.style.display = cat.value === 'other' ? '' : 'none';
    }
    cat && cat.addEventListener('change', function(){
        syncCategoryOther();
        if(cat.value === waterCat && variesCb && !variesCb.checked){
            variesCb.checked = true;
            syncAmountVariesUi();
        }
    });

    var recurring = @json(\Modules\Account\Models\Bill::PAYMENT_MODE_RECURRING);
    var oneTime = @json(\Modules\Account\Models\Bill::PAYMENT_MODE_ONE_TIME);
    var radios = document.querySelectorAll('input[name="payment_mode"]');
    var yearInp = document.getElementById('bill-agreement-year');
    var yearField = document.getElementById('bill-agreement-year-field');
    var cadenceSel = document.getElementById('bill-recurring-type');
    var cadenceField = document.getElementById('bill-recurring-type-field');
    var amtLabel = document.getElementById('bill-amount-label');
    var amtHint = document.getElementById('bill-amount-hint');
    var dueHint = document.getElementById('bill-due-hint');
    var variesCb = document.getElementById('bill-amount-varies');
    var recurringCostEl = document.getElementById('bill-recurring-cost');
    var costHelpEl = document.getElementById('bill-recurring-cost-help');
    var waterCat = @json(\Modules\Account\Models\Bill::CATEGORY_WATER);

    function syncAmountVariesUi(){
        if(!recurringCostEl) return;
        var on = variesCb ? variesCb.checked : false;
        recurringCostEl.required = !on;
        if(costHelpEl){
            costHelpEl.textContent = on
                ? 'Optional typical amount shown on reminders; the actual total is locked in when you enter it on payment.'
                : '';
        }
    }

    variesCb && variesCb.addEventListener('change', syncAmountVariesUi);
    syncAmountVariesUi();

    function syncPaymentMode(){
        var mode = recurring;
        radios.forEach(function(r){ if(r.checked) mode = r.value; });
        var isRec = mode === recurring;
        if(yearInp){
            yearInp.disabled = !isRec;
            yearInp.required = isRec;
        }
        if(cadenceSel){
            cadenceSel.disabled = !isRec;
            cadenceSel.required = isRec;
        }
        if(yearField) yearField.style.opacity = isRec ? '' : '0.55';
        if(cadenceField) cadenceField.style.opacity = isRec ? '' : '0.55';
        if(amtLabel) amtLabel.textContent = isRec ? 'Amount' : 'Payment amount';
        if(amtHint) amtHint.textContent = isRec ? 'per billing cycle' : 'one-time total';
        if(dueHint) dueHint.textContent = isRec ? 'optional · schedule anchor' : 'required for one-time (or use first installment)';
    }
    radios.forEach(function(r){ r.addEventListener('change', syncPaymentMode); });
    syncPaymentMode();
    syncCategoryOther();

    var rpCb = document.getElementById('bill-rental-related');
    var rentalWrap = document.getElementById('bill-rental-link-wrap');
    var rentalSel = document.getElementById('bill-rental-id');
    var toggleLabel = document.getElementById('bill-rental-toggle-label');
    function syncRentalLink(){
        if(!rpCb) return;
        var on = rpCb.checked;
        if(toggleLabel) toggleLabel.classList.toggle('bill-rental-toggle--on', on);
        if(rentalWrap) rentalWrap.hidden = !on;
        if(rentalSel) rentalSel.disabled = !on;
    }
    rpCb && rpCb.addEventListener('change', syncRentalLink);
    syncRentalLink();
})();
</script>
