<?php

if (! function_exists('normalize_uploaded_content_html')) {
    function normalize_uploaded_content_html(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return $html;
        }

        return (string) preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i',
            function (array $matches): string {
                $source = $matches[2] ?? '';

                if ($source === '' || !preg_match('#(^|[\\\\/])storage([\\\\/]|$)#i', $source)) {
                    return $matches[0];
                }

                $parsedPath = parse_url($source, PHP_URL_PATH) ?: $source;
                $normalizedPath = str_replace('\\', '/', $parsedPath);
                $storagePosition = strpos($normalizedPath, '/storage/');

                if ($storagePosition === false) {
                    $storagePosition = strpos($normalizedPath, 'storage/');
                    if ($storagePosition === false) {
                        return $matches[0];
                    }
                }

                $storagePath = ltrim(substr($normalizedPath, $storagePosition + strlen('/storage/')), '/');

                if ($storagePath === '') {
                    return $matches[0];
                }

                return '<img' . $matches[1] . 'src="' . route('public-file', ['path' => $storagePath]) . '"' . $matches[3] . '>';
            },
            $html,
        );
    }
}
