<?php

namespace SilverStripe\GraphQL\Tests\Modules\AssetAdmin;

use SilverStripe\GraphQL\Modules\AssetAdmin\Resolvers\AssetAdminResolver;
use SilverStripe\GraphQL\Tests\Modules\AssetAdmin\FileExtension;
use SilverStripe\GraphQL\Tests\Modules\AssetAdmin\FolderExtension;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\Dev\SapphireTest;
use Silverstripe\Assets\Dev\TestAssetStore;
use SilverStripe\GraphQL\Schema\Schema;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Most of the search functionality is covered in {@link FileFilterInputTypeCreatorTest}
 */
class ReadFileUsageQueryCreatorTest extends SapphireTest
{

    protected $usesDatabase = true;

    protected static $fixture_file = 'fixtures.yml';

    protected function setUp(): void
    {
        parent::setUp();
        TestAssetStore::activate('AssetAdminTest');
        File::add_extension(FileExtension::class);
        Folder::add_extension(FolderExtension::class);
    }

    protected function tearDown(): void
    {
        File::remove_extension(FileExtension::class);
        Folder::remove_extension(FolderExtension::class);

        TestAssetStore::reset();
        parent::tearDown();
    }

    public static function fileUsageDataProvider()
    {
        return [
            [File::class, 'rootfile', 2, 'rootfile file is used twice'],
            [File::class, 'file2', 0, 'file2 is not used'],
            [File::class, 'disallowCanView', false, 'disallowCanView should provide file usage data'],
            [Folder::class, 'folder1', 2, 'folder1 contains files used 2 times'],
            [Folder::class, 'folder1.1', 0, 'folder1.1 does not contained any used files'],
        ];
    }

    /**
     * @param string $class
     * @param string $fixture
     * @param int|false $expectedCount
     * @param $message
     */
    #[DataProvider('fileUsageDataProvider')]
    public function testUsageCount(string $class, string $fixture, $expectedCount, $message)
    {
        $id = $this->idFromFixture($class, $fixture);
        $this->assertUsageCount($id, $expectedCount, $message);
    }

    public function testUsageCountForNonExistentFile()
    {
        $this->expectException('InvalidArgumentException');
        $this->assertUsageCount(999, false, 'File 999 should throw an exception because it does not exists.');
    }

    /**
     * @param array $args
     * @param array $context
     * @return array
     */
    protected function getResultsForSearch($args, $context = null)
    {
        $context = $context ? $context : ['currentUser' => null];
        return AssetAdminResolver::resolveReadFileUsage(null, $args, $context, new FakeResolveInfo());
    }

    /**
     * Assert the file usage of the provided file ID.
     * @param string $fixture
     * @param int|false $expectedCount $expectedCount or false, if no result should be returned
     */
    private function assertUsageCount($id, $expectedCount, $message)
    {
        $actual = $this->getResultsForSearch(['ids' => [$id]]);
        if ($expectedCount === false) {
            $this->assertEmpty($actual, $message);
        } else {
            $expected = [[
                'id' => $id,
                'inUseCount' => $expectedCount
            ]];
            $this->assertEquals($expected, $actual, $message);
        }
    }
}