<?php

declare(strict_types=1);

namespace Framework\Web;

use Framework\Container\MutableContainer;
use Framework\Module\Files\DefaultFileManager;
use Framework\Module\Files\FileManager;
use Framework\Web\AppFilesystemPath;
use Framework\Web\LanguageSettings;
use Framework\Web\UiAssetsSettings;
use Framework\Web\Requests\RequestHandler;
use Framework\Web\Routes\Router;
use Framework\Web\Views\BranchesReplacer;
use Framework\Web\Views\ContentReplacer;
use Framework\Web\Views\ContentReplacerPipeline;
use Framework\Web\Views\HtmlViewEngine;
use Framework\Web\Views\I18nReplacer;
use Framework\Web\Views\ModelReplacer;
use Framework\Web\Views\ViewEngine;
use Framework\Web\Views\ViewValueResolver;
use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Dependencies
{
    /**
     * Registers PSR-17, view pipeline, and request handler bindings for the web stack.
     * The composition root must register {@see Router::class} before calling this method.
     *
     * @param string $basePath Same application root as {@see \Framework\Web\WebApplication} and
     *     {@see \Framework\Web\MvcWebApp} (e.g. port `__DIR__`). Templates load from `{basePath}/Views/`.
     */
    public static function configure(MutableContainer $container, string $basePath): void
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
        $contentReplacer = new ContentReplacerPipeline([
            new ModelReplacer($resolver),
            new BranchesReplacer($resolver),
            new I18nReplacer($languageSettings, $fileManager),
        ]);
        $container->set(ContentReplacer::class, $contentReplacer);

        $viewsRoot = AppFilesystemPath::join($basePath, 'Views/');
        $uiAssetsSettings = self::optionalUiAssetsSettings($container);

        $htmlViewEngine = new HtmlViewEngine(
            viewsRoot: $viewsRoot,
            contentReplacer: $contentReplacer,
            uiAssetsSettings: $uiAssetsSettings,
        );
        $container->set(HtmlViewEngine::class, $htmlViewEngine);
        $container->set(ViewEngine::class, $htmlViewEngine);
        $container->set(RequestHandlerInterface::class, $container->get(RequestHandler::class));
    }

    private static function optionalUiAssetsSettings(MutableContainer $container): ?UiAssetsSettings
    {
        try {
            $resolved = $container->get(UiAssetsSettings::class);
        } catch (\Throwable) {
            return null;
        }

        if (!$resolved instanceof UiAssetsSettings) {
            throw new \RuntimeException('UiAssetsSettings binding is invalid');
        }

        return $resolved;
    }
}
