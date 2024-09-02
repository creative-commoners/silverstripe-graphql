<?php

namespace SilverStripe\GraphQL\Tests\Modules\AssetAdmin;

use SilverStripe\GraphQL\Modules\AssetAdmin\Resolvers\AssetAdminResolver;
use SilverStripe\GraphQL\Tests\Modules\AssetAdmin\FileExtension;
use SilverStripe\GraphQL\Tests\Modules\AssetAdmin\FolderExtension;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\GraphQL\Schema\Schema;
use SilverStripe\GraphQL\Tests\Modules\AssetAdmin\FakeResolveInfo;

class CreateFolderMutationCreatorTest extends SapphireTest
{

    protected static $fixture_file = 'fixtures.yml';

    protected function setUp(): void
    {
        parent::setUp();
        File::add_extension(FileExtension::class);
        Folder::add_extension(FolderExtension::class);
    }

    protected function tearDown(): void
    {
        File::remove_extension(FileExtension::class);
        Folder::remove_extension(FolderExtension::class);

        parent::tearDown();
    }

    public function testItCreatesFolder()
    {
        $folder1 = $this->objFromFixture(Folder::class, 'folder1');

        $args = [
            'folder' => [
                'parentID' => $folder1->ID,
                'name' => 'testItCreatesFolder',
            ]
        ];
        $newFolder = AssetAdminResolver::resolveCreateFolder(null, $args, null, new FakeResolveInfo());
        $this->assertNotNull($newFolder);
        $this->assertEquals($folder1->ID, $newFolder->ParentID);
        $this->assertEquals('testItCreatesFolder', $newFolder->Name);
    }

    public function testItRestrictsCreateFolderByCanCreate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('create not allowed');
        $folder1 = $this->objFromFixture(Folder::class, 'folder1');

        $args = [
            'folder' => [
                'parentID' => $folder1->ID,
                'name' => 'disallowCanCreate',
            ]
        ];
        AssetAdminResolver::resolveCreateFolder(null, $args, null, new FakeResolveInfo());
    }
}
