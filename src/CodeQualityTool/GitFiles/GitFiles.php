<?php

declare(strict_types=1);

namespace KarlosAgudo\Fixtro\CodeQualityTool\GitFiles;

/**
 * Class GitFiles.
 */
class GitFiles
{
	/** @var array */
	private $ignoreFolders = [];

	/** @var array */
	private $sourceFolders = [];

	/**
	 * GitFiles constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->ignoreFolders = $this->extractFolders($config['ignoreFolders']);
		$this->sourceFolders = $this->extractFolders($config['sourceFolders']);
	}

	/**
	 * @return array
	 */
	public function getPreCommitFiles(): array
	{
		//get new files and changes
		exec("git status -uall --porcelain | egrep \"^(\?\?| M|AM|M |A |MM)\" | awk '{print $2;}'", $output);
		//get moved files
		exec("git status -uall --porcelain | egrep \"^(RM|C )\" | awk '{print $4;}'", $movedOutput);

		$precommitFiles = array_merge($output, $movedOutput);

		return $this->removeIgnored($precommitFiles);
	}

	public function getMergedFiles(): array
	{
		exec('git diff-tree -r --name-only --no-commit-id ORIG_HEAD HEAD', $output);

		return $output;
	}

	public function stashFilesBeforePrecommit()
	{
		exec('git stash -q --keep-index');
	}

	public function stageUpdatedFiles()
	{
		exec('git add -u');
	}

	public function unstashFilesAfterPrecommmit()
	{
		exec('git stash pop -q');
	}

	/**
	 * @param string $branch
	 *
	 * @return array
	 */
	public function getBranchDiffFiles(string $branch): array
	{
		$files = [];
		exec("git branch | grep \* |  awk '{print $2}'", $currentBranch);
		exec('git --no-pager diff --name-only '.$currentBranch[0]."..$branch | awk '{print $1;}'", $files);

		return $this->removeIgnored($files);
	}

	/**
	 * @param $precommitFiles
	 *
	 * @return array
	 */
	private function removeIgnored(array $precommitFiles): array
	{
		return array_filter($precommitFiles, function (string $elem) {
			foreach ($this->ignoreFolders as $ignoreFolder) {
				if (preg_match($ignoreFolder, $elem)) {
					return false;
				}
			}

			foreach ($this->sourceFolders as $sourceFolder) {
				if (preg_match($sourceFolder, $elem)) {
					return true;
				}
			}

			return false;
		});
	}

	/**
	 * @param array $folders
	 *
	 * @return array
	 */
	private function extractFolders(array $folders): array
	{
		$returned = [];
		foreach ($folders as $folder) {
			if ('./' === $folder || '' === $folder) {
				$returned[] = '/.*/';
				continue;
			}
			$folder = trim($folder, '/');
			$prepareRegExp = str_replace('/', "\/", $folder);
			$prepareRegExp = str_replace('*', '(.*)', $prepareRegExp);

			$returned[] = '/^'.$prepareRegExp.'(\/)+/';
		}

		return $returned;
	}
}
