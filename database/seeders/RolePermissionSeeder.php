<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeder untuk roles dan permissions
 * Membuat struktur role-permission untuk sistem Diskominfo
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Daftar modul dan aksi yang tersedia
     */
    protected array $modules = [
        // Content Management
        'berita' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'publish'],
        'halaman-statis' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'publish'],
        'pengumuman' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'publish'],
        'agenda' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'publish'],

        // Dokumen & Publikasi
        'dokumen' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],
        'infografis' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],
        'statistik' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],
        'open-data' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],

        // Layanan Publik
        'ppid' => ['view-any', 'view', 'create', 'update', 'respond', 'delete', 'export'],
        'pengaduan' => ['view-any', 'view', 'respond', 'update', 'delete', 'export'],
        'layanan-tik' => ['view-any', 'view', 'create', 'update', 'delete', 'respond'],

        // Media
        'foto' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],
        'video' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete'],
        'file-manager' => ['view-any', 'view', 'upload', 'delete'],

        // User & Role Management
        'user' => ['view-any', 'view', 'create', 'update', 'delete', 'restore', 'force-delete', 'impersonate'],
        'role' => ['view-any', 'view', 'create', 'update', 'delete'],
        'permission' => ['view-any', 'view'],

        // System
        'activity-log' => ['view-any', 'view', 'export', 'delete'],
        'system-setting' => ['view-any', 'view', 'update'],
        'backup' => ['view-any', 'create', 'download', 'delete'],
    ];

    /**
     * Daftar roles dengan default permissions
     */
    protected array $roles = [
        'super-admin' => '*', // Semua permission
        'admin' => [
            'berita.*',
            'halaman-statis.*',
            'pengumuman.*',
            'agenda.*',
            'dokumen.*',
            'infografis.*',
            'statistik.*',
            'open-data.*',
            'ppid.view-any',
            'ppid.view',
            'ppid.export',
            'pengaduan.view-any',
            'pengaduan.view',
            'pengaduan.export',
            'layanan-tik.*',
            'foto.*',
            'video.*',
            'file-manager.*',
            'user.view-any',
            'user.view',
            'user.create',
            'user.update',
            'role.view-any',
            'role.view',
            'role.create',
            'role.update',
            'permission.view-any',
            'permission.view',
            'activity-log.view-any',
            'activity-log.view',
            'system-setting.view-any',
            'system-setting.view',
            'system-setting.update',
        ],
        'operator' => [
            'berita.view-any',
            'berita.view',
            'berita.create',
            'berita.update',
            'halaman-statis.view-any',
            'halaman-statis.view',
            'halaman-statis.create',
            'halaman-statis.update',
            'pengumuman.view-any',
            'pengumuman.view',
            'pengumuman.create',
            'pengumuman.update',
            'agenda.view-any',
            'agenda.view',
            'agenda.create',
            'agenda.update',
            'dokumen.view-any',
            'dokumen.view',
            'dokumen.create',
            'dokumen.update',
            'foto.view-any',
            'foto.view',
            'foto.create',
            'foto.update',
            'video.view-any',
            'video.view',
            'video.create',
            'video.update',
            'file-manager.view-any',
            'file-manager.view',
            'file-manager.upload',
        ],
        'ppid' => [
            'ppid.*',
            'dokumen.view-any',
            'dokumen.view',
            'dokumen.create',
            'dokumen.update',
            'file-manager.view-any',
            'file-manager.view',
            'file-manager.upload',
        ],
        'humas' => [
            'berita.*',
            'pengumuman.*',
            'agenda.*',
            'foto.*',
            'video.*',
            'file-manager.*',
        ],
    ];

    /**
     * Jalankan seeder
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat semua permissions
        $this->createPermissions();

        // Buat roles dan assign permissions
        $this->createRoles();

        // Buat Super Admin user
        $this->createSuperAdmin();

        $this->command->info('Roles dan permissions berhasil dibuat!');
    }

    /**
     * Buat semua permissions dari daftar modul
     */
    protected function createPermissions(): void
    {
        foreach ($this->modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $this->command->info('Permissions dibuat: ' . Permission::count());
    }

    /**
     * Buat roles dan assign permissions
     */
    protected function createRoles(): void
    {
        foreach ($this->roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            if ($permissions === '*') {
                // Super Admin mendapat semua permission
                $role->syncPermissions(Permission::all());
            } else {
                // Parse permission patterns
                $resolvedPermissions = $this->resolvePermissions($permissions);
                $role->syncPermissions($resolvedPermissions);
            }

            $this->command->info("Role '{$roleName}' dibuat dengan " . $role->permissions->count() . " permissions");
        }
    }

    /**
     * Resolve permission patterns (e.g., 'berita.*')
     */
    protected function resolvePermissions(array $patterns): array
    {
        $permissions = [];

        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '.*')) {
                // Wildcard pattern - ambil semua permissions untuk modul
                $module = str_replace('.*', '', $pattern);
                $modulePermissions = Permission::where('name', 'like', "{$module}.%")->pluck('name')->toArray();
                $permissions = array_merge($permissions, $modulePermissions);
            } else {
                // Specific permission
                $permissions[] = $pattern;
            }
        }

        return array_unique($permissions);
    }

    /**
     * Buat akun Super Admin default
     */
    protected function createSuperAdmin(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@tanggamus.go.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@2026'),
                'nip' => '000000000000000000',
                'jabatan' => 'Administrator Sistem',
                'bidang' => 'Diskominfo',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole('super-admin');

        $this->command->info('Super Admin created: superadmin@tanggamus.go.id');
        $this->command->warn('Default password: SuperAdmin@2026 - SEGERA GANTI SETELAH LOGIN!');
    }
}
