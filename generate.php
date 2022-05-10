<?php

$packages = [];

foreach (glob('runtime/**/*.zip') as $filepath) {
    $packages[] = index($filepath);
}

file_put_contents('manifest.json', json_encode($packages, JSON_PRETTY_PRINT));


function index($filepath)
{
    $host = 'https://raw.githubusercontent.com/alexzvn/open-runtime/main';

    [, $component, $file] = explode('/', $filepath);
    [$platformArch] = explode('.', $file);
    $platformArch = explode('-', $platformArch);

    $platform = $platformArch[0];
    $arch = $platformArch[1] ?? null;

    $platformArch = implode('-', $platformArch);

    $entry = match($platform) {
        'darwin' => join('/', [$component, $platformArch, $component, 'jre.bundle/Contents/Home/bin', 'java']),
        default =>  join('/', [$component, $platformArch, $component, 'bin', 'java'])
    };

    $version = match($component) {
        'jre-legacy' => 8,
        'java-runtime-alpha' => 16,
        'java-runtime-beta' => 17,
        'java-runtime-gamma' => 17,
    };

    return [
        'component' => $component,
        'version' => $version,
        'platform' => $platform,
        'arch' => $arch ?: null,
        'entry' => $entry,
        'hash' => sha1_file($filepath),
        'url' => "$host/$filepath",
        'size' => filesize($filepath),
    ];
}
