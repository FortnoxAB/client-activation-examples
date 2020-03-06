# Fortnox integration example (written in PHP)

## Purpose
An example of how to fetch company information of a company by using authorization codes and access tokens

The integration runs a webserver with the endpoint ```/authorization?authorization-code={authorization-code}``` that is used to activate customers for your client/integration.
## Running

In the project folder, execute:
`php -S localhost:9999`
Then navigate to:
`http://localhost:9999/authorization.php`

