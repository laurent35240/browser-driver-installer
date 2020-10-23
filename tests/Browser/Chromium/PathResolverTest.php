<?php

declare(strict_types=1);

namespace DBrekelmans\BrowserDriverInstaller\Tests\Browser\Chromium;

use DBrekelmans\BrowserDriverInstaller\Browser\BrowserName;
use DBrekelmans\BrowserDriverInstaller\Browser\Chromium\PathResolver;
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
        self::assertEquals('chromium', $this->pathResolver->from(OperatingSystem::LINUX()));
        self::assertEquals('/Applications/Chromium.app', $this->pathResolver->from(OperatingSystem::MACOS()));
        self::assertEquals(
            'C:\Program Files (x86)\Chromium\Application\chrome.exe',
            $this->pathResolver->from(OperatingSystem::WINDOWS())
        );
    }

    public function testSupportChromium() : void
    {
        self::assertTrue($this->pathResolver->supports(BrowserName::CHROMIUM()));
    }

    public function testDoesNotSupportFirefox() : void
    {
        self::assertFalse($this->pathResolver->supports(BrowserName::FIREFOX()));
    }
}
