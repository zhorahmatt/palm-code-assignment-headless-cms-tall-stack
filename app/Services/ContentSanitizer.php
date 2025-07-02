<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class ContentSanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        
        // Allow common HTML tags for rich content
        $config->set('HTML.Allowed', 
            'p,br,strong,b,em,i,u,h1,h2,h3,h4,h5,h6,' .
            'ul,ol,li,blockquote,a[href],img[src|alt|width|height],' .
            'table,thead,tbody,tr,td,th,div[class],span[class],' .
            'pre,code'
        );
        
        // Allow some CSS for styling
        $config->set('CSS.AllowedProperties', 'text-align,font-weight,font-style');
        
        // Set cache directory
        $config->set('Cache.SerializerPath', storage_path('app/htmlpurifier'));
        
        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(string $content): string
    {
        return $this->purifier->purify($content);
    }

    public function sanitizeForDisplay(string $content): string
    {
        // More restrictive sanitization for public display
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 
            'p,br,strong,b,em,i,h1,h2,h3,h4,h5,h6,' .
            'ul,ol,li,blockquote,a[href],img[src|alt]'
        );
        
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($content);
    }
}