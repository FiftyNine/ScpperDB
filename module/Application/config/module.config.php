<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'generate-consts' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/generateConsts',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'generateConsts',
                    ),
                ),
            ),            
            'select-site' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/select-site',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'selectSite',
                    )
                )            
            ),
            'changes' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/changes',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Changes',
                        'action' => 'changes',
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Changes',
                            ),
                        ),
                    ),
                ),                
            ),            
            'users' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/users',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Users',
                        'action'     => 'users',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Users',
                            ),
                        ),
                    ),
                ),                                
            ),            
            'pages' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/pages',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Pages',
                        'action'     => 'pages',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Pages',
                            ),
                        ),
                    ),
                ),                                
            ),            
            'revisions' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/revisions',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Revisions',
                        'action'     => 'revisions',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Revisions',
                            ),
                        ),
                    ),
                ),                                
            ),            
            'votes' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/votes',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Votes',
                        'action'     => 'votes',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Votes',
                            ),
                        ),
                    ),
                ),                                
            ),            
            'page' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/page',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'page' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:pageId',
                            'constraints' => array(                                
                                'pageId'     => '[1-9][0-9]*',
                            ),                                                
                            'defaults' => array(
                                'controller' => 'Application\Controller\Page',
                                'action' => 'page'
                            ),
                        ),                        
                    ),
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Page',
                            ),
                        ),
                    ),
                ),                                                
            ),
            'user' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/user',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'user' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:userId',
                            'constraints' => array(                                
                                'pageId'     => '[1-9][0-9]*',
                            ),                                                
                            'defaults' => array(
                                'controller' => 'Application\Controller\User',
                                'action' => 'user'
                            ),
                        ),                        
                    ),
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/:action',
                            'constraints' => array(                                
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\User',
                            ),
                        ),
                    ),
                ),                                                
            ),            
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            // Services
            'Application\Service\SiteServiceInterface' => 'Application\Factory\Service\SiteServiceFactory',
            'Application\Service\UserServiceInterface' => 'Application\Factory\Service\UserServiceFactory',
            'Application\Service\PageServiceInterface' => 'Application\Factory\Service\PageServiceFactory',
            'Application\Service\RevisionServiceInterface' => 'Application\Factory\Service\RevisionServiceFactory',
            'Application\Service\VoteServiceInterface' => 'Application\Factory\Service\VoteServiceFactory',
            'Application\Service\UtilityServiceInterface' => 'Application\Factory\Service\UtilityServiceFactory',
            'Application\Service\HubServiceInterface' => 'Application\Factory\Service\HubServiceFactory',
            'EventLogger' => 'Application\Factory\Service\FileEventLoggerFactory',            
            // Mappers
            'SiteMapper' => 'Application\Factory\Mapper\SiteDbSqlMapperFactory',
            'UserMapper' => 'Application\Factory\Mapper\UserDbSqlMapperFactory',
            'PageMapper' => 'Application\Factory\Mapper\PageDbSqlMapperFactory',
            'RevisionMapper' => 'Application\Factory\Mapper\RevisionDbSqlMapperFactory',
            'VoteMapper' => 'Application\Factory\Mapper\VoteDbSqlMapperFactory',
            'AuthorshipMapper' => 'Application\Factory\Mapper\AuthorshipDbSqlMapperFactory',
            'UserActivityMapper' => 'Application\Factory\Mapper\UserActivityDbSqlMapperFactory',
            'MembershipMapper' => 'Application\Factory\Mapper\MembershipDbSqlMapperFactory',
            // Model object prototypes
            'SitePrototype' => 'Application\Factory\Prototype\SitePrototypeFactory',
            'UserPrototype' => 'Application\Factory\Prototype\UserPrototypeFactory',
            'PagePrototype' => 'Application\Factory\Prototype\PagePrototypeFactory',
            'RevisionPrototype' => 'Application\Factory\Prototype\RevisionPrototypeFactory',
            'VotePrototype' => 'Application\Factory\Prototype\VotePrototypeFactory',
            'AuthorshipPrototype' => 'Application\Factory\Prototype\AuthorshipPrototypeFactory',
            'UserActivityPrototype' => 'Application\Factory\Prototype\UserActivityPrototypeFactory',
            'MembershipPrototype' => 'Application\Factory\Prototype\MembershipPrototypeFactory',
            // Overrides
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',            
            'Zend\Db\Adapter\Adapter' => 'Application\Factory\Service\DbAdapterFactory',
            'navigation' => 'Application\Factory\Service\NavigationFactory', // Zend\Navigation\Service\DefaultNavigationFactory',         
            'LazyServiceFactory' => 'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory',            
        ),
        'delegators' => array(
            'SiteMapper' => array('LazyServiceFactory'),
            'UserMapper' => array('LazyServiceFactory'),
            'PageMapper' => array('LazyServiceFactory'),
            'RevisionMapper' => array('LazyServiceFactory'),
            'VoteMapper' => array('LazyServiceFactory'),            
            'AuthorshipMapper' => array('LazyServiceFactory'),            
            'UserActivityMapper' => array('LazyServiceFactory'),            
            'MembershipMapper' => array('LazyServiceFactory'),            
        ),        
    ),
    'lazy_services' => array(
        'class_map' => array(
            'SiteMapper' => 'Application\Mapper\ZendDbSqlMapper',
            'UserMapper' => 'Application\Mapper\UserDbSqlMapper',
            'PageMapper' => 'Application\Mapper\PageDbSqlMapper',
            'RevisionMapper' => 'Application\Mapper\RevisionDbSqlMapper',
            'VoteMapper' => 'Application\Mapper\VoteDbSqlMapper',
            'AuthorshipMapper' => 'Application\Mapper\AuthorshipDbSqlMapper',
            'UserActivityMapper' => 'Application\Mapper\UserActivityDbSqlMapper',
            'MembershipMapper' => 'Application\Mapper\MembershipDbSqlMapper',
        ),
    ),    
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Application\Controller\Index' => 'Application\Factory\Controller\IndexControllerFactory',
            'Application\Controller\Changes' => 'Application\Factory\Controller\ChangesControllerFactory',
            'Application\Controller\Users' => 'Application\Factory\Controller\UsersControllerFactory',
            'Application\Controller\Pages' => 'Application\Factory\Controller\PagesControllerFactory',
            'Application\Controller\Revisions' => 'Application\Factory\Controller\RevisionsControllerFactory',
            'Application\Controller\Votes' => 'Application\Factory\Controller\VotesControllerFactory',
            'Application\Controller\Page' => 'Application\Factory\Controller\PageControllerFactory',
            'Application\Controller\User' => 'Application\Factory\Controller\UserControllerFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),        
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Wiki',
                'route' => 'home',                
            ),
            array(
                'label' => 'Users',
                'route' => 'users',
            ),
            array(
                'label' => 'Pages',
                'route' => 'pages',
            ),
            array(
                'label' => 'Revisions',
                'route' => 'revisions',
            ),            
            array(
                'label' => 'Votes',
                'route' => 'votes',
            ),            
            array(
                'label' => 'Changes',
                'route' => 'changes',
            ),            
        )
    )
);
