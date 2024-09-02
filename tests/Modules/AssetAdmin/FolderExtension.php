<?php

namespace SilverStripe\GraphQL\Tests\Modules\AssetAdmin;

use SilverStripe\Dev\TestOnly;
use SilverStripe\Core\Extension;

class FolderExtension extends Extension implements TestOnly
{
    public function canView($member = null, $context = array())
    {
        if ($this->owner->Name === 'disallowCanView') {
            return false;
        }
    }

    public function canEdit($member = null, $context = array())
    {
        if ($this->owner->Name === 'disallowCanEdit') {
            return false;
        }
    }

    public function canDelete($member = null, $context = array())
    {
        if ($this->owner->Name === 'disallowCanDelete') {
            return false;
        }
    }

    public function canCreate($member = null, $context = array())
    {
        if (isset($context['Name']) && $context['Name'] === 'disallowCanCreate') {
            return false;
        }
    }
}
