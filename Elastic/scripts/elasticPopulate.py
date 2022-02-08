#!/usr/bin/env python
# Licensed to Elasticsearch B.V under one or more agreements.
# Elasticsearch B.V licenses this file to you under the Apache 2.0 License.
# See the LICENSE file in the project root for more information

"""Script that downloads a public dataset and streams it to an Elasticsearch cluster"""

import json
from os.path import abspath, join, dirname
from elasticsearch import Elasticsearch
from elasticsearch.helpers import streaming_bulk



DATASET_PATH = join(dirname(abspath(__file__)), "dummy.json")
CHUNK_SIZE = 16384

def create_index(client):
    """Creates an index in Elasticsearch if one isn't already there."""
    client.indices.create(
        index="offer",
        body={
    "mappings": {
        "properties": {
            "offerID": { "type": "keyword" },
            "title": { "type": "text" },
            "description": { "type": "text" },
            "permalink": { "type": "text" },
            "thumb": { "type": "text" },
            "images": { "type": "text" },
            "ISactive": { "type": "boolean" },
            "authorID": { "type": "keyword" },
            "platformINcountryID": { "type": "keyword" },
            "offerCategory": { "type": "keyword" },
            "sourceID": { "type": "keyword" },
            "tagID": { "type": "text" },
            "tags": { "type": "text" },
            "offerRisk": { "type": "boolean" },
            "updated": { "type": "date" },
            "created": { "type": "date" },
            "author": {
                "properties": {
                    "authorID": { "type": "keyword" },
                    "authorName": { "type": "keyword" },
                    "cityName": { "type": "keyword" },
                    "stateName": { "type": "keyword" },
                    "countryName": { "type": "keyword" },
                    "countryCode": { "type": "text" },
                    "platformINcountryID": { "type": "keyword" },
                    "permalink": { "type": "text" },
                    "ISactive": { "type": "boolean" },
                    "updated": { "type": "date" },
                    "created": { "type": "date" },
                    "authorData": {
                        "properties": {
                        }
                    }
                }
            },
            "offerData": {
                "properties": {
                    "price": {
                        "properties": {
                            "key_name": {
                                "type": "text",
                                "fields": {
                                    "keyword": {
                                        "type": "keyword",
                                        "ignore_above": 256}}},"send_to_mysql": {"type": "boolean"},"value": {"type": "float"}}}}}}},
    "settings": {
        "index": {
            "number_of_replicas": 2,
            "number_of_shards": 5}}},
        ignore=400,
    )


def generate_actions():
    """Reads the file through csv.DictReader() and for each row
    yields a single document. This function is passed into the bulk()
    helper to create many documents in sequence.
    """
    with open(DATASET_PATH) as myoffer_json:

        reader = json.load(myoffer_json)
        for row in reader:
            yield row


def main():
    print("Loading dataset...")

    client = Elasticsearch(
        # Add your cluster configuration here!
    )
    print("Creating an index...")
    create_index(client)

    number_of_docs = 500
    print("Indexing documents...")
    successes = 0
    for ok, action in streaming_bulk(
        client=client, index="offer", actions=generate_actions(),
    ):
        successes += ok
    print("Indexed %d/%d documents" % (successes, number_of_docs))


if __name__ == "__main__":
    main()
