<?php

declare(strict_types=1);

namespace Framework\Web;

use Framework\Container\MutableContainer;
use Framework\Files\DefaultFileManager;
use Framework\Files\FileManager;
use Framework\LanguageSettings;
use Framework\Requests\RequestHandler;
use Framework\Routes\Router;
use Framework\Views\BranchesReplacer;
use Framework\Views\ContentReplacer;
use Framework\Views\ContentReplacerPipeline;
use Framework\Views\HtmlViewEngine;
use Framework\Views\I18nReplacer;
use Framework\Views\ModelReplacer;
use Framework\Views\ViewEngine;
use Framework\Views\ViewValueResolver;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Dependencies
{
    /**
     * Registers PSR-17, view pipeline, and request handler bindings for the web stack.
     * The composition root must register {@see Router::class} before calling this method.
     */
    public static function configure(MutableContainer $container): void
    {
        if (!($container->get(Router::class) instanceof Router)) {
            throw new \RuntimeException(
                'Router must be registered in the container before Web\\Dependencies::configure().'
            );
        }

        $psr17Factory = new Psr17Factory();
        $container->set(Psr17Factory::class, $psr17Factory);
        $container->set(ResponseFactoryInterface::class, $psr17Factory);
        $container->set(ServerRequestCreator::class, new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
        ));
        $container->set(FileManager::class, $container->get(DefaultFileManager::class));
        $resolver = new ViewValueResolver();
        $languageSettings = $container->get(LanguageSettings::class);
        $fileManager = $container->get(FileManager::class);
        if (!$languageSettings instanceof LanguageSettings || !$fileManager instanceof FileManager) {
            throw new \RuntimeException('LanguageSettings or FileManager not found in container');
        }
        $container->set(
            ContentReplacer::class,
            new ContentReplacerPipeline([
                new ModelReplacer($resolver),
                new BranchesReplacer($resolver),
                new I18nReplacer($languageSettings, $fileManager),
            ])
        );
        $container->set(ViewEngine::class, $container->get(HtmlViewEngine::class));
        $container->set(RequestHandlerInterface::class, $container->get(RequestHandler::class));
    }
}
