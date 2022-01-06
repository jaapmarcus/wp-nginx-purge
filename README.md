# WP-NGINX-PURGE

Purge Wordpress with ngx_http_cache_purge module for Ngix 

## Setup 

- Make sure you have ngx_http_cache_purge enabled for your nginx configuration
- For Proxy cache add proxy_cache_purge PURGE from all;  to the same block as proxy_cache {key}
- Activate plugin