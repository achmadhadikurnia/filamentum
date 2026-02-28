<?php

namespace App\Support\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;
use Spatie\Csp\Scheme;

class FilamentumCspPreset implements Preset
{
    public function configure(Policy $policy): void
    {
        $policy
            ->add(Directive::BASE, Keyword::SELF)
            ->add(Directive::DEFAULT, Keyword::SELF)
            ->add(Directive::CONNECT, Keyword::SELF)
            ->add(Directive::FORM_ACTION, Keyword::SELF)
            ->add(Directive::FRAME_ANCESTORS, Keyword::SELF)
            ->add(Directive::OBJECT, Keyword::NONE)

            // Scripts: self + nonce + CDNs used by public pages
            ->add(Directive::SCRIPT, Keyword::SELF)
            ->add(Directive::SCRIPT, Keyword::UNSAFE_INLINE)
            ->add(Directive::SCRIPT, Keyword::UNSAFE_EVAL)
            ->add(Directive::SCRIPT, 'https://cdn.tailwindcss.com')
            ->add(Directive::SCRIPT, 'https://cdn.jsdelivr.net')

            // Styles: self + inline (Filament widgets use inline styles)
            ->add(Directive::STYLE, Keyword::SELF)
            ->add(Directive::STYLE, Keyword::UNSAFE_INLINE)
            ->add(Directive::STYLE, 'https://fonts.googleapis.com')
            ->add(Directive::STYLE, 'https://fonts.bunny.net')

            // Fonts: Google Fonts + Bunny Fonts + data: URIs
            ->add(Directive::FONT, Keyword::SELF)
            ->add(Directive::FONT, 'https://fonts.gstatic.com')
            ->add(Directive::FONT, 'https://fonts.bunny.net')
            ->add(Directive::FONT, Scheme::DATA)

            // Images: self + data: URIs + any HTTPS source
            ->add(Directive::IMG, Keyword::SELF)
            ->add(Directive::IMG, Scheme::DATA)
            ->add(Directive::IMG, Scheme::HTTPS)

            // Media
            ->add(Directive::MEDIA, Keyword::SELF);
    }
}
