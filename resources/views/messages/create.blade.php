@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success">Compose Message</h2>
            <a href="{{ route('messages.inbox') }}" class="btn btn-outline-success">Back to Inbox</a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label text-success">To</label>
                <div class="position-relative">
                    <input type="hidden" name="receiver_id" id="receiver_id" value="{{ isset($user) ? $user->id : '' }}" required>
                    <input type="text"
                           class="form-control"
                           id="receiver_search"
                           placeholder="Start typing a username..."
                           value="{{ isset($user) ? $user->name : '' }}"
                           autocomplete="off"
                           required>
                    <div id="user-suggestions" class="list-group position-absolute w-100 shadow" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-success">Subject</label>
                <input type="text" class="form-control" name="subject" value="{{ old('subject', $subject ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-success">Message</label>
                <textarea class="form-control" name="content" rows="6" required>{{ old('content') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-success">Attachments</label>
                <input type="file" class="form-control" name="attachments[]" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip">
                <small class="text-muted">Max 10MB per file. Allowed: images, GIFs, videos, PDF, DOC, TXT, ZIP</small>
            </div>

            <button type="submit" class="btn btn-success">Send Message</button>
            <a href="{{ route('messages.inbox') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('receiver_search');
    const receiverIdInput = document.getElementById('receiver_id');
    const suggestionsDiv = document.getElementById('user-suggestions');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Clear the receiver_id when user types (they need to select from suggestions)
        receiverIdInput.value = '';

        clearTimeout(debounceTimer);

        if (query.length < 1) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('users.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(users => {
                    suggestionsDiv.innerHTML = '';

                    if (users.length === 0) {
                        suggestionsDiv.innerHTML = '<div class="list-group-item list-group-item-dark text-muted">No users found</div>';
                        suggestionsDiv.style.display = 'block';
                        return;
                    }

                    users.forEach(user => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action list-group-item-dark';
                        item.textContent = user.name;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            searchInput.value = user.name;
                            receiverIdInput.value = user.id;
                            suggestionsDiv.style.display = 'none';
                        });
                        suggestionsDiv.appendChild(item);
                    });

                    suggestionsDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error searching users:', error);
                });
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });

    // Form validation - ensure a user is selected
    searchInput.closest('form').addEventListener('submit', function(e) {
        if (!receiverIdInput.value) {
            e.preventDefault();
            alert('Please select a valid recipient from the suggestions.');
            searchInput.focus();
        }
    });
});
</script>
@endsection
