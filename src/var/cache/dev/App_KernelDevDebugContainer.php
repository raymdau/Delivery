<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container2LBLD1S\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container2LBLD1S/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container2LBLD1S.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container2LBLD1S\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \Container2LBLD1S\App_KernelDevDebugContainer([
    'container.build_hash' => '2LBLD1S',
    'container.build_id' => '24b65e57',
    'container.build_time' => 1590434415,
], __DIR__.\DIRECTORY_SEPARATOR.'Container2LBLD1S');