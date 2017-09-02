# PhoenixSearch
PhoenixSearch is a fast and modern full-text real-time search engine based on Redis + PHP7

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phoenixsearch/phoenixsearch/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phoenixsearch/phoenixsearch/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/phoenixsearch/phoenixsearch/badges/build.png?b=master)](https://scrutinizer-ci.com/g/phoenixsearch/phoenixsearch/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/phoenixsearch/phoenixsearch/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phoenixsearch/phoenixsearch/?branch=master)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

![alt Redis logo](https://github.com/phoenixsearch/phoenixsearch/blob/master/tests/images/redis.png)
![alt PHP logo](https://github.com/phoenixsearch/phoenixsearch/blob/master/tests/images/php.png)

* [Installation](#user-content-installation-via-composer)
* [Index document](#user-content-index-document)
* [Search documents](#user-content-search-documents)
    * [Search with offset/limit](#user-content-search-with-offset/limit)
    * [Search with highlighted query](#user-content-search-with-highlighted-query)    
* [Delete document](#user-content-delete-document)
* [Delete index](#user-content-delete-index)
* [Reindex](#user-content-reindex)
* [Getting indices info](#user-content-getting-indices-info)
* [Getting detailed index info](#user-content-getting-detailed-index-info) 

### Installation via composer
```sh
composer create-project phoenixsearch/phoenixsearch yourprojectpath/
```

then cd to yourprojectpath/ and run:
```sh
php phoenixsearchd.php <key>
```
the key is in Your `.env` file.

PS you need `phoenixsearchd` to execute long running complicated tasks ex.: 
delete an entire index, reindex data to another index/indexType etc  

It is possible to identify the process by it's title: 
```sh
ps aux | grep phoenixsearch
```

### Index document

Index a new document into the storage and increments `docs_count` in index info.

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
    "took": 23,
    "_index": "myindex",
    "_type": "myindextype",
    "_id": 2,
    "result": "created",
    "_version": 1
}
```

#### Update document with same content (idempotent operation).

If an update with the same content occurred, then this document will be found and `_version` 
property will be updated to `i++`.  

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

Whether you need to search by word or phrase just add `query->term` into json body.

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

By offsetting the search request (like in sql) you saying to engine - start collecting documents from J, 
for amount of K if limit was set. 

Request:

```json
GET http://pheonixsearch.loc/myindex/myindextype?pretty
```

```json
{   
    "offset":10, 
    "limit":5, 
    "query" : {
        "term" : { "text" : "quis" }
    }
}
```

Response:
```json
{
    "took": 11,
    "timed_out": false,
    "hits": {
        "total": 5,
        "hits": [
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 695,
                "_timestamp": 1503231848,
                "_source": {
                    "title": "Ms.",
                    "text": "Et aut et dolor assumenda ea. Iste corrupti quis quis voluptas similique quos tenetur. Et nisi dolore quod quidem architecto qui."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 2027,
                "_timestamp": 1503231889,
                "_source": {
                    "title": "Dr.",
                    "text": "Quae ut ad omnis est. Impedit reiciendis illo aut magnam fugit. Sed ratione illum quibusdam illum et dolores quis quia."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 4506,
                "_timestamp": 1503232042,
                "_source": {
                    "title": "Dr.",
                    "text": "Necessitatibus quod est commodi accusamus. Occaecati quis nam veritatis quia. Dicta a non ex non repellendus sed ipsa. Molestiae aliquam quia dolor porro laboriosam corporis consequatur."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 4568,
                "_timestamp": 1503232046,
                "_source": {
                    "title": "Dr.",
                    "text": "Magnam quis nihil aliquid nihil enim. Ad id odio tenetur aut. Nihil ea iusto aliquam ut."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": 7398,
                "_timestamp": 1503232264,
                "_source": {
                    "title": "Mrs.",
                    "text": "Non adipisci sunt quisquam sint ullam qui sed. Ut voluptate eum quia quia. Nihil blanditiis eos quis fuga unde reprehenderit veritatis voluptatem. Dolorum neque temporibus vel reiciendis voluptatem."
                }
            }
        ]
    }
}
```

#### Search with highlighted query

When you need to highlight words, phrases etc, it is simple enough to do by adding `highlight` property into json scheme.    

```json
{   
    "offset":5, 
    "limit":5, 
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
    "took": 46,
    "timed_out": false,
    "hits": {
        "total": 5,
        "hits": [
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "7483",
                "_timestamp": "1503232272",
                "_source": {
                    "title": "Dr.",
                    "text": "Pariatur aut consequatur cumque dolores. Hic quis tempora quia error suscipit <tag1><tag2>quis enim</tag1></tag2> omnis. Et ut aperiam voluptatum officia rem vitae quod. Cupiditate qui et commodi est quod."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "3973",
                "_timestamp": "1503232006",
                "_source": {
                    "title": "Prof.",
                    "text": "Corporis provident tempore omnis voluptatem voluptates distinctio aliquam voluptatem. Non quis <tag1><tag2>quis enim</tag1></tag2> nulla aliquid quidem eligendi. Rerum et mollitia consequuntur consequatur."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "3276",
                "_timestamp": "1503231961",
                "_source": {
                    "title": "Miss",
                    "text": "Distinctio voluptatem autem exercitationem quo cumque. Labore omnis sapiente qui itaque. Sunt iusto et porro id <tag1><tag2>quis enim</tag1></tag2> corrupti. Quaerat id doloribus est adipisci et debitis voluptas."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "3827",
                "_timestamp": "1503231997",
                "_source": {
                    "title": "Dr.",
                    "text": "Iure est culpa vitae blanditiis explicabo voluptatem aliquam. Nostrum ullam quo ipsum reprehenderit magni officiis dolor. Quo <tag1><tag2>quis enim</tag1></tag2> facilis quidem facilis quaerat."
                }
            },
            {
                "_index": "myindex",
                "_type": "myindextype",
                "_id": "9524",
                "_timestamp": "1503232463",
                "_source": {
                    "title": "Miss",
                    "text": "Nam dolorem et laboriosam <tag1><tag2>quis enim</tag1></tag2> voluptas. Rerum vel nihil delectus fugit qui. Tempore quis commodi error provident aperiam esse. Dolorum nulla ipsa molestias veritatis dolorem sed distinctio."
                }
            }
        ]
    }
}
```

### Delete document

Deletes one document by it's id, decreasing counter in index info by 1.

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

### Delete index

Deletes an entire index data from storage.

```json
DELETE http://pheonixsearch.loc/myindex/myindextype
```

Response:
```json
{
    "acknowledged": true
}
```

The message `"acknowledged": true` means the job is processed under the daemon `phoenixsearchd`.

### Reindex 

Copies documents from one index to another with mappings of source index by default.

```json
POST http://pheonixsearch.loc/_reindex
```

```json
{
  "source": {
    "index": "myindex",
    "index_type":"myindextype"
  },
  "dest": {
    "index": "myanotherindex",
    "index_type":"myanothertype"
  }
}
```

Response:
```json
{
    "acknowledged": true
}
```

### Getting indices info

This request will output general information about all indices, that has been stored yet. 

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
The `store_size` is the size of storage for all indices got from Redis.

### Getting detailed index info

```json
GET http://pheonixsearch.loc/myindex
```

```json
{
    "myindex": {
        "aliases": [],
        "mappings": {
            "myindextype": {
                "properties": {
                    "title": {
                        "type": "text",
                        "fields": {
                            "whitespace": {
                                "type": "whitespace",
                                "ignore_above": 0
                            }
                        }
                    },
                    "text": {
                        "type": "text",
                        "fields": {
                            "whitespace": {
                                "type": "whitespace",
                                "ignore_above": 0
                            }
                        }
                    },
                    "data": {
                        "type": "text",
                        "fields": {
                            "whitespace": {
                                "type": "whitespace",
                                "ignore_above": 0
                            }
                        }
                    }
                }
            }
        }
    }
}
```

`"ignore_above": 0` means no restriction on string(text) length is applied, `whitespace` type is the default type 
of inverted index analyzer which just breaks text by whitespace tokens.

### Performance

#### Full-text search with offset/limit + highlighting (by phrase)

Request:
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

Response:
```json
{
    "took": 57,
    "timed_out": false,
    "hits": {
        "total": 10 ...
```

#### Full-text search with offset/limit (by phrase)

Request:
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
    "took": 34,
    "timed_out": false,
    "hits": {
        "total": 10 ...
```

#### Full-text search without restriction for 1 000 000 documents by phrase

Request:
```json
{   
    "query" : {
        "term" : { "text" : "quis enim" }
    }
}
```

Response:
```json
{
    "took": 72,
    "timed_out": false,
    "hits": {
        "total": 229 ...
```

#### Full-text search without restriction for 1 000 000 documents by word

Request:
```json
{   
    "query" : {
        "term" : { "text" : "quis" }
    }
}
```

Response:
```json
{
    "took": 49,
    "timed_out": false,
    "hits": {
        "total": 2450 ...
```

As You can see selection is blazingly fast, no magic - just Redis with native C as core.

#### Put a document into the index with type

Request:
```json
{
  "title": "Lorem ipsum is a pseudo-Latin text used in web design",
  "text": "Lorem ipsum is a pseudo-Latin text used in web design, typography, layout, and printing in place of English to emphasise design elements over content. It's also called placeholder (or filler) text. It's a convenient tool for mock-ups. It helps to outline the visual elements of a document or presentation, eg typography, font, or layout. Lorem ipsum is mostly a part of a Latin text by the classical author and philosopher Cicero. Its words and letters have been changed by addition or removal, so to deliberately render its content nonsensical; it's not genuine, correct, or comprehensible Latin anymore. While lorem ipsum's still resembles classical Latin, it actually has no meaning whatsoever. As Cicero's text doesn't contain the letters K, W, or Z, alien to latin, these, and others are often inserted randomly to mimic the typographic appearence of European languages, as are digraphs not to be found in the original.",
  "data": "2017-08-21"
}
```

Response:
```json
{
    "created": true,
    "took": 66 ...
}
```

#### Deleting document 

Request:
```json
http://pheonixsearch.loc/myindex/myindextype/44972?pretty
```

Response:
```json
{
    "found": true,
    "took": 33 ...
```

### Notes

- `took` time measured in milliseconds
