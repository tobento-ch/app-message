<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\App\Message\Test\Boot;

use PHPUnit\Framework\TestCase;
use Tobento\App\Message\Boot\Message;
use Tobento\Service\Message\MessagesFactoryInterface;
use Tobento\Service\Translation\TranslatorInterface;
use Tobento\Service\Translation\Resource;
use Tobento\App\Logging\LoggersInterface;
use Monolog\Logger;
use Monolog\Handler\TestHandler;
use Tobento\App\AppInterface;
use Tobento\App\AppFactory;
use Tobento\App\Boot;
use Tobento\Service\Filesystem\Dir;
use Tobento\Service\Filesystem\File;
use Psr\Log\LogLevel;

class ConsoleTest extends TestCase
{    
    protected function createApp(): AppInterface
    {
        (new Dir())->create(__DIR__.'/../../app/');
        
        $app = (new AppFactory())->createApp();
        
        $app->dirs()
            ->dir(realpath(__DIR__.'/../../'), 'root')
            ->dir($app->dir('root').'app', 'app')
            ->dir($app->dir('app').'config', 'config', group: 'config')
            ->dir($app->dir('root').'vendor', 'vendor')
            // for testing only we add public within app dir.
            ->dir($app->dir('app').'public', 'public');
        
        return $app;
    }
    
    public static function tearDownAfterClass(): void
    {
        (new Dir())->delete(__DIR__.'/../../app/');
        (new File(__DIR__.'/../../ap'))->delete();
    }
    
    public function testInterfacesAreAvailable()
    {
        $app = $this->createApp();
        $app->boot(Message::class);
        $app->booting();
        
        $this->assertInstanceof(MessagesFactoryInterface::class, $app->get(MessagesFactoryInterface::class));
    }
    
    public function testDefaultMessageModifiersAreSet()
    {
        $app = $this->createApp();
        $app->boot(Message::class);
        $app->booting();
        
        $messagesFactory = $app->get(MessagesFactoryInterface::class);
        $messages = $messagesFactory->createMessages();
        
        $modifiers = [];
        
        foreach($messages->modifiers()->all() as $modifier) {
            $modifiers[] = $modifier::class;
        }
        
        $this->assertSame(
            [
                \Tobento\Service\Message\Modifier\Pluralization::class,
                \Tobento\Service\Message\Modifier\ParameterReplacer::class,
            ],
            $modifiers
        );
    }
    
    public function testTranslatingMessages()
    {
        $app = $this->createApp();
        $app->boot(\Tobento\App\Translation\Boot\Translation::class);
        $app->boot(Message::class);
        
        $app->on(TranslatorInterface::class, function (TranslatorInterface $translator) {
            $translator->setLocale('de');
            
            $translator->resources()->add(new Resource('*', 'de', [
                'Hello World' => 'Hallo Welt',
            ]));
        });
        
        $app->booting();
        
        $messagesFactory = $app->get(MessagesFactoryInterface::class);
        $messages = $messagesFactory->createMessages();
        $messages->add(level: 'notice', message: 'Hello World');
        
        $this->assertSame('Hallo Welt', $messages->first()->message());
    }
    
    public function testLoggingMessages()
    {
        $app = $this->createApp();
        $app->boot(\Tobento\App\Logging\Boot\Logging::class);
        $app->boot(Message::class);
        
        $logger = new Logger('foo');
        $testHandler = new TestHandler();
        $logger->pushHandler($testHandler);
        
        $app->on(LoggersInterface::class, function (LoggersInterface $loggers) use ($logger) {
            $loggers->addAlias(alias: 'messages', logger: 'foo');
            $loggers->add(name: 'foo', logger: $logger);
        });
        
        $app->booting();
        
        $messagesFactory = $app->get(MessagesFactoryInterface::class);
        $messages = $messagesFactory->createMessages();
        $messages->add(level: 'info', message: 'Hello World');
        
        $this->assertTrue($testHandler->hasRecordThatContains('Hello World', LogLevel::INFO));
    }    
}