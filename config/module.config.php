<?php

namespace PersonalNotebook;

return [
    'api_adapters' => [
        'invokables' => [
            'personalnotebook_notes' => Api\Adapter\NoteAdapter::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'PersonalNotebook\Controller\Note' => Controller\NoteController::class,
        ],
    ],
    'entity_manager' => [
        'filters' => [
            'personalnotebook_note_visibility' => Db\Filter\NoteVisibilityFilter::class,
        ],
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'router' => [
        'routes' => [
            'personal-notebook' => [
                'type' => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route' => '/personal-notebook',
                    'defaults' => [
                        '__NAMESPACE__' => 'PersonalNotebook\Controller',
                    ],
                ],
                'child_routes' => [
                    'notes' => [
                        'type' => \Laminas\Router\Http\Segment::class,
                        'options' => [
                            'route' => '/notes[/:id]',
                            'defaults' => [
                                'controller' => 'Note',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'personalNotebook' => View\Helper\PersonalNotebook::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
