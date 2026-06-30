@extends('admin.layouts.app')
@section('title', 'Add New Staff')
@section('content')
<div style="max-width: 600px;">
    <div class="card">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h2 style="font-size: 1.2rem; font-weight: 700;">Add New Staff</h2>
        </div>
        
        <form action="{{ route('admin.staff.store') }}" method="POST" style="padding: 20px;">
            @csrf
            
            @if($errors->any())
                <div class="alert" style="background:#ffeaa7; color:#d63031; margin-bottom: 20px;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Password</label>
                <input type="password" name="password" required minlength="8" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem;">
                <small style="color: var(--text-muted);">Minimum 8 characters</small>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Role</label>
                <select name="role" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 1rem; background: white;">
                    <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client (User Biasa)</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn-primary" style="flex: 1;">Create Account</button>
                <a href="{{ route('admin.staff.index') }}" style="flex: 1; text-align: center; background: #e5e7eb; color: var(--text-main); text-decoration: none; padding: 10px; border-radius: 8px; font-weight: 600;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
