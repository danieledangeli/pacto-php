
Plase Note, this version is in Beta!
# Pacto-php
A PHPUnit extension to load and test Pacto contracts

Pacto-php is a tool to validate Pact contracts.

If you want to know more about Pact and Contract Testing please read more [here](https://github.com/realestate-com-au/pact).

##How it works

Pacto-php takes a list of "Pact" contract and translate them into *psr7* requests/response object called Pacto.
Given a Pacto, you can take the *psr7* request to make a real call against your service provider (written in PHP)

Given the server response, you have to translate it into a *psr7* Response object, and then you can simply verify if the pact
has been honored.

##Example usage

[Silex Php Pacto](https://github.com/danieledangeli/silex-php-pacto)

