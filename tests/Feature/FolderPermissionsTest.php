<?php

namespace Tests\Feature;

use App\Models\FolderMaster;
use App\Models\FolderPermission;
use App\Models\User;
use App\Traits\HashIds;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The folder browser's listing and the folder actions follow each user's folder permissions on the
 * server, and refusals come back as clear messages. Runs against the local database inside a
 * transaction that is rolled back, with a fake S3.
 */
class FolderPermissionsTest extends TestCase
{
    use DatabaseTransactions, HashIds;

    private FolderMaster $root;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
        $this->root = FolderMaster::where('slug', 'root')->firstOrFail();
    }

    public function test_users_only_see_and_open_what_they_may_view(): void
    {
        $shared = $this->folder('zz_shared');
        $inner = $this->folder('zz_inner', $shared);
        $closed = $this->folder('zz_closed');
        $behindClosed = $this->folder('zz_behind_closed', $closed);
        $this->actingAsUser([$shared->id => ['view'], $inner->id => ['view'], $behindClosed->id => ['view']]);

        $this->assertSame(['zz_shared'], $this->names($this->getJson('/api/folders/browse')));
        $this->assertSame(['zz_inner'], $this->names($this->getJson('/api/folders/browse?folder='.$this->encode($shared))));
        // Refusals don't name the folder (ids can be guessed); broken links are "not found".
        $this->getJson('/api/folders/browse?folder='.$this->encode($closed))
            ->assertForbidden()
            ->assertJson(['message' => 'You do not have view permission on this folder or file.']);
        $this->getJson('/api/folders/browse?folder=not-a-real-id')->assertNotFound();
        // Search only finds what the user could reach by opening folders.
        $this->assertSame([], $this->names($this->getJson('/api/folders/browse?search=zz_behind')));
        $this->assertSame(['zz_inner'], $this->names($this->getJson('/api/folders/browse?search=zz_inn')));
    }

    public function test_folder_actions_need_the_matching_permission(): void
    {
        $folder = $this->folder('zz_folder');
        $this->actingAsUser([$folder->id => ['view', 'info']]);
        $id = $this->encode($folder);

        $this->getJson("/api/folders/folder-info/$id")->assertOk();
        $this->deleteJson("/api/folders/$id")->assertForbidden();
        $this->getJson("/api/folders/download-file/$id")->assertForbidden();
        $this->putJson("/api/folders/$id", ['id' => $id, 'name' => 'zz_renamed', 'resource_module' => 'software'])->assertForbidden();
        $this->postJson('/api/folders', ['parent_id' => $id, 'name' => 'zz_child', 'resource_module' => 'software'])->assertForbidden();
        $this->getJson("/api/folders/folder-permission-users/$id?fromadd=false")->assertForbidden();
        $this->assertDatabaseHas('folder_masters', ['id' => $folder->id, 'name' => 'zz_folder']);
    }

    public function test_superusers_cannot_rename_or_delete_the_root_folder(): void
    {
        $this->signIn($this->user(User::SUPER_USER));
        $id = $this->encode($this->root);

        $this->deleteJson("/api/folders/$id")->assertForbidden();
        $this->putJson("/api/folders/$id", ['id' => $id, 'name' => 'zz_root', 'resource_module' => 'all'])->assertForbidden();
        $this->getJson("/api/folders/folder-info/$id")->assertOk();
    }

    public function test_listing_takes_file_sizes_and_times_from_s3(): void
    {
        $folder = $this->folder('zz_files');
        $this->folder('report.csv', $folder, FolderMaster::RESOURCE_TYPE_FILE);
        Storage::disk('s3')->put("$folder->path/report.csv", 'a,b,c');
        $this->signIn($this->user(User::SUPER_USER));

        $item = $this->getJson('/api/folders/browse?folder='.$this->encode($folder))->assertOk()->json('data.items.0');

        $this->assertSame('report.csv', $item['name']);
        $this->assertSame(5, $item['size']);
        $this->assertNotNull($item['last_modified']);
    }

    public function test_downloading_an_empty_folder_says_so_and_is_not_counted(): void
    {
        $folder = $this->folder('zz_empty');
        $this->signIn($this->user(User::SUPER_USER));

        $this->getJson('/api/folders/download-file/'.$this->encode($folder))
            ->assertStatus(422)
            ->assertJson(['message' => 'zz_empty is empty, there is nothing to download.']);
        $this->assertEquals(0, $folder->fresh()->times_downloaded);
    }

    private function folder(string $name, ?FolderMaster $parent = null, string $type = FolderMaster::RESOURCE_TYPE_FOLDER): FolderMaster
    {
        $parent = $parent ?? $this->root;

        return FolderMaster::create([
            'parent_id' => $parent->id,
            'name' => $name,
            'depth' => $parent->depth + 1,
            'path' => "$parent->path/$name",
            'resource_type' => $type,
            'resource_module' => 'software',
            'created_at' => now(),
        ]);
    }

    private function user(string $role): User
    {
        $user = User::create([
            'first_name' => 'Folder',
            'last_name' => 'Test',
            'email' => uniqid('folder-test-').'@example.com',
            'password' => bcrypt('password'),
            'is_active' => 1,
        ]);
        $user->assignRole($role);

        return $user;
    }

    /**
     * Signs in a new user with role "user" and these folder permissions, as [folder id => [actions]].
     */
    private function actingAsUser(array $permissions): void
    {
        $user = $this->user(User::USER);
        foreach ($permissions as $folderId => $actions) {
            FolderPermission::create(['folder_id' => $folderId, 'user_id' => $user->id, 'created_at' => now()]
                + array_merge(array_fill_keys(FolderMaster::PERMISSION_KEYS, 0), array_fill_keys($actions, 1)));
        }
        $this->signIn($user);
    }

    /**
     * A real API token, as the app's token-expiry middleware updates the token on every request.
     */
    private function signIn(User $user): void
    {
        $this->withToken($user->createToken('folder-test')->plainTextToken);
    }

    private function names($response): array
    {
        return collect($response->assertOk()->json('data.items'))->pluck('name')->all();
    }
}
