<?php

namespace Interna\Core\Tests\Unit\CommandBus;

use Interna\Core\CommandBus\CommandBus;
use Interna\Core\CommandBus\Exception\ClassCannotBeConstructedException;
use Interna\Core\CommandBus\Exception\CommandHandlerNotExistException;
use Interna\Core\CommandBus\Exception\HandlerMustHaveHandleMethodException;
use Interna\Core\CommandBus\Locator\Handler\ConfigLocator;
use Interna\Core\CommandBus\Locator\Handler\InMemoryLocator;
use Interna\Core\Tests\Unit\CommandBus\Helpers\Command;
use Interna\Core\Tests\Unit\CommandBus\Helpers\CommandHandler;
use Interna\Core\Tests\Unit\CommandBus\Helpers\CommandHandlerMissing;
use Interna\Core\Tests\Unit\CommandBus\Helpers\CommandHandlerWithDep;
use Interna\Core\Tests\Unit\CommandBus\Helpers\Dependency;
use Interna\Core\Tests\Unit\CommandBus\Helpers\DependencyCannotBeConstructed;
use Interna\Core\UnitTestCase;
use Phalcon\Config;

final class ConfigCommandBusTest extends UnitTestCase
{
    public const INT = 1;
    public const STRING = 'abc';
    public const DATE = '01.01.2000';

    /**
     * @throws \Exception
     */
    public function testSuccessFlowWithoutHandlerSpecified(): void
    {
        $config = new Config(
            [
                'bus' =>
                [
                    'test_success' =>
                    [
                        '@attributes' =>
                            [
                                'command' => Command::class
                            ]
                    ]
                ]
            ]
        );

        $locator = new ConfigLocator($config->get('bus'));
        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testSuccessFlowWithHandlerSpecified(): void
    {
        $config = new Config(
            [
                'bus' =>
                    [
                        'test_success' =>
                            [
                                '@attributes' =>
                                    [
                                        'command' => Command::class,
                                        'handler' => CommandHandler::class,
                                    ]
                            ]
                    ]
            ]
        );

        $locator = new ConfigLocator($config->get('bus'));
        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testSuccessFlowWithDependencies(): void
    {
        $config = new Config(
            [
                'bus' =>
                    [
                        'test_success' =>
                            [
                                '@attributes' =>
                                    [
                                        'command' => Command::class,
                                        'handler' => CommandHandlerWithDep::class,
                                    ],
                                'dependencies' =>
                                [
                                    'dep_class' => Dependency::class,
                                    'dep_object'=>
                                    [
                                        'type' => 'object',
                                        'value' => (object)['test'=>123],
                                    ],
                                    'dep_int'=>
                                        [
                                            'type' => 'int',
                                            'value' => 123,
                                        ],
                                    'dep_float'=>
                                        [
                                            'type' => 'float',
                                            'value' => 123.45,
                                        ],
                                    'dep_string'=>
                                        [
                                            'type' => 'string',
                                            'value' => 'abc',
                                        ],
                                    'dep_array'=>
                                        [
                                            'type' => 'array',
                                            'items' =>
                                            [
                                                'abc' =>
                                                [
                                                    'type' => 'string',
                                                    'value'=> 'abc'
                                                ],
                                                'def' =>
                                                [
                                                    'type' => 'int',
                                                    'value'=> 123
                                                ],
                                            ]
                                        ],
                                    'dep_null' =>
                                    [
                                        'type' => 'abc',
                                        'value'=> '123'
                                    ]
                                ]
                            ]
                    ]
            ]
        );

        $locator = new ConfigLocator($config->get('bus'));
        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testClassCannotBeConstructedException(): void
    {
        $this->expectException(ClassCannotBeConstructedException::class);
        $config = new Config(
            [
                'bus' =>
                    [
                        'test_success' =>
                            [
                                '@attributes' =>
                                    [
                                        'command' => Command::class,
                                        'handler' => CommandHandlerWithDep::class,
                                    ],
                                'dependencies' =>
                                    [
                                        'dep_class' => Dependency::class,
                                        'dep_object'=>
                                            [
                                                'type' => 'object',
                                                'value' => (object)['test'=>123],
                                            ],
                                        'dep_int'=>
                                            [
                                                'type' => 'int',
                                                'value' => 123,
                                            ],
                                        'dep_float'=>
                                            [
                                                'type' => 'float',
                                                'value' => 123.45,
                                            ],
                                        'dep_string'=>
                                            [
                                                'type' => 'string',
                                                'value' => 'abc',
                                            ],
                                        'dep_array'=>
                                            [
                                                'type' => 'array',
                                                'items' =>
                                                    [
                                                        'abc' =>
                                                            [
                                                                'type' => 'string',
                                                                'value'=> 'abc'
                                                            ],
                                                        'def' =>
                                                            [
                                                                'type' => 'int',
                                                                'value'=> [] // <- Invalid
                                                            ],
                                                    ]
                                            ],
                                        'dep_null' =>
                                            [
                                                'type' => 'abc',
                                                'value'=> '123'
                                            ]
                                    ]
                            ]
                    ]
            ]
        );

        $locator = new ConfigLocator($config->get('bus'));
        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testCommandHandlerMissing(): void
    {
        $this->expectException(HandlerMustHaveHandleMethodException::class);
        $locator = new InMemoryLocator(
            [
                Command::class => new CommandHandlerMissing(),
            ]
        );

        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }

    /**
     * @throws \Exception
     */
    public function testCommandMissing(): void
    {
        $this->expectException(CommandHandlerNotExistException::class);
        $config = new Config(
            [
                'bus' =>
                    [
                        'test_success' =>
                            [
                                '@attributes' =>
                                    [
                                        'command' => 'SomeNotExistingCommand',
                                        'handler' => CommandHandler::class,
                                    ]
                            ]
                    ]
            ]
        );

        $locator = new ConfigLocator($config->get('bus'));
        $commandBus = new CommandBus($locator);
        $commandBus->handle(new Command(
            self::INT,
            self::STRING,
            new \DateTimeImmutable(strftime(self::DATE))
        ));
    }
}