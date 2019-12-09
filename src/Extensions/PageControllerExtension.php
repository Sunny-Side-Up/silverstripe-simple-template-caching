<?php

namespace Sunnysideup\SimpleTemplateCaching\Extensions;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Security;

class PageControllerExtension extends Extension
{
    private static $_cache_key_content = null;

    private static $_cache_key_footer = null;

    private static $_can_cache_content = null;

    public function CacheKeyContent(): string
    {
        if (self::$_cache_key_content === null) {
            self::$_cache_key_content = 'C_' . $this->CacheKeyHeader();
        }

        return self::$_cache_key_content;
    }

    public function CacheKeyHeader(): string
    {
        return 'H_' .
            $this->CacheKeyFooter() . '_' .
            'ID_' . $this->owner->ID . '_';
    }

    public function CacheKeyFooter(): string
    {
        if (self::$_cache_key_footer === null) {
            self::$_cache_key_footer = 'Page_' .
            SiteTree::get()->count() . '_' .
            strtotime(SiteTree::get()->Max('LastEdited'));
        }

        return 'F_' . self::$_cache_key_footer;
    }

    public function HasCacheKeys(): bool
    {
        if (self::$_can_cache_content === null) {
            if ($this->owner->request->param('Action')
                ||
                $this->owner->request->param('ID')
                ||
                count($this->owner->request->requestVars())
                ||
                Security::getCurrentUser()
            ) {
                self::$_can_cache_content = false;
            } else {
                self::$_can_cache_content = true;
            }
        }
        return self::$_can_cache_content;
    }
}
