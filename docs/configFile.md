# Config

Fixtro will look for a fixtro.yml file in your root project,
or in your {rootFolder}/build. If doesnt find any fixtro.yml
will take fixtro default one.

This its the default behaviour, so its pretty easy to override.
Also , when you run the command:

    {binFolder}/fixtro install
    
Will autogenerate a default fixtro.yml in your root Folder or in build folder in case it exists.

Pretty much the same happens with each checker, will try to look for each checker config file 
(Ex:phpmd.xml ) in the same folders (build, and later the root one)

You can check which one is using fixtro by running the command in verbose mode.

The default fixtro yml file is:

    ignoreFolders: ['var','vendor', 'tests', 'test']
    sourceFolders: ['./']
    composerChecker:
      enable: true
    codeStyleFixer:
      enable: true
    nameSpaceFixer:
      enable: true
    phpLintChecker:
      enable: true
    phpMessDetectorChecker:
      enable: true
    phpUnitChecker:
      enable: true
    psAlmChecker:
      enable: true
    strictDeclareFixer:
      enable: true
    esLintChecker:
      enable: true
    phpStanChecker:
      enable: true
    badMessage: ~
    goodMessage: ~
    
In Ignore folders, this folders will not be used for checkers (except obviosly for phpunit).
The Source Folder, allow you to whitelist the folders you want to be sure to check . Ex: src
The rest are the checkers, and a enable: true meaning will run in each process.

(TODO: In future versions will allow to pass special parameters)

badMessage: Its the message to be shown in case an error has been raise. (if not appear a very offensive one - intended,
you can change it)
goodMessage: In case its not set up will appear an Ok message.
Please configure this messages as you want. Errors will appear in red in the console.
     
    