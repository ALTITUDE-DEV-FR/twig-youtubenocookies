<?php
/**
* add this snippet to your function.php in your theme directory
* ex: LazyContent(content());
* ex: LazyContent('your custom html block, acf, html..);
*/
 function LazyContent($content)
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        /**
        * [IFRAMES] Transform all iframes youtube.com to youtube-nocookies.com and use Vanilla Lazyload JS or loading lazy !
        * if you need others links add elseif youtube.be.. 
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
