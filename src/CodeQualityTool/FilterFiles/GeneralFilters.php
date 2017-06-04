<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\FilterFiles;

class GeneralFilters
{
	const PHP_FILES = '/^(.*)(\.php)$/';
	const PHP_FILES_IN_SRC = '/^src\/(.*)(\.php)$/';
	const JS_FILES = '/^(.*)(\.js)$/';
	const COMPOSER_FILES = '/^(.*)composer(.*)/';

	/**
	 * @var array
	 */
	private $files;

	/**
	 * GeneralFilters constructor.
	 *
	 * @param array $files
	 */
	public function __construct(array $files)
	{
		$this->files = $files;
	}

	public function getPhpFiles()
	{
		return $this->matchFilesAgainst(self::PHP_FILES);
	}

	public function getPhpFilesInSrc()
	{
		return $this->matchFilesAgainst(self::PHP_FILES_IN_SRC);
	}

	public function getJsFiles()
	{
		return $this->matchFilesAgainst(self::JS_FILES);
	}

	public function getComposerFiles()
	{
		return $this->matchFilesAgainst(self::COMPOSER_FILES);
	}

	public function getNullFiles()
	{
		return [];
	}

	/**
	 * @return array
	 */
	private function matchFilesAgainst($regExp)
	{
		$return = [];
		foreach ($this->files as $gitFile) {
			if (!preg_match($regExp, $gitFile)) {
				continue;
			}
			$return[] = realpath($gitFile);
		}

		return $return;
	}
}
