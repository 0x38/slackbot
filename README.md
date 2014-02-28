slackbot
========

Basic bot for Slack that responds to Outgoing Webhooks.


## Set Up

Copy or rename example.config.php to config.php. Open and edit to change settings like [Forecast.io](http://forecast.io) API key.

Once signed in visit your [Outgoing Webhooks](https://my.slack.com/services/new/outgoing-webhook) page and scroll down to add a new Outgoing Webhooks integration.

The Fenix integration has channel set to "Any" and the trigger words are the names of the personalities like "Jeff:, jeff:, Ryan:, ryan:" and the URLs points to this project's respond.php.

Afterwards you can make configurations to set up a default avatar (and username if you like).

Easy!

## Making your own personality

See personalities/example.persona.php for an example. So far each new personality has to have trigger words set up manually because I'm lazy.

You can make personalities that will be ignored by git by using .pvt instead of .php as an extension.