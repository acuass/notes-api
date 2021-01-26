# Welcome 

Note API crud assessment.

## Installation

Step 1.

Run composer install

```bash
composer install
```

Step 2.

Create a file with the name .env.local at the root of the project with the proper DB settings.
Can checkout the .env demo file.

Step 3.

Load fixtures. 

```bash
symfony console doctrine:fixtures:load
```

The users created with fixtures are:

jt@jt.com // test1

j@j.com // test2


## Usage

The next cURL commands are examples to be executed
 
 POST
```bash
curl --user jt@jt.com:test1 -d '{ "title":"Title 1", "note":"Description note 1"}' -H "Content-Type: applicationson" --url "http://127.0.0.1:8000/notes"
```

 GET all
```bash
curl --user jt@jt.com:test1 -H "Accept: application/json" http://127.0.0.1:8000/notes
```

 GET one
```bash
curl --user jt@jt.com:test1 -H "Accept: application/json" http://127.0.0.1:8000/notes/12
```

 PUT
```bash
curl --user jt@jt.com:test1 -d '{ "title":"New Title 18", "note":"New Description note 18"}' -H "Content-Type: applicationson" -X PUT --url "http://127.0.0.1:8000/notes/18"
```

 DELETE
```bash
curl --user jt@jt.com:test1 -X DELETE  http://127.0.0.1:8000/notes/19
```

