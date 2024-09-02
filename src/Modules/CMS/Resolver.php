<?php

namespace SilverStripe\GraphQL\Modules\CMS;

use SilverStripe\CMS\Model\SiteTree;

class Resolver
{
    public static function resolveGetPageByLink($obj, array $args = [])
    {
        return SiteTree::get_by_link($args['link']);
    }
}
