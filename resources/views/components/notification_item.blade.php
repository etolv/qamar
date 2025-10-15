@php
    $notification_image = $notification->user
        ? ($notification->user->getFirstMedia('profile')
            ? $notification->user->getFirstMediaUrl('profile')
            : asset('assets/img/front-pages/icons/user.png'))
        : asset('assets/img/front-pages/icons/user.png');
@endphp
<li class="list-group-item list-group-item-action dropdown-notifications-item 'marked-as-read'">
    <div class="d-flex">
        <div class="flex-shrink-0 me-3">
            <div class="avatar">
                <img src="{{ $notification_image }}" alt class="h-auto rounded-circle">
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-1">{{ $notification->name }}</h6>
            <p class="mb-0">{{ $notification->body }}</p>
            <small class="text-muted">{{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
        </div>
        <div class="flex-shrink-0 dropdown-notifications-actions">
            <a href="{{ route('notification.show', $notification->id) }}" class="dropdown-notifications-read"><span
                    class="badge badge-dot"></span></a>
            <a href="{{ route('notification.show', $notification->id) }}" class="dropdown-notifications-archive"><i
                    class="fa-regular fa-eye"></i></a>
        </div>
    </div>
</li>
