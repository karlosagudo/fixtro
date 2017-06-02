# Events
You can config a callable listening to the mains events in fixtro, just by adding in the config file:
This way you can call your own classes with the info processed by the runners or the config.

The best way to see what events are being launched, and what event listeners has been configured its to use the verbose mode: fixtro entire -vvv

## General config file: fixtro.yml
You can for example put in place a listener everytime the config is being loaded:

    events:
        config.post_load : YourProjectNamespace\TestingEvenListener
  
So, your local (in your own project), class YourProjectNamespace\TestingEventListener
will be called.
It is mandatory to be a php callable.
This class should receive a FixtroEvent Class.

## Fixtro Event Class
Its a simple class with this methods:
 - when: Will show the datetime when this event happened
 - getInfo: get the info messages
 - getErrors: get the error messages
 - setStopSignal: If its set to true, fixtro will launch an exception and stop at this very moment.
 - setPassSignal: If its set to true, will not execute the checker. (THIS IS FOR CHECKERS-FIXERS ONLY)

## How to use this listeners:
Example, in case you want to stop fixtro:

    class TestingEvenListener
    {
        public function __invoke(FixtroEvent $event)
        {
            if ($this->conditionToStop($event->getInfo()) {
                $event->setStopSignal(true);
            }
        }
    }
    
## List of events and data parsed

|Event Name | Info | Error |
|---------------------|---------------------|---------------------|
|config.post_load | config | [] |
|files.loaded| files (absolute paths) | [] |
|analyzer.{analyzerNameInLower}.files| files after applied the filter (absolute Paths) | []
|analyzer.{analyzerNameInLower}.pre| config | []
|analyzer.{analyzerNameInLower}.after| Info | Errors

Examples of analyzerNameInLower: 
- analyzer.composerchecker.files
- analyzer.namespacefixer.files
- analyzer.phplintchecker.files


## Example - Stop in case a file has to be tested
If we listen to the files.loaded , and we want to stop in case someone has commited changes to a file we can create a
listener like this one in the config file:

    events:
      files.loaded: YourProjectNamespace\StopIfFileFoundListener
      
The code for this StopIfFileFoundListener can be as simple as this one:

        public function __invoke(FixtroEvent $event)
        {
            $files = $event->getInfo();
            foreach ($files as $file) {
                if (strpos($file, self::STOP_IF_CHANGES_ON_THIS_FILE) !== false) {
                    $event->setStopSignal();
                    break;
                }
            }
        }
        
Where we put in the const STOP_IF_CHANGES_ON_THIS_FILE the value of the file we dont want to change.
              
## Example - Avoid to execute a test, no matter what it is configured
In this case we will listen to the pre analyzer:
analyzer.{analyzerNameInLower}.pre

    events:
      analyzer.phplintchecker.pre: YourProjectNamespace\PassPrePhpLintListener

And the class can be as simple as:

    public function __invoke(FixtroEvent $event)
    {
        $event->setPassSignal();
    }      

