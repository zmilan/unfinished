<?php

namespace Web;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'templates' => [
                'map'   => [
                    'layout/web'         => 'templates/layout/web.phtml',
                    'article/pagination' => 'templates/partial/pagination.phtml',
                ],
                'paths' => [
                    'templates' => ['templates'],
                    'web'       => ['templates/web'],
                ],
            ],

            'dependencies' => [
                'factories' => [
                    Action\HomeAction::class     => Factory\Action\HomeActionFactory::class,
                    Action\CategoryAction::class => Factory\Action\CategoryActionFactory::class,
                    Action\PostAction::class     => Factory\Action\PostActionFactory::class,
                    Action\VideosAction::class   => Factory\Action\VideosActionFactory::class,
                    Action\VideoAction::class    => Factory\Action\VideoActionFactory::class,
                    Action\EventsAction::class   => Factory\Action\EventsActionFactory::class,
                    Action\EventAction::class    => Factory\Action\EventActionFactory::class,
                ],
            ],

            'routes' => [
                [
                    'name'       => 'home',
                    'path'       => '/',
                    'middleware' => Action\HomeAction::class
                ],
                [
                    'name'       => 'category',
                    'path'       => '/:category/',
                    'middleware' => Action\CategoryAction::class
                ],
                [
                    'name'       => 'post',
                    'path'       => '/:segment_1[/:segment_2]',
                    'middleware' => Action\PostAction::class
                ],

                // Different article types
                [
                    'name'       => 'videos',
                    'path'       => '/php-videos/',
                    'middleware' => Action\VideosAction::class
                ],
                [
                    'name'       => 'video',
                    'path'       => '/video/:video_slug',
                    'middleware' => Action\VideoAction::class
                ],
                [
                    'name'       => 'events',
                    'path'       => '/events/',
                    'middleware' => Action\EventsAction::class
                ],
                [
                    'name'       => 'event',
                    'path'       => '/event/:event_slug',
                    'middleware' => Action\EventAction::class
                ],
                // \Different article types
            ],
        ];
    }
}
