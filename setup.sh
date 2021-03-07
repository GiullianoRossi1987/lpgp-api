#!/usr/bin/bash

# Apache configurations
if [ ! -d  "logs" ]; then mkdir logs; touch logs/error.log; fi
if [ -a "/etc/apache2/sites-available/lpgp-api.conf" ]; then
    echo -e "Seems that there's a configurations file installed already.\nSo you don't have to install it again"
else
    # installation begins
    # make sure the script was started by the root user
    content=$(cat apache-content.conf)
    document_root=""
    ssl_key_location=""
    ssl_certificate_location=""
    echo -e "First of all enter the document root of the project: "; read document_root
    # TODO
fi

if [ -a "config/config.xml" ]; then
	echo -e "Seems you don't have to generate a configurations file too, awesome!"
else
	mkdir config && touch config/config.xml
	XML_STRC="""
<?xml version="1.0" encoding=\"UTF-8\"?>
<api_config>
	<ext_config value=\"\"/>
	<gen_logs_path value=\"\"/>
	<error_log_path value=\"\"/>
</api_config>
	"""
	echo -en $XML_STRC | tee config/config.xml
	echo "Configurations file done, put your content there now"
fi
