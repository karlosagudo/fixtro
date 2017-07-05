Feature: Run a stupid test to test behat is able to run with default values

  Scenario:
      Given Behat is installed
        And There is a behat config file
       When I run behat
       Then I get results
