<?php

declare(strict_types=1);

namespace karlosagudo\Fixtro\Tests\CodeQualityTool\FilterFiles;

use karlosagudo\Fixtro\CodeQualityTool\FilterFiles\GeneralFilters;
use PHPUnit\Framework\TestCase;

class GeneralFiltersTest extends TestCase
{
	protected $files = [];
	private $randomJsFiles;
	private $randomPhpFiles;
    private $randomPhpFilesInSrc;

    protected function setUp()
    {
        $this->randomPhpFiles = random_int(2, 10);
        $this->randomJsFiles = random_int(2, 10);
        $this->randomPhpFilesInSrc = random_int(2,10);

        $this->generateRandomFiles($this->randomPhpFiles, 'php');
        $this->generateRandomFiles($this->randomJsFiles, 'js');
        $this->generateRandomFiles($this->randomPhpFilesInSrc, 'php', 'src/');
        $this->files[] = 'composer.json';
    }

	public function testGetPhpFiles()
	{
		$filter = new GeneralFilters($this->files);
		$outputFiles = $filter->getPhpFiles();
		$this->assertCount($this->randomPhpFiles + $this->randomPhpFilesInSrc, $outputFiles);
	}

    public function testGetPhpFilesInSrc()
    {
        $filter = new GeneralFilters($this->files);
        $outputFiles = $filter->getPhpFilesInSrc();
        $this->assertCount($this->randomPhpFilesInSrc, $outputFiles);
    }

	public function testGetJsFiles()
	{
		$filter = new GeneralFilters($this->files);
		$outputFiles = $filter->getJsFiles();
		$this->assertCount($this->randomJsFiles, $outputFiles);
	}

	public function testComposerFiles()
	{
		$filter = new GeneralFilters($this->files);
		$outputFiles = $filter->getComposerFiles();
		$this->assertCount(1, $outputFiles);
	}

	public function testNullFiles()
	{
		$filter = new GeneralFilters($this->files);
		$outputFiles = $filter->getNullFiles();
		$this->assertCount(0, $outputFiles);
	}

    private function generateRandomFiles($number, $extension, $path = '')
    {
        for ($i = 0; $i < $number; ++$i) {
            $this->files[] = $this->generateRandomFile($extension, $path);
        }
    }

    private function generateRandomFile($extension, $path = '')
    {
        $letters = range('a', 'z');
        $characters = array_merge(array_pad(['/'], 5, '/'), $letters);
        shuffle($characters);
        $randomFile = '';

        if ($path) {
            $randomFile = $path;
        }

        for ($i = 0, $iMax = random_int(1, count($characters)); $i < $iMax; ++$i) {
            $randomFile .= $characters[$i];
        }

        $randomFile .= '.'.$extension;

        $randomFile = str_replace('//','/', $randomFile);

        return $randomFile;
    }
}
