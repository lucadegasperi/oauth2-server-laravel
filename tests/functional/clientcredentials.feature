Feature: Client Credentials Authorization
  In order to gain access to an api
  As a client
  I want to be able to exchange my credentials for an access token

  Background:
    Given An authorization server exists that supports the "client_credentials" grant type

  Scenario Outline: With invalid credentials I won't get an access token
    Given I have invalid client credentials
    When I post to the "oauth/access_token" page <grant_type> <client_id> <client_secret>
    Then I should get an "invalid_client" error

    Examples:
      | grant_type           | client_id | client_secret |
      | "client_credentials" | "invalid" | "invalid"     |

  Scenario Outline: With valid client credentials I should get an access token
    Given I have valid client credentials
    When I post to the "oauth/access_token" page <grant_type> <client_id> <client_secret>
    Then I should get an access token.

    Examples:
      | grant_type           | client_id     | client_secret         |
      | "client_credentials" | "client1id"   | "client1secret"       |