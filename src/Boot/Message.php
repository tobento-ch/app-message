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
 
namespace Tobento\App\Message\Boot;

use Tobento\App\Boot;
use Tobento\Service\Message\MessagesFactoryInterface;
use Tobento\Service\Message\MessagesFactory;
use Tobento\Service\Message\Modifiers;
use Tobento\Service\Message\Modifier;
use Tobento\Service\Translation\TranslatorInterface;
use Tobento\App\Logging\LoggersInterface;
use Psr\Container\ContainerInterface;

/**
 * Message
 */
class Message extends Boot
{
    public const INFO = [
        'boot' => [
            'configures the messages factory',
        ],
    ];
    
    public function boot(): void
    {
        $this->app->set(
            MessagesFactoryInterface::class,
            static function(ContainerInterface $container): MessagesFactoryInterface {
                $modifiers = new Modifiers();
                
                if ($container->has(TranslatorInterface::class)) {
                    $modifiers->add(new Modifier\Translator(
                        translator: $container->get(TranslatorInterface::class),
                        src: '*',
                    ));
                }
                
                $modifiers->add(new Modifier\Pluralization());
                $modifiers->add(new Modifier\ParameterReplacer());
                
                $logger = null;
                
                if ($container->has(LoggersInterface::class)) {
                    $logger = $container->get(LoggersInterface::class)->get(name: 'messages');
                }
                
                return new MessagesFactory(modifiers: $modifiers, logger: $logger);
            }
        );
    }
}