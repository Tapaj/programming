@qtype @qtype_programming
Feature: Test creating an programming question
  As a teacher
  In order to test my students
  I need to be able to create an programming question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Create an programming question with Response format set to 'HTML editor'
    When I add a "programming" question filling the form with:
      | Question name            | programming-001                      |
      | Question text            | Write an programming with 500 words. |
      | General feedback         | This is general feedback       |
      | Response format          | HTML editor                    |
    Then I should see "programming-001"

  Scenario: Create an programming question with Response format set to 'HTML editor with the file picker'
    When I add a "programming" question filling the form with:
      | Question name            | programming-002                      |
      | Question text            | Write an programming with 500 words. |
      | General feedback         | This is general feedback       |
      | Response format          | HTML editor                    |
    Then I should see "programming-002"
