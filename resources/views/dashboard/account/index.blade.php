@extends('layouts/layoutMaster')

@section('title', 'Accounts')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js'])
@endsection

@section('page-style')
    <style>
        .select2-dropdown {
            z-index: 9999 !important;
            /* Adjust this value as needed */
        }
    </style>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('.account_select2').select2();
            $('.to_account_select2').select2();
            $('.is_debit_select2').select2();
            $('#searchAccounts').on('keyup', function() {
                var searchValue = $(this).val().toLowerCase();

                if (searchValue === '') {
                    // If search is empty, reset the tree
                    $('#accountsTree .list-group-item').show(); // Show all items
                    $('.collapse').removeClass('show'); // Close all collapses
                    return;
                }

                // Loop through each account name
                $('#accountsTree .list-group-item').each(function() {
                    var accountName = $(this).find('.view-transactions').text().toLowerCase();
                    var parentCollapse = $(this).closest('.collapse');

                    // Check if the account name includes the search value
                    if (accountName.includes(searchValue)) {
                        $(this).show(); // Show matching account
                        if (parentCollapse.length) {
                            parentCollapse.addClass('show'); // Open parent collapses
                        }
                    } else {
                        $(this).hide(); // Hide non-matching accounts
                    }
                });

                // Close collapses that have no visible children
                $('.collapse').each(function() {
                    if ($(this).find('.list-group-item:visible').length === 0) {
                        $(this).removeClass('show');
                    }
                });
            });
            $('.view-transactions').on('click', function(e) {
                e.preventDefault();
                $('[id^="account-name-"]').removeClass('fw-bolder text-primary');
                $(this).addClass('fw-bolder text-primary');
                let accountID = $(this).data('id');
                let name = $(this).data('name');
                let slug = $(this).data('slug');
                // add transaction modal
                $('.add-transaction').removeClass('d-none');
                $('#from_account_id').val(accountID);
                $('.from_account_text').text(`${name} (${slug})`);
                $.ajax({
                    url: route('account.transaction', {
                        account: accountID
                    }),
                    method: 'GET',
                    success: function(data) {
                        console.log(data);
                        var transactionsDiv = $('#transactions');
                        var transactionTable = `<h5>Transactions for ${name} (${slug})</h5>`;

                        // Create the table for transactions
                        transactionTable += `
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>From Account</th>
                                        <th>To Account</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        data.forEach(function(transaction) {
                            transactionTable += `
                                <tr>
                                    <td>${transaction.description}</td>
                                    <td>${transaction.from_account.name}</td>
                                    <td>${transaction.to_account.name}</td>
                                    <td>${transaction.amount} {{ _t('SAR') }}</td>
                                </tr>
                            `;
                        });

                        transactionTable += `
                                </tbody>
                            </table>
                        `;

                        // Append the table to the container
                        transactionsDiv.html(transactionTable);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        $('#transactions').html(
                            '<p class="text-danger">Error loading transactions.</p>');
                    }
                });
            });
        });
    </script>

@endsection

@section('content')
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <h4>{{ _t('Accounts Tree') }}</h4>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#newAccountModal">
                            {{ _t('Add Account') }}
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" id="searchAccounts" class="form-control"
                        placeholder="{{ _t('Search accounts') }}...">
                </div>
                <div class="list-group" id="accountsTree">
                    @foreach ($accounts as $account)
                        @if ($account['account_id'] === null)
                            @include('components.account_tree', ['account' => $account, 'level' => 0])
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Transactions -->
            <div class="col-md-8">
                <div class="d-flex justify-content-between">
                    <h4>{{ _t('Account Transactions') }}</h4>
                    <button type="button" class="btn btn-primary add-transaction d-none" data-bs-toggle="modal"
                        data-bs-target="#newTransactionModal">
                        {{ _t('Add Transaction') }}
                    </button>
                </div>
                <div id="transactions">
                    <p class="text-muted">{{ _t('Click on an account to view its transactions') }}.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-onboarding modal fade animate__animated" id="newTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('New Transaction') }}</h4>
                        <h5 class="onboarding-title text-body">
                            {{ _t('Transaction from') }} <span class="from_account_text text-muted"></span>
                        </h5>
                        <form method="POST" action="{{ route('transaction.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="from_account_id" id="from_account_id" />
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('To Account') }}</label>
                                <div class="col-sm-10">
                                    <select id="to_account_id" name="to_account_id" class="to_account_select2 form-select"
                                        data-allow-clear="true">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->name . " ($account->slug)" }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_account_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Amount') }}</label>
                                <div class="col-sm-10">
                                    <input type="number" placeholder="{{ _t('Amount') }}" step="any" name="amount"
                                        id="amount" class="form-control" required />
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Description') }}</label>
                                <div class="col-sm-10">
                                    <textarea name="description" placeholder="{{ _t('Description') }}" id="description" class="form-control" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">{{ _t('Create') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-onboarding modal fade animate__animated" id="newAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ _t('New Account') }}</h4>
                        <form method="POST" action="{{ route('account.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Name') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="{{ _t('Name') }}" name="name"
                                        id="name" class="form-control" required />
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Number') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="{{ _t('Number') }}" name="number"
                                        id="number" class="form-control" required />
                                    @error('number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Slug') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <input type="text" placeholder="{{ _t('Slug') }}" name="slug"
                                        id="slug" class="form-control" required />
                                    @error('slug')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"
                                    for="basic-icon-default-email">{{ _t('Parent Account') }}</label>
                                <div class="col-sm-10">
                                    <select id="account_id" name="account_id" class="account_select2 form-select"
                                        data-allow-clear="true">
                                        <option value="" selected>{{ _t('None') }}</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->name . " ($account->slug)" }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('account_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="basic-icon-default-email">{{ _t('Type') }}
                                    *</label>
                                <div class="col-sm-10">
                                    <select id="is_debit" name="is_debit" class="is_debit_select2 form-select">
                                        <option value="1" selected>{{ _t('Debit') }}</option>
                                        <option value="0">{{ _t('Credit') }}</option>
                                    </select>
                                    @error('is_debit')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">{{ _t('Create') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                </div>
            </div>
        </div>
    </div>
    <!-- end modal -->
@endsection
