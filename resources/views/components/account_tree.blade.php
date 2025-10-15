@php
    $collapseId = 'collapse-' . $account['id'];
@endphp

<div class="list-group-item">
    <div class="d-flex justify-content-between align-items-center">
        <span class="d-flex justify-content-between w-100">
            <span class="text-muted view-transactions cursor-pointer" data-id="{{ $account->id }}"
                id="account-name-{{ $account->id }}" data-name="{{ $account->name }}" data-slug="{{ $account->slug }}">
                {{ $account['name'] }}
            </span>
            @if ($account['is_debit'])
                <span class="text-success"> {{ _t('Debit') }}</span>
            @else
                <span class="text-danger"> {{ _t('Credit') }}</span>
            @endif
        </span>
        @if (count($account['accounts']))
            <button class="btn btn-sm btn-link" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        @endif
    </div>

    @if (count($account['accounts']))
        <div class="collapse" id="{{ $collapseId }}">
            <div class="list-group ms-3 mt-2">
                @foreach ($account['accounts'] as $subAccount)
                    @include('components.account_tree', [
                        'account' => $subAccount,
                        'level' => $level + 1,
                    ])
                @endforeach
            </div>
        </div>
    @endif
</div>
