<?php

namespace SilverStripe\GraphQL\Tests\Modules\Versioned\Fake;

use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ResolveInfo;

class FakeResolveInfo extends ResolveInfo
{
    public function __construct()
    {
        parent::__construct(
            new FieldDefinition(['name' => 'fake', 'type' => Type::string()]),
            new \ArrayObject,
            new ObjectType(['name' => 'fake']),
            [],
            new Schema([]),
            [],
            '',
            new OperationDefinitionNode([]),
            []
        );
    }
}
