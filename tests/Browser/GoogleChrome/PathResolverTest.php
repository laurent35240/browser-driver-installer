<?php

declare(strict_types=1);

namespace DBrekelmans\BrowserDriverInstaller\Tests\Browser\GoogleChrome;

use DBrekelmans\BrowserDriverInstaller\Browser\BrowserName;
use DBrekelmans\BrowserDriverInstaller\Browser\GoogleChrome\PathResolver;
use DBrekelmans\BrowserDriverInstaller\OperatingSystem\OperatingSystem;
use PHPUnit\Framework\TestCase;

final class PathResolverTest extends TestCase
{
    private PathResolver $pathResolver;

    protected function setUp() : void
    {
        $this->pathResolver = new PathResolver();
    }

    public function testFromKnownOs() : void
    {
        self::assertEquals('google-chrome', $this->pathResolver->from(OperatingSystem::LINUX()));
        self::assertEquals('/Applications/Google\ Chrome.app', $this->pathResolver->from(OperatingSystem::MACOS()));
        self::assertEquals('C:\Program Files (x86)\Google\Chrome\Application\chrome.exe', $this->pathResolver->from(OperatingSystem::WINDOWS()));
    }

    public function testSupportChrome() : void
    {
        self::assertTrue($this->pathResolver->supports(BrowserName::GOOGLE_CHROME()));
    }

    public function testDoesNotSupportFirefox() : void
    {
        self::assertFalse($this->pathResolver->supports(BrowserName::FIREFOX()));
    }
}