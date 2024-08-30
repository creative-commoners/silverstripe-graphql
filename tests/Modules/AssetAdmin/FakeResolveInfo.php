<?php

namespace SilverStripe\GraphQL\Tests\Modules\AssetAdmin;

use GraphQL\Language\AST\OperationDefinitionNode;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

class FakeResolveInfo extends ResolveInfo
{
    public function __construct()
    {
        parent::__construct(
            new FieldDefinition(['name' => 'fake', 'type' => Type::string()]),
            new \ArrayObject(),
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
