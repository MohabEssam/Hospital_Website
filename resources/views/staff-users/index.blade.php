@extends('layouts.app')

@section('content')
  <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
    <div>
      <p class="mb-0 text-muted small">Role-based access management</p>
      <h4 class="fw-bold mb-0">Staff Users</h4>
    </div>
    <a href="{{ route('staff-users.create') }}" class="btn btn-dark btn-sm">Create User</a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="small text-muted fw-semibold">Public ID</th>
            <th class="small text-muted fw-semibold">Name</th>
            <th class="small text-muted fw-semibold">Email</th>
            <th class="small text-muted fw-semibold">Phone</th>
            <th class="small text-muted fw-semibold">Gender</th>
            <th class="small text-muted fw-semibold">Role</th>
            <th class="small text-muted fw-semibold">Created</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td class="fw-semibold">{{ $user->public_id }}</td>
              <td>{{ $user->name }}</td>
              <td class="text-muted small">{{ $user->email }}</td>
              <td class="text-muted small">{{ $user->phone ?? 'Not recorded' }}</td>
              <td class="text-capitalize">{{ $user->gender ?? 'Not recorded' }}</td>
              <td><span class="badge bg-dark text-capitalize">{{ str($user->role)->replace('_', ' ') }}</span></td>
              <td class="small text-muted">{{ $user->created_at?->format('d M Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4 small">No staff users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @if($users->hasPages())
    <div class="mt-3">{{ $users->links('partials.pagination') }}</div>
  @endif
@endsection
