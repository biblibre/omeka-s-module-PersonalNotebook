<?php

namespace PersonalNotebook;

return [
    'api_adapters' => [
        'invokables' => [
            'personalnotebook_notes' => Api\Adapter\NoteAdapter::class,
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
];
