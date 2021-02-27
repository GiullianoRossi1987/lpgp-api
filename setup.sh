#!/usr/bin/bash

# Apache configurations
if [ ! -d  "logs" ]; then mkdir logs; touch logs/error.log; fi
if [ -a "/etc/apache2/sites-available/lpgp-api.conf" ]; then
    echo -e "Seems that there's a configurations file installed already.\nSo you don't have to install it again"
    exit 0
else
    # installation begins
    # make sure the script was started by the root user
    content=$(cat apache-content.conf)
    document_root=""
    ssl_key_location=""
    ssl_certificate_location=""
    echo -e "First of all enter the document root of the project: "; read document_root

fi
