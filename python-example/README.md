# Fortnox integration example (written in Python)

## Purpose
An example of how to fetch company information of a company by using authorization codes and access tokens.

The integration runs a webserver with the endpoint ```/authorization?authorization-code={authorization-code}``` that is used to activate customers for your client/integration.

## Requirements
Install the requirements with pip. https://pip.pypa.io/en/stable/installing/

* To install globally, make sure you are in the project folder. Then use: ```pip install -r requirements.txt``` to install the requirements globally.
#
If you prefer to not install the dependencies globally use a virtual environment instead.
* Create the virtual environment: ```python3 -m venv env``` (The following command requires python3-venv to be installed)
* Activate the virtual environment: ```source env/bin/activate``` (https://virtualenv.pypa.io/en/stable/userguide/#activate-script)
* Install the requirements in the virtual environment: ```pip install -r requirements.txt```
* Notice that the virtual environment must be activated before starting the webserver in this case.
* To leave the virtual environment use: ```deactivate```

## Running
The webserver can be started with the following command:
`CLIENT_SECRET={client-secret} python webserver.py` (A client secret must be provided to run the webserver)

Then navigate to:
`https://localhost:9999/authorization`