<?php

namespace SilverStripe\GraphQL\Tests\Modules\Versioned\Plugins;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

class UnversionedWithField extends DataObject implements TestOnly
{
    private static $table_name = 'GraphQLVersionedTest_UnversionedWithField';

    private static $db = [
        'Version' => 'Varchar(255)'
    ];
}
