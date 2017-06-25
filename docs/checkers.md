# List of Checkers

### CodeStyleFixer
Will run [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
ConfigurationFile: .php_cs
Filter: Php files

### ComposerChecker
Will check if you are trying to submit a composer.json without a composer.lock (that can break a lot of things)
ConfigurationFile: ~
Filter: composer.json and composer.lock

### EsLintChecker
Will run [esLint](http://eslint.org/)
ConfigurationFile: .eslintrc
Filter: Js files

### NameSpaceFixer
Will order your namespaces in php using [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
ConfigurationFile: ~
Filter: Php files

### PhpLintChecker
Will run [php-parallel-lint](https://github.com/JakubOnderka/PHP-Parallel-Lint)
Its just a php Linter, to avoid submitting broke code
ConfigurationFile: ~
Filter: Php files

### PhpMessDetectorChecker
Will run [phpmd](https://phpmd.org/)
This tool is for detect bad code, with high complexity, and bad namings, etc..
ConfigurationFile: phpmd.xml
Filter: Php files

### PhpStanChecker
Will run [phpstan](https://github.com/phpstan/phpstan)
This tool its for running a static analysis to prevent possible bugs
ConfigurationFile: phpstan.neon
Filter: Php files

### PhpUnitChecker
Will run [phpunit](https://phpunit.de)
Will do a battery of php unit tests
ConfigurationFile: phpunit.xml
Filter: ~

### PsAlmChecker
Will run [psalm](https://github.com/vimeo/psalm)
A static analysis tool for finding errors in Php applications
ConfigurationFile: psalm.xml
Filter: Php files

### StrictDeclareFixer
Will put the declaration: declare(strict_types=1);
Use internally [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
ConfigurationFile: ~
Filter: Php files
