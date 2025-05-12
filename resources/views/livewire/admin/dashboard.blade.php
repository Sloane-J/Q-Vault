<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    @if(auth()->user()->isAdmin())
    {{-- Admin-specific dashboard content --}}
@elseif(auth()->user()->isStudent())
    {{-- Student-specific dashboard content --}}
@endif
</div>
