#/bin/bash

GCP_PROJECT=$(gcloud info --format='value(config.project)')

curl -X POST \
    -T cluster.json \
    -H "Authorization: Bearer $(gcloud config config-helper --format='value(credential.access_token)')" \
    https://container.googleapis.com/v1/projects/$GCP_PROJECT/zones/us-central1-a/clusters
