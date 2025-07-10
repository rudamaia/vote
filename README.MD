

# Voting API Drupal

This repository contains the **Voting** custom Drupal module along with full site configuration to manage poll questions and votes via JSON:API.

## Requirements
- PHP 8.3

## Installation

Follow these steps to install and run the project locally:

1. **Clone the repository**  
   ```bash
   git clone <repository-url> vote
   cd vote
   ```

2. **Install PHP dependencies**  
   ```bash
   composer install
   ```

3. **Set up Drupal settings and install the site**  
   - If you dont want to use Drush, you can always manually set `settings.php`:
     ```bash
     cp web/sites/default/default.settings.php web/sites/default/settings.php
     chmod 664 web/sites/default/settings.php
     ```  
   - **Install via Drush** (recommended):
     ```bash
     drush site:install standard \
       --db-url="mysql://username:password@127.0.0.1/vote" \
       --site-name="Vote" -y
     ``` 

## Configuration Management

- Active configuration is stored in `config/sync`.  
- To import configuration:
  ```bash
  drush config-import -y
  ```

## JSON:API Endpoints

- **List Poll Questions**  
  `GET /jsonapi/poll_question/poll_question`

- **Create Poll Question**  
  ```bash
  curl -X POST http://<your-host>/jsonapi/poll_question/poll_question \
    -H "Accept: application/vnd.api+json" \
    -H "Content-Type: application/vnd.api+json" \
    -d '{
      "data": {
        "type": "poll_question--poll_question",
        "attributes": {
          "title": "What is you favorite color?",
          "show_results": true,
          "options": [
            {
              "title": "red",
              "description": "",
              "image": null
            },
            {
              "title": "green",
              "description": "",
              "image": null
            }
          ]
        }
      }
    }'
  ```

- **List Votes**  
  `GET /jsonapi/poll_vote/poll_vote`

- **Create Vote**  
  ```bash
  curl -X POST http://<your-host>/jsonapi/poll_vote/poll_vote \
    -H "Accept: application/vnd.api+json" \
    -H "Content-Type: application/vnd.api+json" \
    -d '{
      "data": {
        "type": "poll_vote--poll_vote",
        "attributes": {
          "option_uuid": "<OPTION_UUID>"
        },
        "relationships": {
          "question_id": {
            "data": {
              "type": "poll_question--poll_question",
              "id": "<QUESTION_UUID>"
            }
          }
        }
      }
    }'
  ```

## Example Requests (HAR)

A Postman/Insomnia HAR collection is provided at `vote-requests.har` in the project root. You can import it into your API client to test all available endpoints.  
**Make sure to update the host domain** (e.g. `http://vote.com.loc`) and replace the `<QUESTION_UUID>` and `<OPTION_UUID>` placeholders with the actual UUIDs from your site before sending requests.

## Database Dump

A SQL dump of the site database is provided at the root as `dump.sql.zip` for easier setup if needed.