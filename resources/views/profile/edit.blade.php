<x-admin-layout title="Profil Saya">

    <div style="max-width: 700px;" class="mx-auto">

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success">Profil berhasil diperbarui.</div>
        @endif
        @if (session('status') === 'password-updated')
            <div class="alert alert-success">Password berhasil diperbarui.</div>
        @endif

        <div class="card">
            <div class="card-header"><h3 class="card-title">Informasi Profil</h3></div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Ubah Password</h3></div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger"><h3 class="card-title text-white">Hapus Akun</h3></div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</x-admin-layout>