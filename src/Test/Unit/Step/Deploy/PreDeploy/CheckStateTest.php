<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MagentoCloud\Test\Unit\Step\Deploy\PreDeploy;

use Magento\MagentoCloud\Config\Magento\Env\ReaderInterface as ConfigReader;
use Magento\MagentoCloud\Filesystem\Flag\Manager as FlagManager;
use Magento\MagentoCloud\Step\Deploy\PreDeploy\CheckState;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @inheritdoc
 */
class CheckStateTest extends TestCase
{
    /**
     * @var ConfigReader|MockObject
     */
    private $configReaderMock;

    /**
     * @var FlagManager|MockObject
     */
    private $flagManagerMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var CheckState
     */
    private $checkState;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->configReaderMock = $this->createMock(ConfigReader::class);
        $this->flagManagerMock = $this->createMock(FlagManager::class);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);

        $this->checkState = new CheckState(
            $this->configReaderMock,
            $this->flagManagerMock,
            $this->loggerMock
        );
    }

    /**
     * @param $config array
     * @throws \Magento\MagentoCloud\Step\StepException
     *
     * @dataProvider executeWithEmptyFileDataProvider
     */
    public function testExecuteWithEmptyFile($config)
    {
        $this->configReaderMock->expects($this->once())
            ->method('read')
            ->willReturn($config);

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(sprintf('Set "%s" flag', FlagManager::FLAG_ENV_FILE_ABSENCE));
        $this->flagManagerMock->expects($this->once())
            ->method('set')
            ->with(FlagManager::FLAG_ENV_FILE_ABSENCE);

        $this->checkState->execute();
    }

    /**
     * Data provider for testExecuteWithEmptyFile test
     * @return array
     */
    public function executeWithEmptyFileDataProvider()
    {
        return [
            [[]],
            [['cache_type' => '', ]],
            [['cache_type' => ['type_1' => 1], ]],
        ];
    }

    /**
     * @param $config
     * @throws \Magento\MagentoCloud\Step\StepException
     *
     * @dataProvider executeWithFullOfDataFileDataProvider
     */
    public function testExecuteWithFullOfDataFile($config)
    {
        $this->configReaderMock->expects($this->once())
            ->method('read')
            ->willReturn($config);

        $this->loggerMock->expects($this->never())
            ->method('info');
        $this->flagManagerMock->expects($this->never())
            ->method('set');

        $this->checkState->execute();
    }

    public function executeWithFullOfDataFileDataProvider()
    {
        return [
            [
                ['cache_type' => '', 'other_data' => []]
            ],
            [
                ['other_data' => []]
            ],
        ];
    }
}