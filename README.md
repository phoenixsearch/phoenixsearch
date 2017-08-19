# PhoenixSearch
PhoenixSearch is a fast and modern full-text real-time search engine based on Redis + PHP7

![alt Redis logo](https://github.com/phoenixsearch/pheonixsearch/blob/master/tests/images/redis.png)
![alt PHP logo](https://github.com/phoenixsearch/pheonixsearch/blob/master/tests/images/php.png)

* [Introduction](#user-content-introduction)
* [Installation](#user-content-installation-via-composer)
* [Index document](#user-content-index-document)
* [Search documents](#user-content-search-documents)
    * [Search with offset/limit](#user-content-search-with-offset/limit)
    * [Search with highlighted query](#user-content-search-with-highlighted-query)    
* [Delete document](#user-content-delete-document)
* [Getting indices info](#user-content-getting-indices-info) 

### Installation via composer
```sh
composer create-project phoenixsearch/phoenixsearch yourprojectpath/
```

### Index document

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
    "took": 63,
    "_index": "myindex",
    "_type": "myindextype",
    "_id": 1,
    "result": "created",
    "_version": 1
}
```

#### Update document with same content (idempotent operation).

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

### Search documents

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

#### Search with offset/limit

Request:

```json
GET http://pheonixsearch.loc/myindex/myindextype?pretty
```

```json
{   
  "offset":5, 
  "limit":10, 
    "query" : {
        "term" : { "text" : "quis enim" }
    }
}
```

Response:
```json
{
    "took": 27,
    "timed_out": false,
    "hits": {
        "total": 5,
        "hits": [
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 191,
                "_timestamp": 1502998465,
                "_source": {
                    "title": "Ms.",
                    "text": "Rerum maxime possimus unde expedita rerum. Inventore quia quis enim in non. Necessitatibus reprehenderit facere qui quia."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 4349,
                "_timestamp": 1503120647,
                "_source": {
                    "title": "Mr.",
                    "text": "Expedita dolorum quis enim nesciunt rerum repellendus consequatur. Iure voluptatem quia dicta porro doloremque. Voluptas architecto quis quos voluptatibus amet."
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
                "_id": 8694,
                "_timestamp": 1503120980,
                "_source": {
                    "title": "Mr.",
                    "text": "Sequi aut tempore quisquam labore odio libero. Et sunt quis enim. Animi necessitatibus nihil necessitatibus magni."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 5510,
                "_timestamp": 1503120726,
                "_source": {
                    "title": "Prof.",
                    "text": "Distinctio expedita enim dolor et explicabo. Saepe eligendi ullam vero adipisci sed quis enim. Quis sunt est libero dolore assumenda qui et."
                }
            }
        ]
    }
}
```

#### Search with highlighted query
```json
{   
    "offset":5, 
    "limit":10, 
    "highlight" : {
        "pre_tags" : ["<tag1>", "<tag2>"],
        "post_tags" : ["</tag1>", "</tag2>"],
        "fields" : {
            "name" : {}, "text" : {}
        }
    },
    "query" : {
        "term" : { "text" : "quis enim" }
    }
}
```

```json
{
    "took": 37,
    "timed_out": false,
    "hits": {
        "total": 5,
        "hits": [
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "191",
                "_timestamp": "1502998465",
                "_source": {
                    "title": "Ms.",
                    "text": "Rerum maxime possimus unde expedita rerum. Inventore quia <tag1><tag2>quis enim</tag1></tag2> in non. Necessitatibus reprehenderit facere qui quia."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "4349",
                "_timestamp": "1503120647",
                "_source": {
                    "title": "Mr.",
                    "text": "Expedita dolorum <tag1><tag2>quis enim</tag1></tag2> nesciunt rerum repellendus consequatur. Iure voluptatem quia dicta porro doloremque. Voluptas architecto quis quos voluptatibus amet."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "1026",
                "_timestamp": "1502998620",
                "_source": {
                    "title": "Miss",
                    "text": "Hic magnam deserunt numquam ut vero qui reiciendis. Odio nemo repellendus hic est doloribus delectus. Dicta <tag1><tag2>quis enim</tag1></tag2> et voluptatem."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "8694",
                "_timestamp": "1503120980",
                "_source": {
                    "title": "Mr.",
                    "text": "Sequi aut tempore quisquam labore odio libero. Et sunt <tag1><tag2>quis enim</tag1></tag2>. Animi necessitatibus nihil necessitatibus magni."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "5510",
                "_timestamp": "1503120726",
                "_source": {
                    "title": "Prof.",
                    "text": "Distinctio expedita enim dolor et explicabo. Saepe eligendi ullam vero adipisci sed <tag1><tag2>quis enim</tag1></tag2>. Quis sunt est libero dolore assumenda qui et."
                }
            }
        ]
    }
}
```

### Delete document

```json
DELETE http://pheonixsearch.loc/myindex/myindextype/2?pretty
```

For existing document it returns:
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

For non-existent document:
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

### Getting indices info

```json
GET http://pheonixsearch.loc/_cat/indices
```

```json
[
    {
        "store_size": "456.91M"
    },
    {
        "_index": "myanotherindex",
        "docs_count": 2,
        "docs_deleted": 0
    },
    {
        "_index": "myindex",
        "docs_count": 12687,
        "docs_deleted": 1
    }
]
```