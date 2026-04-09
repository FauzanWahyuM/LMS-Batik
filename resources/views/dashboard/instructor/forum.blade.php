@extends('dashboard.layouts.app')

@section('dashboard-content')
    @include('components.notification-modal')

    <section class="space-y-6">
        @include('forum.discussions-list', [
            'discussions' => $discussions ?? collect(),
            'user' => $user ?? [],
            'modules' => $modules ?? collect(),
            'selectedModuleSlug' => $selectedModuleSlug ?? '',
            'showModuleFilter' => true,
        ])
    </section>
@endsection
