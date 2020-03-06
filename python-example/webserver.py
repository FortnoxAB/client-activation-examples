import os

import requests
from flask import Flask, request

APP_DEBUG = True

app = Flask(__name__)

# Loading configuration from environment variables
fortnox_url = os.getenv('FORTNOX_URL', 'https://api.fortnox.se/3/')
client_secret = os.getenv('CLIENT_SECRET', None)

if client_secret is None:
    raise Exception("No client secret provided!")

# Defining a route that can process the authorization code
@app.route("/authorization", methods=['GET'])
def index():
    # The authorization-code will be sent as a query parameter
    authorization_code = request.args.get('authorization-code')

    if authorization_code is None:
        return 'No authorization code was provided!'

    try:
        access_token = get_access_token(authorization_code)
    except Exception as e:
        return 'Failed to activate your company! Error: ' + str(e)

    try:
        company_info = get_company_info(access_token)
    except Exception as e:
        return 'Failed to activate your company! Error: ' + str(e)

    # Company info can be used to validate if the tenant is a customer
    customerOrganizationNumbers = [
        '5555555555'
    ]

    if company_info.get('OrganizationNumber') not in customerOrganizationNumbers:
        return 'You are not a customer yet. Click <a href=#>here</a> to become a customer.'

    # Tell the customer that the activation was a success!
    return company_info.get('Name') + ' has now been activated! Thank you for using our integration!'


def get_access_token(authorization_code):
    # Retrieve an access token by sending authorization-code and client-secret
    result = requests.get(fortnox_url, headers={
        'authorization-code': authorization_code,
        'client-secret': client_secret
    })

    check_for_errors(result)

    # Get the access token from the json body result
    access_token = result.json().get('Authorization').get('AccessToken')

    return access_token


def get_company_info(access_token):
    # Use the access token to retrieve information, combined url will be https://api.fortnox.se/3/settings/company
    result = requests.get(fortnox_url + 'settings/company', headers={
        'access-token': access_token,
        'client-secret': client_secret
    })

    check_for_errors(result)
    return result.json().get('CompanySettings')


def check_for_errors(result):
    if result.json().get('ErrorInformation') is not None:
        raise Exception(result.json().get('ErrorInformation'))


if __name__ == "__main__":
    app.run(ssl_context='adhoc', host='0.0.0.0', threaded=True, port=9999, debug=APP_DEBUG)
