<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit60ebfeed9176bb1bcf595b4be895ab85
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
            'Symfony\\Component\\Filesystem\\' => 29,
            'Symfony\\Component\\DependencyInjection\\' => 38,
            'Symfony\\Component\\Config\\' => 25,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
            'PhantomInstaller\\Test\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Symfony\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/filesystem',
        ),
        'Symfony\\Component\\DependencyInjection\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/dependency-injection',
        ),
        'Symfony\\Component\\Config\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/config',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'PhantomInstaller\\Test\\' => 
        array (
            0 => __DIR__ . '/..' . '/jakoch/phantomjs-installer/tests',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
        'P' => 
        array (
            'PhantomInstaller\\' => 
            array (
                0 => __DIR__ . '/..' . '/jakoch/phantomjs-installer/src',
            ),
        ),
        'J' => 
        array (
            'JonnyW\\PhantomJs\\' => 
            array (
                0 => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'JonnyW\\PhantomJs\\Cache\\CacheInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Cache/CacheInterface.php',
        'JonnyW\\PhantomJs\\Cache\\FileCache' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Cache/FileCache.php',
        'JonnyW\\PhantomJs\\Client' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Client.php',
        'JonnyW\\PhantomJs\\ClientInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/ClientInterface.php',
        'JonnyW\\PhantomJs\\DependencyInjection\\ServiceContainer' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/DependencyInjection/ServiceContainer.php',
        'JonnyW\\PhantomJs\\Engine' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Engine.php',
        'JonnyW\\PhantomJs\\Exception\\InvalidExecutableException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/InvalidExecutableException.php',
        'JonnyW\\PhantomJs\\Exception\\InvalidMethodException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/InvalidMethodException.php',
        'JonnyW\\PhantomJs\\Exception\\InvalidUrlException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/InvalidUrlException.php',
        'JonnyW\\PhantomJs\\Exception\\NotExistsException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/NotExistsException.php',
        'JonnyW\\PhantomJs\\Exception\\NotWritableException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/NotWritableException.php',
        'JonnyW\\PhantomJs\\Exception\\PhantomJsException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/PhantomJsException.php',
        'JonnyW\\PhantomJs\\Exception\\ProcedureFailedException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/ProcedureFailedException.php',
        'JonnyW\\PhantomJs\\Exception\\RequirementException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/RequirementException.php',
        'JonnyW\\PhantomJs\\Exception\\SyntaxException' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Exception/SyntaxException.php',
        'JonnyW\\PhantomJs\\Http\\AbstractRequest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/AbstractRequest.php',
        'JonnyW\\PhantomJs\\Http\\CaptureRequest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/CaptureRequest.php',
        'JonnyW\\PhantomJs\\Http\\CaptureRequestInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/CaptureRequestInterface.php',
        'JonnyW\\PhantomJs\\Http\\MessageFactory' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/MessageFactory.php',
        'JonnyW\\PhantomJs\\Http\\MessageFactoryInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/MessageFactoryInterface.php',
        'JonnyW\\PhantomJs\\Http\\PdfRequest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/PdfRequest.php',
        'JonnyW\\PhantomJs\\Http\\PdfRequestInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/PdfRequestInterface.php',
        'JonnyW\\PhantomJs\\Http\\Request' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/Request.php',
        'JonnyW\\PhantomJs\\Http\\RequestInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/RequestInterface.php',
        'JonnyW\\PhantomJs\\Http\\Response' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/Response.php',
        'JonnyW\\PhantomJs\\Http\\ResponseInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Http/ResponseInterface.php',
        'JonnyW\\PhantomJs\\Parser\\JsonParser' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Parser/JsonParser.php',
        'JonnyW\\PhantomJs\\Parser\\ParserInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Parser/ParserInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\ChainProcedureLoader' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ChainProcedureLoader.php',
        'JonnyW\\PhantomJs\\Procedure\\Input' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/Input.php',
        'JonnyW\\PhantomJs\\Procedure\\InputInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/InputInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\Output' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/Output.php',
        'JonnyW\\PhantomJs\\Procedure\\OutputInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/OutputInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\Procedure' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/Procedure.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureCompiler' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureCompiler.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureCompilerInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureCompilerInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureFactory' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureFactory.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureFactoryInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureFactoryInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureLoader' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureLoader.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureLoaderFactory' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureLoaderFactory.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureLoaderFactoryInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureLoaderFactoryInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureLoaderInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureLoaderInterface.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureValidator' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureValidator.php',
        'JonnyW\\PhantomJs\\Procedure\\ProcedureValidatorInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Procedure/ProcedureValidatorInterface.php',
        'JonnyW\\PhantomJs\\StringUtils' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/StringUtils.php',
        'JonnyW\\PhantomJs\\Template\\TemplateRenderer' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Template/TemplateRenderer.php',
        'JonnyW\\PhantomJs\\Template\\TemplateRendererInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Template/TemplateRendererInterface.php',
        'JonnyW\\PhantomJs\\Test\\TestCase' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Test/TestCase.php',
        'JonnyW\\PhantomJs\\Tests\\Integration\\ClientTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Integration/ClientTest.php',
        'JonnyW\\PhantomJs\\Tests\\Integration\\Procedure\\ProcedureCompilerTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Integration/Procedure/ProcedureCompilerTest.php',
        'JonnyW\\PhantomJs\\Tests\\Integration\\Procedure\\ProcedureValidatorTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Integration/Procedure/ProcedureValidatorTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Cache\\FileCacheTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Cache/FileCacheTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\ClientTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/ClientTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\EngineTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/EngineTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Http\\CaptureRequestTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Http/CaptureRequestTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Http\\MessageFactoryTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Http/MessageFactoryTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Http\\PdfRequestTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Http/PdfRequestTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Http\\RequestTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Http/RequestTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Http\\ResponseTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Http/ResponseTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Parser\\JsonParserTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Parser/JsonParserTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\ChainProcedureLoaderTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/ChainProcedureLoaderTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\InputTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/InputTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\OutputTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/OutputTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\ProcedureFactoryTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/ProcedureFactoryTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\ProcedureLoaderFactoryTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/ProcedureLoaderFactoryTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\ProcedureLoaderTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/ProcedureLoaderTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Procedure\\ProcedureTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Procedure/ProcedureTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\StringUtilsTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/StringUtilsTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Template\\TemplateRendererTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Template/TemplateRendererTest.php',
        'JonnyW\\PhantomJs\\Tests\\Unit\\Validator\\EsprimaTest' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Tests/Unit/Validator/EsprimaTest.php',
        'JonnyW\\PhantomJs\\Validator\\EngineInterface' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Validator/EngineInterface.php',
        'JonnyW\\PhantomJs\\Validator\\Esprima' => __DIR__ . '/..' . '/jonnyw/php-phantomjs/src/JonnyW/PhantomJs/Validator/Esprima.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit60ebfeed9176bb1bcf595b4be895ab85::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit60ebfeed9176bb1bcf595b4be895ab85::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit60ebfeed9176bb1bcf595b4be895ab85::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit60ebfeed9176bb1bcf595b4be895ab85::$classMap;

        }, null, ClassLoader::class);
    }
}
