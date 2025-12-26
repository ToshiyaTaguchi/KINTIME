<div class="attendance__action-wrapper">
    <form method="POST" action="{{ route('attendance.store') }}" class="attendance__action-form"
        onsubmit="this.querySelectorAll('button').forEach(b => b.disabled = true);">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <button class="attendance__button {{ $class }}">
            {{ $label }}
        </button>
    </form>
</div>