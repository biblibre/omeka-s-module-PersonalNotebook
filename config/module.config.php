<?php

namespace PersonalNotebook;

return [
    'api_adapters' => [
        'invokables' => [
            'personalnotebook_notes' => Api\Adapter\NoteAdapter::class,
        ],
    ],
    'block_layouts' => [
        'invokables' => [
            'personalNotebook' => Site\BlockLayout\PersonalNotebook::class,
        ],
    ],
    'controllers' => [
        'invokables' => [
            'PersonalNotebook\Controller\Note' => Controller\NoteController::class,
            'PersonalNotebook\Controller\Site\Note' => Controller\Site\NoteController::class,
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
            'site' => [
                'child_routes' => [
                    'personal-notebook' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/personal-notebook',
                            'defaults' => [
                                '__NAMESPACE__' => 'PersonalNotebook\Controller\Site',
                            ],
                        ],
                        'child_routes' => [
                            'notes-csv' => [
                                'type' => \Laminas\Router\Http\Literal::class,
                                'options' => [
                                    'route' => '/notes.csv',
                                    'defaults' => [
                                        'controller' => 'Note',
                                        'action' => 'export-as-csv',
                                    ],
                                ],
                            ],
                            'notes-id' => [
                                'type' => \Laminas\Router\Http\Segment::class,
                                'options' => [
                                    'route' => '/notes/:id[/:action]',
                                    'defaults' => [
                                        'controller' => 'Note',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
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
        'factories' => [
            'personalNotebook' => Service\ViewHelper\PersonalNotebookFactory::class,
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
