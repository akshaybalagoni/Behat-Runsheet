@javascript @api
Feature: Runsheet management
  In order to manage runsheets
  As an authenticated user
  I need to be able to manage runsheets, runsheet teasers and sheduled cards

  Scenario: Create and delete a Runsheet
    Given I am logged in as a user with the "administrator" role
    When I create a Runsheet
    Then I should be able to delete it

  Scenario: Test position validation
    Given I am logged in as a user with the "administrator" role
    When I open the form to create a Runsheet
    And submit the form with duplicated positions
    Then I should see an error saying that they must be unique

  Scenario: Test position validation with three items
    Given I am logged in as a user with the "administrator" role
    When I open the form to create a Runsheet
    And submit the form with duplicated positions
    And I fix validation errors but I introduce a new element with duplicated id
    Then I should see an error saying that they must be unique

  Scenario: Test that validation does not run on deleted positions.
    Given I am logged in as a user with the "administrator" role
    When I open the form to create a Runsheet
    And I request a new position field
    And I delete the new position field
    And I fill the rest of the fields and submit
    Then I should see a success message

  Scenario: Test filtering scheduled runsheet items by runsheet
    Given I am logged in as a user with the "administrator" role
    When I create a Runsheet named "Homepage runsheet"
    And I create a Runsheet named "Videos runsheet"
    And I add a Runsheet teaser bundle named "Promo"
    And I create a "Promo" Runsheet teaser named "Promo A"
    And I create a scheduled runsheet item for item "Promo A" on runsheet "Homepage runsheet"
    And I create a scheduled runsheet item for item "Promo A" on runsheet "Videos runsheet"
    And I filter the scheduled runsheeet items by "Homepage runsheet"
    Then I should see 1 scheduled runsheet item