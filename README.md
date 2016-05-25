# Pacto-php
A PHPUnit extension to load and test Pacto contracts

Pacto-php is a tool to validate Pact contracts.

If you want to know more about Pact and Contract Testing please read more [here](https://github.com/realestate-com-au/pact).

##How it works

Pacto-php takes a list of "Pact" contract and translate them into *psr7* requests/response object called Pacto.
Given a Pacto, you can take the *psr7* request to make a real call against your service provider (written in PHP)

Given the server response, you have to translate it into a *psr7* Response object, and then you can simply verify if the pact
has been honored.

```php
class MyIntegrationTestCase extends PactoIntegrationTest 
{
   public function setUp() 
   {
      $this->loadContracts('my contract list folder');
   }
   public function testAPIServiceProviderGetUser() 
   {
       $pacts = $this->getPactsByDescription('My service provider name', 'My pacto unique description for get user');
       
       if(count($pacts) <= 0) {
          $this->fail('no pacts found');
       }
       
       $pact = $pacts[0];
       
       $response = $this->makeRealServerRequest($pact->getRequest()); //write the function to make a real request
       $psr7Response = $this->translateToPsr7($response); //write the function to transalte into a Psr7 Reponse if needed
       
       $this->assertResponse($pact->getResponse(), $$psr7Response, false); //use strict true to compare the body and headers contents
   }
}
```



