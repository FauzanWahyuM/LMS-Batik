@extends('dashboard.layouts.app')

@section('dashboard-content')
    @include('components.notification-modal')

    <section class="space-y-6">
        @include('forum.discussions-list', [
            'discussions' => $discussions ?? collect(),
            'user' => $user ?? [],
        ])
    </section>
@endsection
