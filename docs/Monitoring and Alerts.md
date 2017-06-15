# Mass.gov Monitoring and Alerts

Because Mass.gov is hosted on Acquia Cloud, there are some fixed ways that we can monitor site performance. New Relic is one of the available ways to monitor the site and errors.

(Note: Acquia has some performance metrics and scores, but when our lower environments ran out of memory it still said our performance was amazing --- so we don't trust it.)

New Relic brands all the segments heavily, making it hard for new comers to browse around. Here's some quick descriptions:

- APM: Server-side application monitoring including errors and performance metrics
- Browser: Monitors performance in users' browsers
- Synthetics: Let you create "scripts" that hit the site in certain ways. Great for downtime monitoring.
- Mobile: Not relevant to us. Used for monitoring mobile application performance.
- Plugins: New Relibc plugins.
- Insights: Essentially SQL for everything New Relic creates. If you want to dig super deep into anything being monitored, this is the place.
- Infrastructure: Not relevant to us. Monitors infrastructure itself.
- Servers: No clue.
- Alerts: Lets you setup custom notifications. New Relic is in transition between their old platform and new one. The new one is at alerts.newrelic.com .

## Monitoring

### Available Monitors

New Relic provides a few canned monitors that are useful for debugging.

For each monitor, you can tweak the application, time range, and specific server.

Here are some links:

- [Errors](https://rpm.newrelic.com/accounts/1522041/applications/46704875/filterable_errors#/table?top_facet=transactionUiName&barchart=barchart&_k=8stx70): Shows errors that appear in the logs. Ideally, we want there to be nothing in here, so anything that appears might warrant investigation.
- [Drupal Modules](https://rpm.newrelic.com/accounts/1522041/applications/46704875/drupal/modules): Shows performances metrics per Drupal module
- [Drupal Views](https://rpm.newrelic.com/accounts/1522041/applications/46704875/drupal/views): Performance metrics per view on the site such as response times and most requested
- [Database](https://rpm.newrelic.com/accounts/1522041/applications/46704875/datastores#/overview/MySQL?value=total_call_time_per_minute): Shows metrics for different CRUD operations, but also what is used most often. Could be used for tuning or seeing if queries are taking a really long time.
- [External]: Not super useful right now, but when/if Mass.gov connects to other external dependencies, this will tell us more about their availablility and if we should build "circuit breakers" between them and us. Most useful thing today: is Acquia itself up (see `nspi.acquia.com`)?
- [Default Dashboard](https://rpm.newrelic.com/accounts/1522041/applications/46704875): More of an interesting thing than something super useful for debugging (the above stuff is more useful). Default dashboard shows response times by layer of the application (i.e. PHP vs. DB vs. Web)


## Alerts

It's pretty easy to setup alerts based on things within [New Relic Alerts](https://alerts.newrelic.com).

Some example alerts that can setup:

- Ping Tests: Is a URL up? Great for checking if parts of your site is up and/or if external web services are available.
- Performance: Uses the Apdex score, which measures performance across a few metrics based on defined user tolerance metrics. Alerts when "user satisfaction" drops below a certain percentage.
- Error rate: Sends an alert when a defined percentage of requests result in errors over a set time period.

We've configured New Relic to send notifications by broadcast via Slack message to #massgov-alerts and also by e-mail to the Tech Team.

Here are instructions on setting up some of these alerts:

### Ping Test

1. Go to [New Relic Synthetics](https://synthetics.newrelic.com)
1. Click `Add New`
1. Choose `Ping` as monitor type
1. Name the monitor
1. Add the URL
1. Add some text that you would expect to appear in the response that indicates the request is successful
1. Set a schedule for how often the ping should happen
1. Add to an existing alert policy (Mass.gov alert policy will funnel alerts to #massgov-alerts)
1. Click `Create my monitor`
1. If you navigate to [New Relic Alerts](https://alerts.newrelic.com), then choose `Alert policies`, you'll see your alert (and can re-name it, etc.)

To see a quick demo of this, [checkout this video](https://drive.google.com/file/d/0B08nMn1nAWOeRGlqdXhjejhYVnc/view?usp=sharing).

### Error Rates

Setup for this kind of alert is similar to performance ones. Both are created in the same area of New Relic.

1. Go to [New Relic Alerts](https://alerts.newrelic.com)
1. Click `Add a condition`
1. Choose `APM`, `Application Metric`, and `Scope to application` before clicking `Next`
1. Choose the application you want to monitor, then click `Next`
1. For Defining thresholds, change the metric from `Apdex` (default) to `Error percentage` and select percentage `above`.
1. Enter an error threshold (i.e. percent of requests that result in errors)
1. Enter a warning threshold
1. Give the alert condition a clear name
1. Click `Create condition`

To see a quick demo of this, [checkout this video](https://drive.google.com/open?id=0B08nMn1nAWOeX0ZYNUF6STdtZzA).


## Could things we could do / Known issues

- [Track differences between deployments](https://rpm.newrelic.com/accounts/1522041/applications/46704875/deployments)
- Acquia does not expose the php-errors.log. There is a feature request to have this added. See [this support ticket](https://insight.acquia.com/support/tickets/381074#animated).
