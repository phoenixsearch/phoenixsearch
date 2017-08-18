# pheonixsearch
PhoenixSearch is a fast and modern full-text real-time search engine based on Redis + PHP7

![alt PHP logo](https://github.com/phoenixsearch/pheonixsearch/blob/master/tests/images/php.png)
![alt Redis logo](https://github.com/phoenixsearch/pheonixsearch/blob/master/tests/images/redis.png)

Installation via composer
```sh
composer create-project phoenixsearch/pheonixsearch path/
```

Index document.

Request:
```json
PUT http://pheonixsearch.loc/myindex/myindextype?pretty
```

```json
{
  "title": "Lorem ipsum is a pseudo-Latin text",
  "text": "Lorem ipsum is a pseudo-Latin text used in web design, typography, layout, and printing in place of English to emphasise design elements over content. It's also called placeholder (or filler) text. It's a convenient tool for mock-ups. It helps to outline the visual elements of a document or presentation, eg typography, font, or layout. Lorem ipsum is mostly a part of a Latin text by the classical author and philosopher Cicero. Its words and letters have been changed by addition or removal, so to deliberately render its content nonsensical; it's not genuine, correct, or comprehensible Latin anymore. While lorem ipsum's still resembles classical Latin, it actually has no meaning whatsoever. As Cicero's text doesn't contain the letters K, W, or Z, alien to latin, these, and others are often inserted randomly to mimic the typographic appearence of European languages, as are digraphs not to be found in the original."
}
```

Response:
```json
{
    "created": true,
    "took": 76,
    "_index": "myindex",
    "_type": "myindextype",
    "_id": 1,
    "result": "created",
    "_version": 1
}
```

Update document with same content (idempotent operation).

Request:
```json
PUT http://pheonixsearch.loc/myindex/myindextype?pretty
```

```json
{
  "title": "Lorem ipsum is a pseudo-Latin text",
  "text": "Lorem ipsum is a pseudo-Latin text used in web design, typography, layout, and printing in place of English to emphasise design elements over content. It's also called placeholder (or filler) text. It's a convenient tool for mock-ups. It helps to outline the visual elements of a document or presentation, eg typography, font, or layout. Lorem ipsum is mostly a part of a Latin text by the classical author and philosopher Cicero. Its words and letters have been changed by addition or removal, so to deliberately render its content nonsensical; it's not genuine, correct, or comprehensible Latin anymore. While lorem ipsum's still resembles classical Latin, it actually has no meaning whatsoever. As Cicero's text doesn't contain the letters K, W, or Z, alien to latin, these, and others are often inserted randomly to mimic the typographic appearence of European languages, as are digraphs not to be found in the original."
}
```

Response:
```json
{
    "created": false,
    "took": 1,
    "_index": "myindex",
    "_type": "myindextype",
    "_id": 1,
    "result": "updated",
    "_version": 2
}
```

Search documents.

Request:
```json
GET http://pheonixsearch.loc/myindex/myindextype?pretty
```

```json
{   
    "query" : {
        "term" : { "title" : "Lorem ipsum" }
    }
}
```

Response:
```json
{
    "took": 1,
    "timed_out": false,
    "hits": {
        "total": 2,
        "hits": [
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 1,
                "_timestamp": 1502997604,
                "_source": {
                    "title": "Lorem ipsum is a pseudo-Latin text",
                    "text": "Lorem ipsum is a pseudo-Latin text used in web design, typography, layout, and printing in place of English to emphasise design elements over content. It's also called placeholder (or filler) text. It's a convenient tool for mock-ups. It helps to outline the visual elements of a document or presentation, eg typography, font, or layout. Lorem ipsum is mostly a part of a Latin text by the classical author and philosopher Cicero. Its words and letters have been changed by addition or removal, so to deliberately render its content nonsensical; it's not genuine, correct, or comprehensible Latin anymore. While lorem ipsum's still resembles classical Latin, it actually has no meaning whatsoever. As Cicero's text doesn't contain the letters K, W, or Z, alien to latin, these, and others are often inserted randomly to mimic the typographic appearence of European languages, as are digraphs not to be found in the original."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 2,
                "_timestamp": 1502997883,
                "_source": {
                    "title": "Lorem ipsum is a pseudo-Latin text used in web design",
                    "text": "Lorem ipsum is a pseudo-Latin text used in web design, typography, layout, and printing in place of English to emphasise design elements over content. It's also called placeholder (or filler) text. It's a convenient tool for mock-ups. It helps to outline the visual elements of a document or presentation, eg typography, font, or layout. Lorem ipsum is mostly a part of a Latin text by the classical author and philosopher Cicero. Its words and letters have been changed by addition or removal, so to deliberately render its content nonsensical; it's not genuine, correct, or comprehensible Latin anymore. While lorem ipsum's still resembles classical Latin, it actually has no meaning whatsoever. As Cicero's text doesn't contain the letters K, W, or Z, alien to latin, these, and others are often inserted randomly to mimic the typographic appearence of European languages, as are digraphs not to be found in the original."
                }
            }
        ]
    }
}
```

Search with offset/limit.

Request:

```json
GET http://pheonixsearch.loc/myindex/myindextype?pretty
```

```json
{   
  "offset":0, 
  "limit":15, 
    "query" : {
        "term" : { "title" : "Lorem ipsum" }
    }
}
```

Response:
```json
{
    "took": 11,
    "timed_out": false,
    "hits": {
        "total": 3,
        "hits": [
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 1415,
                "_timestamp": 1502998633,
                "_source": {
                    "title": "Prof.",
                    "text": "Iste velit aperiam ut sunt ut. Enim architecto velit quis enim asperiores nisi mollitia recusandae. Ullam harum vitae dicta."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 1026,
                "_timestamp": 1502998620,
                "_source": {
                    "title": "Miss",
                    "text": "Hic magnam deserunt numquam ut vero qui reiciendis. Odio nemo repellendus hic est doloribus delectus. Dicta quis enim et voluptatem."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 191,
                "_timestamp": 1502998465,
                "_source": {
                    "title": "Ms.",
                    "text": "Rerum maxime possimus unde expedita rerum. Inventore quia quis enim in non. Necessitatibus reprehenderit facere qui quia."
                }
            }
        ]
    }
}
```

Delete document.

```json
DELETE http://pheonixsearch.loc/myindex/myindextype/2?pretty
```

For existing document.
```json
{
    "found": true,
    "took": 6,
    "_index": "myindex",
    "_type": "myindextype",
    "_id": 2,
    "result": "deleted",
    "_version": 1
}
```

For non-existent document.
```json
{
    "found": false,
    "took": 1,
    "_index": "myindex",
    "_type": "myindextype",
    "_id": 2,
    "result": "not found",
    "_version": 1
}
```

Get indices info:

```json
GET http://pheonixsearch.loc/_cat/indices
```

```json
[
    {
        "_index": "myanotherindex",
        "docs_count": 2,
        "docs_deleted": 0
    },
    {
        "_index": "myindex",
        "docs_count": 1955,
        "docs_deleted": 1
    }
]
```