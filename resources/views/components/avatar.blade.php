@props(['user'])
@if ($user->avatar)
    <img src="{{ $user->avatar }}" alt="" referrerpolicy="no-referrer" {{ $attributes->merge(['class' => 'rounded-full object-cover']) }}>
@else
    <span {{ $attributes->merge(['class' => 'grid place-items-center rounded-full bg-gradient-to-br from-accent-400 to-accent-600 text-xs font-bold text-white']) }}>{{ $user->initials() }}</span>
@endif
