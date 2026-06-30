@extends('admin.layouts.app')
@section('title', 'Staff Management')
@section('content')
<div class="card">
    <div style="padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
        <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main);">Staff Management</h2>
        <a href="{{ route('admin.staff.create') }}" class="btn-primary" style="text-decoration:none;">+ Add New Staff</a>
    </div>
    
    @if(session('error'))
        <div class="alert" style="background:#ffeaa7; color:#d63031; margin: 20px;">{{ session('error') }}</div>
    @endif

    <div style="padding: 20px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 0.9rem;">
                    <th style="padding: 15px 10px;">Name</th>
                    <th style="padding: 15px 10px;">Email</th>
                    <th style="padding: 15px 10px;">Role</th>
                    <th style="padding: 15px 10px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $user)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 15px 10px; font-weight: 600;">{{ $user->name }}</td>
                    <td style="padding: 15px 10px; color: var(--text-muted);">{{ $user->email }}</td>
                    <td style="padding: 15px 10px;">
                        @if($user->role == 'admin')
                            <span style="background: var(--primary); color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Admin</span>
                        @elseif($user->role == 'cashier')
                            <span style="background: var(--yellow); color: #000; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Cashier</span>
                        @else
                            <span style="background: #e5e7eb; color: var(--text-main); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Client</span>
                        @endif
                    </td>
                    <td style="padding: 15px 10px; text-align: right;">
                        <a href="{{ route('admin.staff.edit', $user->id) }}" style="color: var(--text-main); text-decoration: none; font-weight: 600; font-size: 0.9rem; margin-right: 15px;"><i class="fa-solid fa-pen"></i> Edit</a>
                        
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.staff.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus staff ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: var(--primary); font-weight: 600; font-size: 0.9rem; cursor: pointer; font-family: inherit;"><i class="fa-solid fa-trash"></i> Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
