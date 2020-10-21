<?php

declare(strict_types=1);

namespace DBrekelmans\BrowserDriverInstaller\Tests\Driver\ChromeDriver;

use DBrekelmans\BrowserDriverInstaller\Driver\ChromeDriver\Downloader;
use DBrekelmans\BrowserDriverInstaller\Driver\Driver;
use DBrekelmans\BrowserDriverInstaller\Driver\DriverName;
use DBrekelmans\BrowserDriverInstaller\OperatingSystem\OperatingSystem;
use DBrekelmans\BrowserDriverInstaller\Version;
use PHPStan\Testing\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use ZipArchive;
use const DIRECTORY_SEPARATOR;
use function sys_get_temp_dir;

class DownloaderTest extends TestCase
{
    private Downloader $downloader;
    private Driver $chromeDriverMac;
    /** @var MockObject&Filesystem */
    private $fsMock;
    /** @var MockObject&ZipArchive */
    private $zipMock;
    /** @var MockObject&HttpClientInterface */
    private $httpClientMock;

    public function setUp() : void
    {
        $this->fsMock = $this->getMockBuilder(Filesystem::class)->disableOriginalConstructor()->getMock();
        $this->httpClientMock = $this->getMockBuilder(HttpClientInterface::class)->getMock();
        $this->zipMock = $this->getMockBuilder(ZipArchive::class)->getMock();
        $this->downloader = new Downloader($this->fsMock, $this->httpClientMock, $this->zipMock);

        $this->chromeDriverMac = new Driver(DriverName::CHROME(), Version::fromString('86.0.4240.22'), OperatingSystem::MACOS());
    }

    public function testSupportChrome() : void
    {
        self::assertTrue($this->downloader->supports($this->chromeDriverMac));
    }

    public function testDoesNotSupportGecko() : void
    {
        $geckoDriver = new Driver(DriverName::GECKO(), Version::fromString('0.27.0'), OperatingSystem::MACOS());
        self::assertFalse($this->downloader->supports($geckoDriver));
    }

    public function testDownloadMac() : void
    {
        $this->mockFsAndZipForSuccessfulDownload();

        $this->httpClientMock
            ->expects(self::atLeastOnce())
            ->method('request')
            ->with('GET', 'https://chromedriver.storage.googleapis.com/86.0.4240.22/chromedriver_mac64.zip');

        $filePath = $this->downloader->download($this->chromeDriverMac, '.');

        self::assertEquals('./chromedriver', $filePath);
    }

    public function testDownloadLinux() : void
    {
        $this->mockFsAndZipForSuccessfulDownload();

        $this->httpClientMock
            ->expects(self::atLeastOnce())
            ->method('request')
            ->with('GET', 'https://chromedriver.storage.googleapis.com/86.0.4240.22/chromedriver_linux64.zip');

        $chromeDriverLinux = new Driver(DriverName::CHROME(), Version::fromString('86.0.4240.22'), OperatingSystem::LINUX());
        $filePath = $this->downloader->download($chromeDriverLinux, '.');

        self::assertEquals('./chromedriver', $filePath);
    }

    public function testDownloadWindows() : void
    {
        $this->mockFsAndZipForSuccessfulDownload();

        $this->httpClientMock
            ->expects(self::atLeastOnce())
            ->method('request')
            ->with('GET', 'https://chromedriver.storage.googleapis.com/86.0.4240.22/chromedriver_win32.zip');

        $chromeDriverLinux = new Driver(DriverName::CHROME(), Version::fromString('86.0.4240.22'), OperatingSystem::WINDOWS());
        $filePath = $this->downloader->download($chromeDriverLinux, '.');

        self::assertEquals('./chromedriver.exe', $filePath);
    }

    private function mockFsAndZipForSuccessfulDownload() : void
    {
        $this->fsMock
            ->expects(self::any())
            ->method('tempnam')
            ->willReturn(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'chromedriver-XXX.zip');
        $this->fsMock
            ->expects(self::any())
            ->method('readLink')
            ->willReturn('YYY');

        $this->zipMock
            ->expects(self::any())
            ->method('open')
            ->willReturn(true);
        $this->zipMock
            ->expects(self::any())
            ->method('count')
            ->willReturn(1);
        $this->zipMock
            ->expects(self::any())
            ->method('extractTo')
            ->willReturn(true);
        $this->zipMock
            ->expects(self::any())
            ->method('close')
            ->willReturn(true);
    }
}