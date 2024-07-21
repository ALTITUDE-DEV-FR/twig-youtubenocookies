<?php 
/**
 * Twig LazyContent escape cookies and lazyload
 * use lazy_content
 */
namespace App\ThirdParty\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class LazyContentExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('lazy_content', [$this, 'LazyContent'], ['is_safe' => ['html']]),
        ];
    }

    public function LazyContent($content)
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        /**
        * [IFRAMES] Transform all iframes youtube.com to youtube-nocookies.com and use Vanilla Lazyload JS or loading lazy !
        */
        $iframes = $dom->getElementsByTagName('iframe');
        foreach ($iframes as $iframe) {
            $src = $iframe->getAttribute('src');
            if (strpos($src, '//www.youtube.com') !== false) {
                $newSrc = str_replace('//www.youtube.com/', '//www.youtube-nocookie.com/', $src);
            } elseif (strpos($src, '//youtube.com') !== false) {
                $newSrc = str_replace('//youtube.com/', '//www.youtube-nocookie.com/', $src);
            } else {
                continue;
            }

            // if you use a vanilla lazyload js or comment this
            $iframe->setAttribute('data-src', $newSrc);
            $iframe->removeAttribute('src');
            $iframe->setAttribute('class', trim($iframe->getAttribute('class') . ' lazy'));

            // native browsers
            $iframe->setAttribute('loading', 'lazy');
        }

        /**
        * [Pictures] Vanilla Lazyload JS or Browser loading lazy
        */
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            if ($src) {

                // vanilla lazyload js method
                $img->setAttribute('data-src', $src);
                $img->removeAttribute('src');
                $img->setAttribute('class', trim($img->getAttribute('class') . ' lazyload androModal'));

                // for native browsers
                $img->setAttribute('loading', 'lazy');
            }
        }

        return $dom->saveHTML();
    }
    
}
