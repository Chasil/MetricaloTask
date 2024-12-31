To run API request you can use Postman with following curl:

curl -X POST http://your-domain.com/app/example/{system} \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer your_token_here" \
     -d '{
           "amount": "100",
           "currency": "EUR",
           "card_number": "1234567890123456",
           "card_exp_year": "2025",
           "card_exp_month": "12",
           "card_cvv": "123"
         }'
Where [system} is currently one of 2 available: aci or shift4.
For connectrion geenrate APi Token.

To use CLI Command: 

php bin/console app:example aci 100 EUR 4111111111111111 2025 12 123 

To run integration test:

php ./vendor/bin/phpunit 
