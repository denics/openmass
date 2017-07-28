# Mass.gov Oauth Authentication

Our content metadata API is secured with Oauth 2.0. To access this API you need to first generate an access token, then use that token to make requests against the API.

## Getting an access token
First, you need to have a drupal account with a developer developer role. Ensure you have this.
Next, you must authenticate against an oauth client that has already been set up within Drupal. Find your client information here: `/admin/config/people/simple_oauth/oauth2_client/`

Make a POST request to `/oath/token`. The POST body should be encoded as form-data and look like the following:
```json
  "grant_type" : "password"
  "client_id" : <client uuid from the oauth client>
  "client_secret" : <secret from oauth client>
  "username" : <username of your Drupal account (developer role)>
  "password" : <password of your Drupal account (developer role)>
```

You should get a valid response from this POST request containing an access token and refresh token. Take note of your access token & use that in subsequent requests to API endpoints protected by oauth.

## Making authenticated requests
To make an authenticated request, you must have a HTTP Header of 'Authorization' with the value of 'Bearer <your access token>'. Note a space between 'Bearer' and the token.
